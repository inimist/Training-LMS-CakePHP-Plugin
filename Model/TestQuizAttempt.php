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

App::uses('TrainingAppModel', 'Training.Model');

/**
 * QuestionAnswer Model
 *
 * @property Training $QuestionAnswer
 */

 class TestQuizAttempt extends TrainingAppModel {

		public $useTable = 'test_quiz_attempts';
		public $archived = false;
		public $resetArchived = false;
	/**
	 * The number of questions answered correct by user while calculating a quiz results
	 *
	 * @var Integar
	 */
		public $correctquestions = 0;

    /*public $belongsTo = array(
			'Course', 
			'Student' => array(
				'className' => 'User',
				'foreignKey' => 'student_id',
				'conditions' => '',
				'fields' => array('id', 'username', 'first_name', 'last_name'),
				'order' => '',
				'dependent' => true
			)
    );
	*/
	function beforeFind($options = array())	{
		parent::beforeFind($options);
		//$default = array('TestQuizAttempt.archived' => 0);
		//$options['conditions'] = array_merge($options['conditions'], $default);
		return $options;
	}

	public function setArchived($archived, $reset = false) {
  $this->archived = $archived;
  $this->resetArchived = $reset;
 }
 private function getArchived() {
  return $this->archived;
 }

	function getTestUserAttempt($quizid, $userid, $options = array(), $action = 'first')	{
		$defaults =  array('conditions'=>array(
		'quiz_id'=>$quizid,
		'user_id'=>$userid,
		'TestQuizAttempt.archived' => 0),
		'recursive'=>-1);
		$options = array_merge($defaults, $options);
		return $this->find($action, $options);
	}

	function countAttempts($quizid)	{
		return $this->find('count', array('conditions'=>array(
				'quiz_id'=>$quizid, 'state'=>'finished'),
				'recursive'=>-1)
		);
	}

	function hasAttempts($quizid)	{
		return (int) $this->countAttempts($quizid) > 0 ;
	}

/**
 * getAttempt - arrange slots from lower to higher.. Ex: from 1,3,2,5 to 1,2,3,4
 *
 * @param mixed $attempt
 * @return array of data
 */
	function getAttempt($attempt)	{ //allow ID or data row
		if(is_array($attempt) && isset($attempt['TestQuizAttempt']))	{
			$id = $attempt['TestQuizAttempt']['id'];
		}	else	{
			$id = $attempt;$attempt=null;
		}
		if(!$attempt)	{
			$attempt = $this->find('first', array('conditions'=>array('TestQuizAttempt.id'=>$id)));
		}
		return $attempt;
	}

/**
 * getTestQuizAttemptQuestions - arrange slots from lower to higher.. Ex: from 1,3,2,5 to 1,2,3,4
 *
 * @param mixed $attempt
 * @return array of data
 */
	function getTestQuizAttemptQuestions($attempt)	{
		//load quiz model
		$Quiz = ClassRegistry::init('Training.Quiz');
		$questions = $Quiz->getTestQuizQuestions($attempt['TestQuizAttempt']['quiz_id']);
		return $questions;
	}

/**
 * getAttemptSumgrades - get sumgrades by an attempt ID or data set/row
 *
 * @param mixed $attempt ; ID or data set (with TestQuizAttempt index set)
 * @return float
 */
	function getAttemptSumgrades($attempt)	{
		$attempt = $this->getAttempt($attempt);
		//debug($attempt);
		$questions = $this->getTestQuizAttemptQuestions($attempt);
		//debug($questions);
		$TestQuestionAttempt = ClassRegistry::init('Training.TestQuestionAttempt');
		$TestQuestionAttempts = $TestQuestionAttempt->find('all', array('conditions'=>array('TestQuestionAttempt.test_quiz_attempt_id'=>$attempt['TestQuizAttempt']['id'])));
		//debug($TestQuestionAttempts);
		$questions = $this->mergeQuestionsAndAttempts($questions, $TestQuestionAttempts);
		//debug($questions);
		//exit;
		//$this->calcTestQuizAttemptGradesByQuestionsDataset( $questions );
		//debug($this->correctquestions);
		//exit;
		return $this->calcTestQuizAttemptGradesByQuestionsDataset( $questions );
	}

/**
 * updateAttemptSumgrades - update sumgrades by an attempt ID or data set/row
 *
 * @param mixed $attempt ; ID or data set (with TestQuizAttempt index set)
 * @return array of data
 */
	function updateAttemptSumgrades($attempt)	{
		$attempt = $this->getAttempt($attempt);
		$this->id = $attempt['TestQuizAttempt']['id'];
		$this->saveField('sumgrades', $this->getAttemptSumgrades($attempt));
		$this->saveField('correctquestions', $this->correctquestions);
	}

/**
 * mergeQuestionsAndAttempts - merge quiz questions and attempt all together (just a useful tool)
 *
 * @param mixed $attempt
 * @return array of data
 */
	function mergeQuestionsAndAttempts($questions, $TestQuestionAttempts)	{
		//arrange them'll in a single array for easy access
		foreach($questions as $i=>$question)	{
			foreach($TestQuestionAttempts as $qa)	{
				if($qa['TestQuestionAttempt']['question_id'] == $question['Question']['id'])	{
					$questions[$i]['TestQuestionAttempt'] = $qa['TestQuestionAttempt'];
				}
			}
		}
		return $questions;
	}

/**
 * calcTestQuizAttemptGrades - calculating quiz attempt grades for a given dataset.
 *
 * @param mixed $attempt
 * @return array of data
 */
	function calcTestQuizAttemptGradesByQuestionsDataset($questions)	{
		$maxmarks = 0;
		foreach($questions as $i=>$question)	{
			$maxmarks += $this->calcQuestionGrades($question);
		}
		return $maxmarks;
	}

/**
 * calcQuestionGrades - calculate grades for a question
 *
 * @param mixed $question
 * @return array of data
 */
	function calcQuestionGrades($question)	{
	//	pr($question);  
		$maxmarks = 0;
		$qtype = $question['QuestionType']['qtype'];
		if(isset($question['TestQuestionAttempt']))	{
			$qattempt = $question['TestQuestionAttempt'];
		}	else	{
			$qattempt = $question;
		}
		switch($qtype)	{
			case 'truefalse':
			case 'multiplechoice-one';
				if($qattempt['rightanswer'] == $qattempt['responsesummary'])	{
					$maxmarks = $qattempt['maxmark'];
					$this->correctquestions++;
				}
			break;
			case 'multiplechoice-multi';
				$ralist = explode(';', $qattempt['rightanswer']); //list of correct answers
				$rslist = explode(';', $qattempt['responsesummary']); //list of answers given by user
				$ranswers = [];
				foreach($rslist as $response)	{
					if(in_array(trim($response), $ralist))	{ //It is a right answer
						array_push($ranswers, trim($response));
						$qfraction = $this->__getFractionforAnswer($response, $question['QuestionAnswer']);
						$maxmarks += $this->__getMarksforAnswer($qfraction, $qattempt['maxmark']);
					}	else	{ //now it is an incorrect answer, let's penalize user
						//debug($qfraction = $this->__getPenaltyByQuestion($response, $question['QuestionAnswer']););
						$penalty = $question['Question']['penalty'];
						$maxmarks -= $penalty * $qattempt['maxmark'];
						//debug($penalty);
					}
				}
				if(sizeof($ranswers)==sizeof($ralist)) $this->correctquestions++;
			break;
			case 'singleline':
			case 'multiplelines';
			case 'multiplelines-rich';
				$maxmarks = $qattempt['minfraction'] * $qattempt['maxmark']; //here we assume that 'minfraction' field is updated by admin user manually
			break;
			case 'matching-pairs':
				if($qattempt['rightanswer'] == $qattempt['responsesummary'])	{
					$maxmarks = $qattempt['maxmark'];
					$this->correctquestions++;
				}
			break;
		}
		//debug($question);
		//debug($maxmarks);
		return $maxmarks;
	}

/**
 * __getFractionforAnswer - get fraction of a given answer by comparing with stored answers of a question
 *
 * @param string $response
 * @param array $answers
 * @return float
 */
	function __getFractionforAnswer( $response, $answers )	{
		//debug($response);
		//debug($answers);
		foreach($answers as $answer)	{
			if($answer['answer']==$response)	return $answer['fraction'];
		}
		return 0;
	}

/**
 * __getMarksforAnswer - get fraction of a given answer by comparing with stored answers of a question
 *
 * @param string $fraction
 * @param array $maxmark
 * @return float
 */
	function __getMarksforAnswer( $fraction, $maxmark)	{
		//debug($fraction);
		//debug($maxmark);
		return $maxmark * $fraction;
		/*foreach($answers as $answer)	{
			if($answer['answer']==$response)	return $answer['fraction'];
		}*/
		//return 0;
	}

/**
 * markAsFinished - Update an attempt to finished
 *
 * @param mixed $attempt
 * @return array of data
 */
	function markAsFinished( $id )	{
		$this->id = $id;
		$data = array(
			'id'=>$id,
			'state'=>'finished',
			'locked'=>true,
			'timefinish'=>date('Y-m-d h:i:s'),
			'sumgrades'=>$this->getAttemptSumgrades($id),
			'correctquestions'=>$this->correctquestions, //this @var is updated within getAttemptSumgrades()
			'attempt'=>$this->field('attempt') + 1
		);
		return $this->save($data);
	}

/**
 * getTestQuizAttemptUsers - find the Users Attemps details
 *
 * @param mixed $attempt
 * @return array of TestQuizAttemptUsers
 */
function getTestQuizAttemptUsers( $id = null , $options = array()) {

$this->Behaviors->load('Containable');
		$defaults = array(
			'conditions'=>array('TestQuizAttempt.quiz_id'=>$id),
			'contain'=>array('User'=>array('id', 'first_name', 'last_name', 'full_name'))
		); 
		
		$options = array_merge_recursive($defaults, $options);
		//pr($options);
		$this->bindModel(
				array('belongsTo' => array(
								'User' => array(
										'className' => 'User',
										'foreignKey' => 'user_id'
								)
						)
				)
		);
		//$this->recursive = 0;
		$TestQuizAttemptUsers = $this->find('all', $options);
		//$this->Model->load('User');
		$i = 0;
		foreach($TestQuizAttemptUsers as $TestQuizAttemptUser){

			$options = array('conditions'=>array('TestQuizAttempt.quiz_id'=>$TestQuizAttemptUser['TestQuizAttempt']['quiz_id'],  'TestQuizAttempt.user_id'=>$TestQuizAttemptUser['TestQuizAttempt']['user_id']));

			$attempt_count = $this->find('count', $options);
			$TestQuizAttemptUsers[$i]['TestQuizAttempt']['attempt_count'] = $attempt_count;

			$i++;
		}
		

		return $TestQuizAttemptUsers;
		//pr($TestQuizAttemptUsers);

}


}