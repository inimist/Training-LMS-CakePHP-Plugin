<?php
/**
 * Copyright 2015 - 2016, Inimist Technologies (http://inimist.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2016, Inimist Technologies (http://inimist.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Question Model
 *
 * @property Training $Question
 */

App::uses('TrainingAppModel', 'Training.Model');

class Question extends TrainingAppModel {

	public $actsAs = array('SoftDelete');
	public $displayName = 'title';

	var $name = 'Question';
	var $validate = array(
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Question title must not be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'QuestionType' => array(
			'className' => 'Training.QuestionType',
			'foreignKey' => 'question_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Course' => array(
			'className' => 'Training.Course',
			'foreignKey' => 'course_id',
			'counterCache' => true,
			'counterScope' => array(
											'Question.deleted' => 0
											),
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'QuestionAnswer' => array(
			'className' => 'Training.QuestionAnswer',
			'foreignKey' => 'question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)  ,
		'QuestionPairMatch' => array(
			'className' => 'Training.QuestionPairMatch',
			'foreignKey' => 'question_id',
			'conditions' => '',
			'fields' => '',
			'order' => 'pair_order'
		) 
	);

	function beforeFind($query) {
			$query['order'] = array_merge_recursive($query['order'], array('Question.title' => 'ASC'));
			return $query;
		}

/**
 * getRightAnswer
 *
 * @param boolean $id ; question id
 * @param array   $questions
 * @return string
 */
	function getRightAnswer($question, $questions = null)	{ //for a question id
		return $this->getAnswer($question, $questions, true);
	}

/**
 * getAnswer - get answer to a question
 *
 * @param boolean $question ; question id or a DB row with "Question" index
 * @param array   $questions
 * @return string
 */

	function getAnswer($question, $questions = null, $rightOnly = false)	{
		
		$question = $this->getQuestion($question, $questions);

		$answers = null;

		$qtype = $this->getQType ( $question );
		if($qtype == 'matching-pairs'){
				if(isset($question['QuestionPairMatch']) && sizeof($question['QuestionPairMatch']) >0 )	{
				$QuestionAnswers = $question['QuestionPairMatch'];
			}	else	{
				$options = array(
					'conditions'=>array('QuestionPairMatch.question_id'=>$question['Question']['id'])
				);
				$QuestionAnswers = $this->QuestionPairMatch->find('all', $options);
			}
			foreach($QuestionAnswers as $pair){
				$answers[$pair['pair_order']] = array($pair['question_pair_left'], $pair['question_pair_right']);
			}
			return serialize($answers);

		}else{
			if(isset($question['QuestionAnswer']) && sizeof($question['QuestionAnswer']) >0 )	{
				$QuestionAnswers = $question['QuestionAnswer'];
			}	else	{
				$options = array(
					'conditions'=>array('QuestionAnswer.question_id'=>$question['Question']['id'], 'QuestionAnswer.fraction > '=> 0)
				);
				$QuestionAnswers = $this->QuestionAnswer->find('all', $options);
			}

			foreach($QuestionAnswers as $answer)	{
				//if only to get right answers
				if( $rightOnly )	{
					if( $answer['fraction']>0 )	{ //we are checking the fraction value.. May be a better approace here??
						$answers[] = $answer['answer'];
						if( $qtype=="multiplechoice-one" ) break;
					}
				}	else	{ //get all possible answers
					 $answers[] = $answer['answer'];
				}
			}

			if( $answers )	{
				return implode(';', $answers);
			}
		}
	}
/**
 * getAnswer - get question summary, Like Question: answer1; answer2; answer3; etc.
 *
 * @param boolean $id ; question id
 * @param array   $questions
 * @return string
 */
	function getQuestionsummary($question, $questions = null)	{
		$question = $this->getQuestion($question, $questions);
		return $question['Question']['title'] .': '. $this->getAnswer($question);
	}


	function question_check_correct($question)	{
		//pr($question);   exit;
		$maxmarks = 0; 
		$qtype = $question['QuestionType']['qtype'];
		if(isset($question['QuestionAttempt']))	{
			$qattempt = $question['QuestionAttempt'];
		}	else	{
			$qattempt = $question;
		}
		switch($qtype)	{
			case 'truefalse':
			case 'multiplechoice-one';
				if($qattempt['rightanswer'] == $qattempt['responsesummary'])	{
					$maxmarks = $qattempt['maxmark'];
					return 'correct';
				}
			break;
			case 'multiplechoice-multi';
				$ralist = explode(';', $qattempt['rightanswer']); //list of correct answers
				$rslist = explode(';', $qattempt['responsesummary']); //list of answers given by user
				$ranswers = [];
				foreach($rslist as $response)	{
					if(in_array(trim($response), $ralist))	{ //It is a right answer
						array_push($ranswers, trim($response));
					}
				}
				if(sizeof($ranswers)==sizeof($ralist)) return 'correct';
			break;
		}
		return 'incorrect';
	}





/**
 * getQuestion - get question by ID or whether it is an incoming array.
 *
 * @param boolean $id ; question id
 * @param array   $questions
 * @return string
 */
	function getQuestion($question, $questions = null)	{
		
		if(is_array($question) && isset($question['Question']))	{
			$id = $question['Question']['id'];
		}	else	{
			$id = $question; $question=null;
		}

		if(!$question)	{
			if(is_array($questions))	{
				foreach($questions as $_question)	{
					if($_question['Question']['id']==$id) $question = $_question;
				}
			}	else	{
				$question = $this->find('first', array('conditions'=>array('Question.id'=>$id)));
			}
		}
		return $question;
	}

	function getQType ( $question )	{
		if( isset($question['QuestionType']) ) return $question['QuestionType']['qtype'];
		if( isset($question['qtype']) ) return $question['qtype'];
	}
	function getTruefalsAnswer($question)	{
		foreach($question['QuestionAnswer'] as $answer)	{
			if($answer['fraction']==1) return $answer['answer'];
		}
	}
	function getMultichoiceAnswer($question)	{
	}

	function hasAttempts( $id )	{
		//debug($id); exit;
		$QuestionAttempt = ClassRegistry::init('Training.QuestionAttempt');
		$count = $QuestionAttempt->find("count", array(
				"conditions" => array("question_id" => $id)
		));
		if ($count == 0) {
				return false;
		}
		return true;
	}

	/*public function beforeDelete($cascade = true)	{
		$QuestionAttempt = ClassRegistry::init('Training.QuestionAttempt');
		$count = $QuestionAttempt->find("count", array(
				"conditions" => array("question_id" => $this->id)
		));
		if ($count == 0) {
				return true;
		}
		return false;
	}*/


	public function beforePurge( $id )	{
		return true;
	}
}