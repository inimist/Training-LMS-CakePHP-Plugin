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
 * Quiz Model
 *
 * @property Training $Quiz
 */

App::uses('TrainingAppModel', 'Training.Model');

class Quiz extends TrainingAppModel {

	public $actsAs = array('SoftDelete');

	public $name = 'Quiz';
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Quiz name must not be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Course' => array(
			'className' => 'Training.Course',
			'foreignKey' => 'course_id',
			'counterCache' => true,
			'counterScope' => array(
											'Quiz.deleted' => 0
											),
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public $hasMany = array(
		'QuizSlot' => array(
			'className' => 'Training.QuizSlot',
			'foreignKey' => 'quiz_id',
			'conditions' => '',
			'fields' => '',
			'order' => 'slot ASC',
			'dependent' => true
		)
	);

	/**
	 * quizSettings If set to true there will only one default Quiz in a course in a given time
	 *
	 * @usedefaullt bool
	*/

	public $quizSettings = array(
		'useopenclose'=>false, //If you want to set open and close time to take quizzes
		'usetimelimitation'=>false, //If you want to impose time limitation to take quizzes
		'usedefault'=>true, //If set to true there will only one default Quiz in a course in a given time
		'usegrading'=>false,  //If set to true quiz will be graded according to the marks percentag,
		'usepassfail'=>true,  //If set to true quiz will be graded according to the marks percentag
		'passfailcriteria'=>'question',  //1). "question" ; min questions required to pass the quiz 2). "grade" : min grade (out of 10) required to pass the quiz 
	);


/**
 * getQuiz - get quiz by ID or by an incoming array.
 *
 * @param boolean $id ; quiz id
 * @param array   $questions
 * @return string
 */
	function getQuiz($quiz, array $options)	{
		if(is_array($quiz) && isset($quiz['Quiz']))	{
			$id = $quiz['Quiz']['id'];
		}	else if((int) $quiz)	{
			$id = $quiz;$quiz=null;
		}	else	{
			return;
		}
		if(!$quiz)	{
			$options = array_merge_recursive(array('conditions'=>array('Quiz.id'=>$id), 'recursive'=>-1), $options);
			$quiz = $this->find('first', $options);
		}
		return $quiz;
	}

	public function isInprogress($quiz_id, $user_id, $currentpage)	{
		$options = array('conditions'=>array('QuizSlot.page >= '=> $currentpage, 'QuizSlot.quiz_id'=>$quiz_id), 'group'=>array('QuizSlot.page'));
		return (int)$this->QuizSlot->find('count', $options);
	}

	public function isLastpage($quiz_id, $currentpage)	{
		$options = array('conditions'=>array('QuizSlot.page > '=> $currentpage, 'QuizSlot.quiz_id'=>$quiz_id), 'group'=>array('QuizSlot.page'));
		return !(int)$this->QuizSlot->find('count', $options);
	}

	public function getLastpage($quiz_id)	{
		$options = array('conditions'=>array('QuizSlot.quiz_id'=>$quiz_id), 'group'=>array('QuizSlot.page'));
		return $this->QuizSlot->find('count', $options);
	}

	public function hasQuestions($id)	{
		return (int) $this->QuizSlot->find('count', array('conditions'=>array('QuizSlot.quiz_id'=>$id), 'limit'=>1)) > 0;
	}

	/**
	 * questionCount - get questionCount for a quiz
	 * @param int $id
	 * @return int
	*/
	function questionCount( $id )	{
		return (int) $this->QuizSlot->find('count', array('conditions'=>array('QuizSlot.quiz_id'=>$id)));
	}

	/**
	 * updateQuestionCount - update questionCount for a quiz
	 * @param int $id
	 * @return void
	 */
	function updateQuestionCount( $id, $updateCourseQQCount = true )	{
		$this->id = $id;
		$this->saveField('question_count', $this->questionCount( $id ));
	}

	/**
	 * updateAllQuestionCount - update questionCount for all available courses
	 * @param int $id
	 * @return void
	 */
	function updateAllQuestionCount()	{		
		foreach($this->find('all', array('fields'=>array('id'))) as $quiz)	{
			$this->updateQuestionCount( $quiz['Quiz']['id'] );
		}
	}

/**
 * hasAttempt - get quiz attempt by ID or by an incoming array.
 *
 * @param $quiz mixed ; quiz id or a row with "Quiz" index
 * @return boolean
 */

	public function hasAttempt($quiz)	{
		$quiz = $this->getQuiz($quiz, array('fields'=>array('Quiz.attempts'), 'limit'=>1));
		if((int) $quiz['Quiz']['attempts'] > 0 ) return true;
		return false;
	}

/**
 * isDefault - Checks whether a quiz is set as default.
 *
 * @param $id integar Quiz id
 * @return boolean
 */

	public function isDefault( $id )	{
		$this->id = $id;
		return $this->field('is_default');
	}

 public function getdefaultQuiz($course_id){
	return $this->find('first', array('conditions'=> array('Quiz.course_id'=>$course_id, 'Quiz.is_default'=>true)));
 }
/**
 * QuestionsDelete - Process question deletion
 *
 * @param mixed &$data
 * @return array
 */

	public function QuestionsDelete( &$data )	{

		if($this->hasAttempt($data)) return false;

		if(isset($data['QuizSlot']) && sizeof($data['QuizSlot']) >0)	{
			$deleted = false;
			//if all set check whether there is a request to delete questions
			foreach($data['QuizSlot'] as $i => $entry)	{
				if(isset($entry['delete']) && (int)$entry['delete'])	{
					//$this->QuizSlot->deleteAll(array('quiz_id'=>$data['Quiz']['id'], 'question_id'=>$question_id));
					$this->QuizSlot->deleteAll(array('QuizSlot.id'=>(int)$entry['id']));
					unset($data['Quiz']['questions'][$i]);
					unset($data['QuizSlot'][$i]);
					$deleted = true;
				}
			}
			return array('data'=>$data, 'deleted'=>$deleted);
		}		
	}

/**
 * RearrangeSlots - arrange slots from lower to higher.. Ex: from 1,3,2,5 to 1,2,3,4
 *
 * @param boolean $data ; posted data
 * @return array
 */

	public function RearrangeSlots( &$data, $action=null)	{
		
		//$data = array(); //the var to store the data to be saved		
		$p=1; //page(, or order)
		//checksum is a way to check the change in the data posted
		$checksum=0;
		$resetslots = true; //this decides whether to update the slots

		//debug($this->request->data);
		//exit;

		//if(!isset($this->request->data['Quiz']['questions'])) return false;

		//if(!$this->request->data['Quiz']['questions']) return false;

		/*foreach($this->request->data['QuizSlot'] as $i => $entry)	{

			//debug($i);

			if($entry['question_id'])	{

				$slot = isset($this->request->data['Quiz']['questions']) ? $this->request->data['QuizSlot'][$i]['slot'] : $p;

				$checksum += $entry['question_id'] / $slot;

				$data[] = array(
					'quiz_id'=>$this->request->data['Quiz']['id'], 
					'question_id'=>$entry['question_id'],
					'slot'=>$slot,
					'page'=>$p
				);
				$p++;
			}
		}*/
		//usually for edit
		/*if(isset($this->request->data['Quiz']['checksum']))	{
			$this->request->data['Quiz']['checksum']= (float)$this->request->data['Quiz']['checksum'];
			//checking whether anything was changed, either "order" or "deleted" or "both"
			if (abs(($this->request->data['Quiz']['checksum'] - $checksum) / $checksum) < 0.00001) {
			}	else	{
				$resetslots = true;
			}
		}*/

		if( !isset($data['QuizSlot']) )	return;
		if( !sizeof($data['QuizSlot']) > 0 )	return;

		if($resetslots)	{

			$slots = array();
			foreach ($data['QuizSlot'] as $key => $row)
			{
					$slots[$key] = $row['slot'];
			}
			array_multisort($slots, SORT_ASC, $data['QuizSlot']);

			if($action && $action=='add') $currentHighestSlot = $this->QuizSlot->find('count', array('conditions'=>array('quiz_id'=>$data['Quiz']['id']))); //Find count of quiz slots, hence no of slots alloted in case of 1 question per slot

			foreach ($data['QuizSlot'] as $i => $row)
			{		
			if(isset($currentHighestSlot)){
						$data['QuizSlot'][$i]['slot'] = $currentHighestSlot+$i+1;
						$data['QuizSlot'][$i]['page'] = $currentHighestSlot+$i+1;
					}else{
						$data['QuizSlot'][$i]['slot'] = $i+1;
						$data['QuizSlot'][$i]['page'] = $i+1; //we need to set page differently TODO
					}
			}

			//debug($data['QuizSlot']);

			//for now just delete'em all and re-enter, May be later on we will work on reseting the order to 1,2,3 based on the lower to highest in the received data
			//$this->Quiz->QuizSlot->deleteAll(array('quiz_id' => $data['Quiz']['id']));

			//debug($data);
			//uasort($data, 'cmp');
			//debug($data);

			/*$min=1;
			foreach($data as $i=>$row)	{
				if($row['slot'] > $min) $data[$i]['slot'] = 
			}*/
		}

		//exit;

		return $data;
	}

	function updateSumgrades( $id )	{
		$sumgrades =  @$this->QuizSlot->find('first', array('conditions'=>array('QuizSlot.quiz_id'=>$id), 'fields'=>array('sum(maxmarks) AS sumgrades')))[0]['sumgrades'];
		$this->id = $id;
		$this->saveField('sumgrades', $sumgrades);
	}

	public function getQuizQuestions($id, $page = null, $options = array())	{
		$defaults['joins'] = array(
			array('table' => 'quiz_slots',
				'alias' => 'QuizSlot',
				'type' => 'LEFT',
				'conditions' => array(
						'QuizSlot.question_id = Question.id',
				)
			)
		);
//pr($id); pr($page);
		$defaults['fields'] = array('Question.*', 'QuizSlot.*', 'QuestionType.*');

		$defaults['conditions'] = array(
			'QuizSlot.quiz_id' => $id
		);
		if(!is_null($page)) $defaults['conditions']['QuizSlot.page'] = $page;

		$options = array_merge($defaults, $options);

		//debug($defaults);
		//$this->Question->unbindModel(array('hasMany'=>array('QuestionAnswer')));
		$Question = ClassRegistry::init('Training.Question');
		$questions = $Question->find('all', $options);
		//debug($questions); //exit;
		return $questions;
	}

	public function initQuizAttempt( $data, $reattempt = false )	{
		//$reattempt = true;
		$quizid = $data['QuizAttempt']['quiz_id'];
		$userid = $data['QuizAttempt']['user_id'];
		$courses_enrollment_id = $data['QuizAttempt']['courses_enrollment_id'];
		//debug($courses_enrollment_id);
		//exit;
		//$this->bindModel(array('hasMany'=>array('QuizAttempt')));

		$Question = ClassRegistry::init('Training.Question');
		$QuizAttempt = ClassRegistry::init('Training.QuizAttempt');
		$QuestionAttempt = ClassRegistry::init('Training.QuestionAttempt');

		$data = array(
			'quiz_id'=>$quizid,
			'user_id'=>$userid,
			//'attempt'=>1,
			'courses_enrollment_id'=>$courses_enrollment_id,
			'currentpage'=>1,
			'state'=>'inprogress',
			'timestart'=>date('Y-m-d H:i:s'),
		 );
			$qa_options = array('conditions' => array('QuizAttempt.courses_enrollment_id' =>$courses_enrollment_id)); //get attempt using enrollment id
			$quizAttempt = $QuizAttempt->getUserAttempt($quizid, $userid, $qa_options);
			if(!$quizAttempt || $reattempt)	{
				//debug($reattempt);
				$QuizAttempt->create(); //if its re-attempt or quizattempt not exists , create new one
			$QuizAttempt->save($data);
			$quizAttempt = $QuizAttempt->read();
			//debug($quizAttempt); 
			//debug($data); exit;

			$questions = $this->getQuizQuestions($quizid);
			//debug($questions); exit;

			$dataQuestionAttempts = array();
			
			foreach($questions as $question)	{
				$rightAnswer = $Question->getRightAnswer($question);
				$dataQuestionAttempts[] = array(
					'slot' => $question['QuizSlot']['slot'],
					'quiz_attempt_id' => $QuizAttempt->id,
					'question_id' => $question['Question']['id'],
					'maxmark' => $question['QuizSlot']['maxmarks'] ? $question['QuizSlot']['maxmarks'] : '1',
					'rightanswer'=> $rightAnswer,
					'questionsummary'=>$Question->getQuestionsummary( $question )
				);
			}
			//debug($dataQuestionAttempts);
			//exit;
			$QuestionAttempt->saveAll($dataQuestionAttempts);
		}
		//debug($quizAttempt); exit;
		return $quizAttempt;
	}

	function isActive( $id )	{
		if(!$this->hasQuestions( $id ))	{
			return false;
		}
		//more checks coming here
		return true;
	}

/**
 * setOpencloseflag - set flag
 *
 * @param $flag boolean
 * @return void
 */
	function setOpencloseflag( $flag=true )	{
		$this->quizSettings['useopenclose'] = $flag;
	}

/**
 * getOpencloseflag - get flag
 *
 * @return boolean
 */
	function getOpencloseflag()	{
		return $this->quizSettings['useopenclose'];
	}

/**
 * setTimelimitflag - set flag
 *
 * @param $flag boolean
 * @return void
 */
	function setTimelimitflag( $flag=true )	{
		$this->quizSettings['usetimelimitation'] = $flag;
	}

/**
 * getTimelimitflag - get flag
 *
 * @return boolean
 */
	function getTimelimitflag()	{
		return $this->quizSettings['usetimelimitation'];
	}


/**
 * getUsedefaultflag - get flag
 *
 * @return boolean
 */
	function getUsedefaultflag()	{
		return $this->quizSettings['usedefault'];
	}

/**
 * getQuizSettings - get quiz settings
 *
 * @return boolean
 */
	function getQuizSettings()	{
		return $this->quizSettings;
	}

/**
 * setQuizSettings - get quiz settings
 *
 * @return boolean
 */
	function setQuizSettings($settings = array())	{
		$this->quizSettings = array_merge($this->getQuizSettings(), $settings);
	}

/**
 * setDefault - set flag
 *
 * @param $flag boolean
 * @return void
 */
	function setDefault( $course_id, $quiz_id)	{
		$this->query("Update `quizzes` set `is_default`='0' where `course_id`='$course_id'");
		$this->query("Update `quizzes` set `is_default`='1' where `id`='$quiz_id'");
		$question_count = $this->field('question_count', array('is_default'=>'1', 'course_id'=>$course_id));
		$minpassquestions = $this->field('minpassquestions', array('is_default'=>'1', 'course_id'=>$course_id));
		if($question_count == NULL) $question_count = 0;
		if($minpassquestions == NULL) $minpassquestions = 0;
		$this->Course->query("Update `courses` set `quizquestion_count`= $question_count , `minpassquestion_count`= $minpassquestions where `id`=$course_id;");
	}

/**
 * Get user attempts for a quiz
 *
 * @param $quizid integar
 * @param $userid integar
 * @param $options array
 * @param $action first
 * @return mixed array|null
 */
	function getUserAttempt($quizid, $userid, $options = array(), $action = 'first')	{
		$QuizAttempt = ClassRegistry::init('Training.QuizAttempt');
		return $QuizAttempt->getUserAttempt($quizid, $userid, $options, $action);
	}


// Functions for Admin to Test Quiz 
public function getTestQuizQuestions($id, $page = null, $options = array())	{
		$defaults['joins'] = array(
			array('table' => 'quiz_slots',
				'alias' => 'QuizSlot',
				'type' => 'LEFT',
				'conditions' => array(
						'QuizSlot.question_id = Question.id',
				)
			)
		);

		$defaults['fields'] = array('Question.*', 'QuizSlot.*', 'QuestionType.*');

		$defaults['conditions'] = array(
			'QuizSlot.quiz_id' => $id
		);
		if(!is_null($page)) $defaults['conditions']['QuizSlot.page'] = $page;

		$options = array_merge($defaults, $options);

		//debug($defaults);
		//$this->Question->unbindModel(array('hasMany'=>array('QuestionAnswer')));
		$Question = ClassRegistry::init('Training.Question');
		$questions = $Question->find('all', $options);
		//debug($questions); exit;
		return $questions;
	}

	public function initTestQuizAttempt( $data, $reattempt = false )	{
		//$reattempt = true;
		$quizid = $data['TestQuizAttempt']['quiz_id'];
		$userid = $data['TestQuizAttempt']['user_id'];
		//$courses_enrollment_id = $data['QuizAttempt']['courses_enrollment_id'];
		//debug($courses_enrollment_id);
		//exit;

		//$this->bindModel(array('hasMany'=>array('QuizAttempt')));

		$Question = ClassRegistry::init('Training.Question');
		$QuizAttempt = ClassRegistry::init('Training.TestQuizAttempt');
		$QuestionAttempt = ClassRegistry::init('Training.TestQuestionAttempt');

		$data = array(
			'quiz_id'=>$quizid,
			'user_id'=>$userid,
			//'attempt'=>1,
			'courses_enrollment_id'=> null,//$courses_enrollment_id,
			'currentpage'=>1,
			'state'=>'inprogress',
			'timestart'=>date('Y-m-d H:i:s'),
		);
		$quizAttempt = $QuizAttempt->getTestUserAttempt($quizid, $userid );
			//debug($quizAttempt); exit;
			if(!$quizAttempt || $reattempt)	{
				//debug($reattempt);
			if( $reattempt )	{
			$QuizAttempt->create();
			}
			
			$QuizAttempt->save($data);
			$quizAttempt = $QuizAttempt->getTestUserAttempt(
				$quizid, 
				$userid
			);
			//debug($quizAttempt); 
			//debug($data); exit;

			$questions = $this->getTestQuizQuestions($quizid);
			//debug($questions); exit;

			$dataQuestionAttempts = array();
			
			foreach($questions as $question)	{
				$rightAnswer = $Question->getRightAnswer($question);
				$dataQuestionAttempts[] = array(
					'slot' => $question['QuizSlot']['slot'],
					'test_quiz_attempt_id' => $QuizAttempt->id,
					'question_id' => $question['Question']['id'],
					'maxmark' => $question['QuizSlot']['maxmarks'],
					'rightanswer'=> $rightAnswer,
					'questionsummary'=>$Question->getQuestionsummary( $question )
				);
			}
			//debug($dataQuestionAttempts);
			//exit;
			$QuestionAttempt->saveAll($dataQuestionAttempts);
		}
		//debug($quizAttempt); exit;
		return $quizAttempt;
	}




}