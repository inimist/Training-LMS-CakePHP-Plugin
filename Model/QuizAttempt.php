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

 class QuizAttempt extends TrainingAppModel {

		public $useTable = 'quiz_attempts';

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
	

function beforeFind($options = array()) {
  parent::beforeFind($options);
  if(!$this->getArchived()) {
   $default = array('QuizAttempt.archived' => 0);
   $options['conditions'] = array_merge($options['conditions'], $default);
  }
  if(!$this->resetArchived) $this->setArchived(false);
  return $options;
 }
 public function setArchived($archived, $reset = false) {
  $this->archived = $archived;
  $this->resetArchived = $reset;
 }
 private function getArchived() {
  return $this->archived;
 }

	function getUserAttempt($quizid, $userid, $options = array(), $action = 'first')	{
		$defaults =  array('conditions'=>array(
		'quiz_id'=>$quizid,
		'user_id'=>$userid),
		'recursive'=>-1);
		$options = array_merge_recursive($defaults, $options);
		return $this->find($action, $options);
	}

	function countAttempts($quizid)	{
		return $this->find('count', array('conditions'=>array(
				'quiz_id'=>$quizid, 
			//'state'=>'finished'
		),
				'recursive'=>-1)
		);
	}

	function hasAttempts($quizid)	{
		return (int) $this->countAttempts($quizid) ;
	}

/**
 * getAttempt - arrange slots from lower to higher.. Ex: from 1,3,2,5 to 1,2,3,4
 *
 * @param mixed $attempt
 * @return array of data
 */
	function getAttempt($attempt){ //allow ID or data row
		if(is_array($attempt) && isset($attempt['QuizAttempt']))	{
			$id = $attempt['QuizAttempt']['id'];
		}	else	{
			$id = $attempt; $attempt=null;
		}
		if(!$attempt)	{
			$attempt = $this->find('first', array('conditions'=>array('QuizAttempt.id'=>$id)));
		}
		return $attempt;
	}

/**
 * getQuizAttemptQuestions - arrange slots from lower to higher.. Ex: from 1,3,2,5 to 1,2,3,4
 *
 * @param mixed $attempt
 * @return array of data
 */
	function getQuizAttemptQuestions($attempt)	{
		//load quiz model
		$Quiz = ClassRegistry::init('Training.Quiz');
		$questions = $Quiz->getQuizQuestions($attempt['QuizAttempt']['quiz_id']);
		return $questions;
	}

/**
 * getAttemptSumgrades - get sumgrades by an attempt ID or data set/row
 *
 * @param mixed $attempt ; ID or data set (with QuizAttempt index set)
 * @return float
 */
	function getAttemptSumgrades($attempt)	{
		$attempt = $this->getAttempt($attempt);
		//debug($attempt);
		$questions = $this->getQuizAttemptQuestions($attempt);
		//debug($questions);
		$QuestionAttempt = ClassRegistry::init('Training.QuestionAttempt');
		$questionAttempts = $QuestionAttempt->find('all', array('conditions'=>array('QuestionAttempt.quiz_attempt_id'=>$attempt['QuizAttempt']['id'])));
		//debug($questionAttempts);
		$questions = $this->mergeQuestionsAndAttempts($questions, $questionAttempts);
		//debug($questions);
		//exit;
		//$this->calcQuizAttemptGradesByQuestionsDataset( $questions );
		//debug($this->correctquestions);
		//exit;
		return $this->calcQuizAttemptGradesByQuestionsDataset( $questions );
	}

/**
 * updateAttemptSumgrades - update sumgrades by an attempt ID or data set/row
 *
 * @param mixed $attempt ; ID or data set (with QuizAttempt index set)
 * @return array of data
 */
	function updateAttemptSumgrades($attempt)	{
		$attempt = $this->getAttempt($attempt);
		$this->id = $attempt['QuizAttempt']['id'];
		$this->saveField('sumgrades', $this->getAttemptSumgrades($attempt));
		$this->saveField('correctquestions', $this->correctquestions);
	}

/**
 * mergeQuestionsAndAttempts - merge quiz questions and attempt all together (just a useful tool)
 *
 * @param mixed $attempt
 * @return array of data
 */
	function mergeQuestionsAndAttempts($questions, $questionAttempts)	{
		//arrange them'll in a single array for easy access
		foreach($questions as $i=>$question)	{
			foreach($questionAttempts as $qa)	{
				if($qa['QuestionAttempt']['question_id'] == $question['Question']['id'])	{
					$questions[$i]['QuestionAttempt'] = $qa['QuestionAttempt'];
				}
			}
		}
		return $questions;
	}

/**
 * calcQuizAttemptGrades - calculating quiz attempt grades for a given dataset.
 *
 * @param mixed $attempt
 * @return array of data
 */
	function calcQuizAttemptGradesByQuestionsDataset($questions)	{
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
		if(isset($question['QuestionAttempt']))	{
			$qattempt = $question['QuestionAttempt'];
		}	else	{
			$qattempt = $question;
		}
		switch($qtype)	{
			case 'truefalse':
			case 'multiplechoice-one';
				if($qattempt['rightanswer'] == $qattempt['responsesummary'])	{
					$maxmarks = $qattempt['maxmark'] ? $qattempt['maxmark'] : '1';
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
					//$qfraction = $this->__getFractionforAnswer($response, $question['QuestionAnswer']);//Marks will be awarded only if question full correct
						//$maxmarks += $this->__getMarksforAnswer($qfraction, $qattempt['maxmark']);
					}	else	{ //now it is an incorrect answer, let's penalize user
						//debug($qfraction = $this->__getPenaltyByQuestion($response, $question['QuestionAnswer']););
						//$penalty = $question['Question']['penalty'];  //No Partial penalty for partial correct question, just award 0 marks 
						//$maxmarks -= $penalty * $qattempt['maxmark'];
						//debug($penalty);
					}
				}
				if(sizeof($ranswers)==sizeof($ralist) && sizeof($rslist)==sizeof($ralist)){ 
					$this->correctquestions++;
					$maxmarks = $qattempt['maxmark'] ? $qattempt['maxmark'] : '1';
				}else{
					$maxmarks = 0;
				}
			break;
			case 'matching-pairs':
				if($qattempt['rightanswer'] == $qattempt['responsesummary'])	{
					$maxmarks = $qattempt['maxmark'] ? $qattempt['maxmark'] : '1';
					$this->correctquestions++;
				}
			break;
			case 'singleline':
			case 'multiplelines';
			case 'multiplelines-rich';
				$maxmarks = $qattempt['minfraction'] * $qattempt['maxmark']; //here we assume that 'minfraction' field is updated by admin user manually
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
	function markAsFinished( $id, $signed=false )	{
		$this->id = $id; 
		$lock = false;
		$quiz_id = $this->field('quiz_id');
		$enrol_id = $this->field('courses_enrollment_id');
		$this->setArchived(true);
		$count = $this->find('count', array('conditions'=>array('QuizAttempt.quiz_id'=>$quiz_id, 'QuizAttempt.courses_enrollment_id'=>$enrol_id, 'state'=>'finished')));
		if($count >= QUIZ_LOCK_OFFSET) $lock = true;
		
		$data = array(
			'id'=>$id,
			'state'=>'finished',
			'signed_by_user'=>$signed,
			'locked'=>$lock,
			'timefinish'=>date('Y-m-d H:i:s'),
			'sumgrades'=>$this->getAttemptSumgrades($id),
			'correctquestions'=>$this->correctquestions, //this @var is updated within getAttemptSumgrades()
			'attempt'=>$this->field('attempt') + 1
		);
		return $this->save($data);
	}

/**
 * getQuizAttemptUsers - find the Users Attemps details
 *
 * @param mixed $attempt
 * @return array of QuizAttemptUsers
 */
function getQuizAttemptUsers( $id = null , $options = array()) {

$this->Behaviors->load('Containable');
		$defaults = array(
			'conditions'=>array('QuizAttempt.quiz_id'=>$id),
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
		$quizAttemptUsers = $this->find('all', $options);
		//$this->Model->load('User');
		$i = 0;
		foreach($quizAttemptUsers as $quizAttemptUser){

			$options = array('conditions'=>array('QuizAttempt.quiz_id'=>$quizAttemptUser['QuizAttempt']['quiz_id'],  'QuizAttempt.user_id'=>$quizAttemptUser['QuizAttempt']['user_id']));
			$this->setArchived(true);
			$attempt_count = $this->find('count', $options);
			$quizAttemptUsers[$i]['QuizAttempt']['attempt_count'] = $attempt_count;

			$i++;
		}
		

		return $quizAttemptUsers;
		//pr($quizAttemptUsers);

}


}