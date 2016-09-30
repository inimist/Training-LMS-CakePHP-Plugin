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
App::uses('TrainingAppController', 'Training.Controller');
/**
 * Quizzes Controller
 *
 * @property Quiz $Quiz
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class QuizzesController extends TrainingAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'FileUpload.FileUpload','Mpdf.Mpdf', 'Training.Training', 
		/*'GoogleAPI.YouTube' => array(
    'Service' => array(
        'YouTube'
    )
  )*/
		);

  public $helpers = array('TinyMCE.TinyMCE');

	function beforeFilter() {
		parent::beforeFilter();
		$this->_validateRequest();
	}


	function beforeRender() {
		$this->set('_quizSettings', $this->Quiz->getQuizSettings());
		//legacy settings set
		foreach($this->Quiz->getQuizSettings() as $key=>$value)	{
			$this->set($key, $value);
		}
		parent::beforeRender();
	}
		

/**
 * index method
 *
 * @return void
 */
	public function admin_index() {
		$options = array('conditions' => array('Quiz.course_' . $this->Course->primaryKey => $this->Course->id));
		$this->paginate = $options;
		$this->Quiz->recursive = 0;
		$quizzes = $this->Paginator->paginate();
		//debug($quizzes);
		$this->loadModel('Training.QuizAttempt');
	foreach($quizzes as $i=>$quiz){
			$option = array('fields' => array('Question.*'), 'recursive'=>-1);
			$quizzes[$i]['Questions'] = $this->Course->Quiz->getQuizQuestions($quiz['Quiz']['id'], null, $option);
			$quizzes[$i]['Quiz']['attempts'] = $this->QuizAttempt->hasAttempts($quiz['Quiz']['id']);
		}
		$this->set(compact('quizzes'));
		//pr($this->Quiz->find('all'));
	}

/**
 * add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Quiz->create();
			$this->request->data['Quiz']['course_module_id'] = $this->Course->getCurrentModule( $this->Course->id );
			if ($this->Quiz->save($this->request->data)) {
				//set default quiz
				if(isset($this->request->data['Quiz']['is_default']) && $this->request->data['Quiz']['is_default'])	{
					$this->Quiz->setDefault($this->Course->id, $this->Quiz->id);
				}
				$this->log("#### added a Quiz ##action-view/{$this->Quiz->id}?course_id={$this->Course->id}## to ##Course:{$this->Course->id}##", "system");
				$this->Session->setFlash(__('The quiz has been saved.'));
				return $this->redirect(array('action' => 'index', '?'=>array('course_id'=>$this->Course->id)));
			} else {
				$this->Session->setFlash(__('The quiz could not be saved. Please, try again.'));
			}
		}
		$this->request->data['Quiz']['course_id'] = $this->Course->id;
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Quiz->exists($id)) {
			throw new NotFoundException(__('Invalid quiz'));
		}
		if ($this->request->is(array('post', 'put'))) {

			$this->Quiz->QuestionsDelete( $this->request->data );
			$this->Quiz->RearrangeSlots( $this->request->data );

			if ($this->Quiz->save($this->request->data['Quiz'])) {
				//set default quiz
				if(isset($this->request->data['Quiz']['is_default']) && $this->request->data['Quiz']['is_default'])	{
					$this->Quiz->setDefault($this->Course->id, $this->Quiz->id);
				}

				if(isset($this->request->data['QuizSlot']) && sizeof($this->request->data['QuizSlot']) > 0)	{
					$this->Quiz->QuizSlot->saveAll($this->request->data['QuizSlot']);
				}

				if($this->Quiz->hasQuestions( $id ))	{
					$this->Quiz->updateSumgrades( $this->Quiz->id );
					$this->Quiz->updateQuestionCount( $this->Quiz->id );
					$this->Course->updateQuizQuestionCount( $this->Course->id ); //Update default quiz question count in course
				}

				$this->Session->setFlash(__('The quiz has been saved.'));

				$this->log("##authuser## updated a quiz ##action-view/{$this->Quiz->id}?course_id={$this->Course->id}##", 'system');

				return $this->redirect(array('action' => 'index', '?'=>array('course_id' => $this->request->data['Quiz']['course_id'])));
			} else {
				$this->Session->setFlash(__('The quiz could not be saved. Please, try again.'));
			}
		}
		
		//else {
			$options = array('conditions' => array('Quiz.' . $this->Quiz->primaryKey => $id), 'recursive'=>2);
			$this->Quiz->QuizSlot->bindModel(array('belongsTo'=>array('Question')));
			$this->Quiz->QuizSlot->unbindModel(array('belongsTo'=>array('Quiz')));

			$this->loadModel('Training.QuestionType');
			$questionTypes = $this->QuestionType->find('list');
			$this->set(compact('questionTypes'));
			$this->request->data = $this->Quiz->find('first', $options);
			//debug($this->request->data);
		//}

		$this->loadModel('Training.QuizAttempt');

		$attempts = $this->QuizAttempt->hasAttempts($id);

		$this->set(compact('attempts'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Quiz->id = $id;
		if (!$this->Quiz->exists()) {
			throw new NotFoundException(__('Invalid quiz'));
		}
		$this->request->allowMethod('post', 'delete');

		if($this->Quiz->hasAttempt( $id ))	{
			$this->Session->setFlash(__('A quiz with attempts cannot be deleted'));
			return $this->redirect($this->referer());
		}

		if($this->Quiz->isDefault( $id ))	{
			$this->Session->setFlash(__('A default quiz cannot be deleted'));
			return $this->redirect($this->referer());
		}

		if ($this->Quiz->delete()) {
			$this->log("##authuser## deleted a quiz from ##action-view##", 'system');
			$this->Session->setFlash(__('The quiz has been deleted.'));
		} else {
			$this->Session->setFlash(__('The quiz could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_undelete($id = null) {
		$this->Quiz->id = $id;
		if (!$this->Quiz->exists()) {
			throw new NotFoundException(__('Invalid quiz'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Quiz->undelete($id)) {
			$this->log("##authuser## retrieved a quiz ##action-view##", 'system');
			$this->Session->setFlash(__('Quiz has been retrieved.'));
		} else {
			$this->Session->setFlash(__('Quiz could not be retrieved. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_add_question method
 *
 * @throws NotFoundException
 * @param string $quizid
 * @return void
 */
	public function admin_add_question($quizid)	{
		$this->Quiz->id = $quizid;
		if (!$this->Quiz->exists()) {
			throw new NotFoundException(__('Invalid quiz'));
		}

		if ($this->request->is(array('post', 'put'))) {

			//debug($this->request->data);
			//exit;

			foreach($this->request->data['QuizSlot'] as $i=>$entry)	{
				if(!isset($entry['question_id']))	{
					unset($this->request->data['QuizSlot'][$i]);
				}
			}

			$this->request->data['QuizSlot'] = $this->Quiz->QuizSlot->noDuplicates($this->request->data['QuizSlot']); 
			$this->Quiz->RearrangeSlots( $this->request->data, 'add' );
			//debug($this->request->data);
			//exit;
			if($this->request->data['QuizSlot'])	{
				//debug($this->request->data['QuizSlot']);exit;
				if ($this->Quiz->QuizSlot->saveAll($this->request->data['QuizSlot'])) {
					$this->Quiz->updateSumgrades( $this->Quiz->id );
					$this->Quiz->updateQuestionCount( $this->Quiz->id );
					$this->Course->updateQuizQuestionCount( $this->Course->id );
					$this->Session->setFlash(__('The quiz has been saved.'));
					$this->log("##authuser## added few questions to quiz ##Quiz:{$quizid}##", 'system');
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('The quiz could not be saved. Please, try again.'));
				}
			}
		}

		$this->request->data = $quiz = $this->Quiz->read();

		//debug($quiz);
		$this->loadModel('Training.Question');
		$options = array('conditions'=>array('Question.course_id'=>$quiz['Quiz']['course_id'], 'Question.deleted'=>false));

		$this->paginate = $options;
		$questions = $this->Paginator->paginate('Question');
		$this->loadModel('Training.QuestionAnswer');
			foreach($questions as $i=>$question){
				$correctAnswer = $this->QuestionAnswer->getCorrectAnswer($question['Question']['id']);
				$questions[$i]['QuestionAnswer'] = $correctAnswer ;
			}
		$this->set(compact('questions', 'quiz'));
	}

/**
 * summary method
 *
 * @param string $quizid
 * @return void
 */
	public function summary( $id ) {
		//$this->loadModel('Training.Quiz');
		$this->loadModel('Training.Course');
		$this->loadModel('Training.QuizAttempt');
		$this->loadModel('Training.Question');
		$this->loadModel('Training.QuestionAttempt');

		$this->QuizAttempt->bindModel(array('belongsTo'=>array('Quiz')));
		$quizAttempt = $this->QuizAttempt->find('first', array('conditions'=>array('QuizAttempt.id'=>$id, 'QuizAttempt.archived' => 0)));

		$this->request->data = $quizAttempt;
		
		$course = $this->Course->find('first', array('conditions'=>array('Course.id'=>$quizAttempt['Quiz']['course_id']), 'recursive'=>-1, 'fields'=>array('Course.name', 'Course.id', 'signature')));
		//debug($course );

		$this->QuestionAttempt->bindModel(array('belongsTo'=>array('Question')));
		$questionAttempt = $this->QuestionAttempt->find('all', array('conditions'=>array('QuestionAttempt.quiz_attempt_id'=>$id), 'order'=>array('QuestionAttempt.slot ASC')));

		//debug($questionAttempt);

		$course_id = $quizAttempt['Quiz']['course_id'];
		$this->set(compact('course', 'quizAttempt', 'questionAttempt', 'course_id'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Quiz->exists((int)$id)) {
			throw new NotFoundException(__('Invalid quiz'));
		}

		$exception = false;

		$this->loadModel('Training.QuizAttempt');
		$options = array('conditions' => array('Quiz.' . $this->Quiz->primaryKey => $id));
		$quiz = $this->Quiz->find('first', $options);

		//debug($quiz);

		$this->set(compact('quiz'));

		//debug($quiz);
		$userid = $this->Auth->user('id'); //current logged in User

		//$courseid = $this->Course->getCourseIdByQuizId($quizid);
		$verifyEnrol = $this->Course->verifyEnrol( $userid, $quiz['Quiz']['course_id'] );
		
		if( !$verifyEnrol )	{
			//throw new NotFoundException(__('You are not enrolled to this course'));
			$exception = __('You are not enrolled to this course');
		}

		if(isset($this->request->query['enid'])){
				$enrol_id = $this->request->query['enid'];
				$enrollment = $this->Course->getCurrentEnrollment($userid, $quiz['Quiz']['course_id'], $enrol_id);
				}
			else{
				$enrollment = $this->Course->getCurrentEnrollment($userid, $quiz['Quiz']['course_id']);
			}

		//check if your has learned the course
		$this->loadModel('Training.CoursesLearnLog');
		$isLearned = $this->CoursesLearnLog->isLearned($quiz['Quiz']['course_id'], $userid, $enrollment['CoursesEnrollment']['id']);
		//debug($userid); exit;
		if(!$isLearned){
			$this->Session->setFlash(__('You must visit course learning page before attempting a test. Click on the "Learn" button shown under course name.'), 'default',  array ('class' => 'btn-danger'));
			return $this->redirect($this->referer());
		}

		if(	!$quiz['Quiz']['is_default'] )	{
			if(!$this->QuizAttempt->getUserAttempt($quiz['Quiz']['id'], $userid, [], 'count'))	{
				$exception = __('This quiz is neither default nor you have any previous attempt');
			}
		}

		//if(!$this->Course->Quiz->QuizAttempt->getUserAttempt($quiz['Quiz']['id'], $userid, [], 'count'))	{
		//	$exception = __('No questions found in the quiz');
		//}

		if( !$exception )	{
			if( !$this->Quiz->isActive($id) )	{
				$exception = __('No questions found in the quiz');
			}
		}

		if( !$exception )	{

			//set quizAttempt options, to retrieve any existing attempt for this quiz
			$qaoptions = array('conditions'=>array('QuizAttempt.user_id'=>$userid, 'QuizAttempt.courses_enrollment_id'=>$enrollment['CoursesEnrollment']['id'], 'QuizAttempt.quiz_id'=>$id,  'QuizAttempt.archived' => 0));

			//look for quizAttempt
			$quizAttempt = $this->QuizAttempt->find('first', $qaoptions);

			if( !isset($quizAttempt['QuizAttempt']) )	{
				$this->request->data['QuizAttempt']['quiz_id'] = $id;
				$this->request->data['QuizAttempt']['user_id'] = $userid;
				$this->request->data['QuizAttempt']['courses_enrollment_id'] = $enrollment['CoursesEnrollment']['id'];
			}	else	{
				$this->request->data = $quizAttempt;
			}

			$quizAttempt['Quiz'] = $quiz['Quiz'];
			
			$this->set( compact('quizAttempt', 'enrollment') );
		}
		//pr($this->request->data); exit;
		$this->set( compact('exception') );
	}

/**
 * attempt method
 *
 * Method to take test
 *
 * @param string $enrollmentid
 * @return void
 */
	public function attempt($quizid = null) {

		//$this->loadModel('Training.Course');
		$this->loadModel('Training.QuizAttempt');
		$this->loadModel('Training.Question');
		$this->loadModel('Training.QuestionAttempt');

		$userid = $this->Auth->user('id'); //current logged in User
		$options = array('conditions'=>array('Quiz.id' => (int)$quizid), 'recursive'=>-1, 'fields'=>array('id', 'name', 'course_id')); //find quiz $options
		$quiz = $this->Quiz->find('first', $options);
		
		$this->set(compact('quiz'));

		//if no quiz found die on error!
		if(!$quiz)	{
			throw new NotFoundException(__('Invalid Test/Quiz Request'));
		}

		//Check whether this quiz has questions to be attempted!
		if(!$this->Quiz->hasQuestions($quizid))	{
			return $this->render('no_quiz_questions');
		}

		//$courseid = $this->Course->getCourseIdByQuizId($quizid);
		$verifyEnrol = $this->Course->verifyEnrol($userid, $quiz['Quiz']['course_id']);
		
		if(!$verifyEnrol)	{
			throw new NotFoundException(__('You are not enrolled to this course'));
		}

		//set quizAttempt options, to retrieve any existing attempt for this quiz
		$qaoptions = array('conditions'=>array('QuizAttempt.user_id'=>$userid,
																					'QuizAttempt.courses_enrollment_id'=>$this->request->data['QuizAttempt']['courses_enrollment_id'], 
																					'QuizAttempt.quiz_id'=>$quizid, 
																					'QuizAttempt.archived' => 0
																					)
																);
		//look for quizAttempt
		$quizAttempt = $this->QuizAttempt->find('first', $qaoptions);
		//pr($this->request->data);
		//debug($quizAttempt); exit;
		if ($this->request->is(array('post', 'put'))) {

			 $reattempt = false;
			//debug($this->request->data); debug($quizAttempt); 
			if(isset($this->request->data['QuizAttempt']['reattempt_yes']) && $quizAttempt['QuizAttempt']['state']=='finished')	{
				//debug($this->request->data);
				$this->QuizAttempt->id = $this->request->data['QuizAttempt']['id'];
				$this->QuizAttempt->saveField('archived', 1);
				//$quizAttempt = $this->Quiz->initQuizAttempt($this->request->data, true); //true means it is a re-attempt
				$this->request->data['QuizAttempt']['currentpage'] = 0;
				$reattempt = true;
			}

			if(isset($this->request->data['QuizAttempt']['mark_as_finised']))	{

				if($this->QuizAttempt->markAsFinished( $quizAttempt['QuizAttempt']['id'], $this->request->data['QuizAttempt']['signed_by_user']))	{
					$this->Quiz->updateAll(array('attempts'=>'attempts+1'), array('Quiz.id'=>$quiz['Quiz']['id']));
					$this->Course->checkReattempt($quizAttempt['QuizAttempt']['courses_enrollment_id']);
					//Check if current attempt is pass or fail.
					$currentAttempt = $this->QuizAttempt->findById($quizAttempt['QuizAttempt']['id']);
					$currentAttempt['Quiz'] = $this->Quiz->findById($quiz['Quiz']['id'])['Quiz'];
					$this->Training->setQuizSettings($this->Quiz->getQuizSettings());
					$result = $this->Training->getresult($currentAttempt, 'basic'); //result will be 'pass' or 'fail'
					$earnedgrade = grade($currentAttempt['QuizAttempt']['sumgrades'], $currentAttempt['Quiz']['sumgrades'], $currentAttempt['Quiz']['grade']);
					$data = array('QuizAttempt' =>array('id' => $currentAttempt['QuizAttempt']['id'],
																							'total_questions'=> $currentAttempt['Quiz']['question_count'],
																							'result'=> $result,
																							'earned_grade' => float($earnedgrade)
																					)
												);
					if($result=='pass'){ //if result is pass Quizattempt should not locked
						$data['QuizAttempt']['locked'] = false;
						}

					$this->QuizAttempt->save($data);  //update result in Quiz Attempt

					$this->Course->markEnrollmentAsCompleted($this->Course->id, $quizAttempt['QuizAttempt']['courses_enrollment_id'], $userid, $result);//exit;
					$this->Quiz->id = $quizAttempt['QuizAttempt']['quiz_id'];
					$this->log("##authuser## completed a test ##action-review/{$quizAttempt['QuizAttempt']['id']}?course_id={$this->Course->id}##", 'system');
					$this->Session->setFlash(__('Test attempt was saved.'));
					return $this->redirect(array('action'=>'view', $quiz['Quiz']['id'], '?'=>array('course_id'=>$quiz['Quiz']['course_id'])));
				}	else	{
					die("Error occured while marking an attempt as finished");
				}
			}
		
			switch($this->request->data['QuizAttempt']['currentpage']):
				case "0":
					//debug($this->request->data['QuizAttempt']);
					//exit;
					//$this->Quiz->set( array('QuizAttempt'=>$this->request->data['QuizAttempt'] ));
					$quizAttempt = $this->Quiz->initQuizAttempt($this->request->data, $reattempt);
					$this->Quiz->id = $this->request->data['QuizAttempt']['quiz_id'];
					if($reattempt) 
							$this->log("##authuser## started re-attempt to test ##action-review/{$quizAttempt['QuizAttempt']['id']}?course_id={$this->Course->id}##", 'system');
					else 
							$this->log("##authuser## started attempt to test ##action-review/{$quizAttempt['QuizAttempt']['id']}?course_id={$this->Course->id}##", 'system');
					$this->set(compact('quizAttempt'));
					
				break;
				case 'continue_attempt':
					if(isset($_GET['page'])){
						$this->QuizAttempt->id = $quizAttempt['QuizAttempt']['id'];
						$this->QuizAttempt->saveField('currentpage', (int)$_GET['page']);
						$quizAttempt['QuizAttempt']['currentpage'] = (int)$_GET['page'];
						//pr($quizAttempt); exit;
						}
					//pr($quizAttempt); exit;
					// if current page is greater than Last page, reset it to last page.
					if($this->Quiz->isLastpage($quizid, $quizAttempt['QuizAttempt']['currentpage'])){
						$this->QuizAttempt->id = $quizAttempt['QuizAttempt']['id'];
						$this->QuizAttempt->saveField('currentpage', (int)$this->Quiz->getLastpage($quizid));
						$quizAttempt['QuizAttempt']['currentpage'] = (int)$this->Quiz->getLastpage($quizid);
					}
				break;
				default:
					//pr($this->request->data); exit;
					foreach($this->request->data['QuestionAttempt'] as $i=>$qAttempt)	{
						$rightanswer = $this->Question->getRightAnswer($qAttempt['question_id']);
						$this->request->data['QuestionAttempt'][$i]['rightanswer'] = $rightanswer;
						
						//in case of multiple choice-multi answers it will be an array of selelction
						if(is_array($this->request->data['QuestionAttempt'][$i]['responsesummary']))	{
							$this->request->data['QuestionAttempt'][$i]['responsesummary'] = implode(';', $this->request->data['QuestionAttempt'][$i]['responsesummary']);
						}
						if(isset($this->request->data['QuestionAttempt'][$i]['pairmatchresponse'])){ //pairmatchresponse
							$this->request->data['QuestionAttempt'][$i]['responsesummary'] = serialize($this->request->data['QuestionAttempt'][$i]['pairmatchresponse']);
							unset($this->request->data['QuestionAttempt'][$i]['pairmatchresponse']);
						}
					}

					//debug($this->request->data);
					//exit;

					$this->Question->bindModel(array('hasMany'=>array('QuestionAttempt')));
					if($this->Question->QuestionAttempt->saveAll($this->request->data['QuestionAttempt']))	{

						//debug($this->Quiz->isLastpage($quizid, $quizAttempt['QuizAttempt']['currentpage']));
						//exit;
						
						if(!$this->Quiz->isLastpage($quizid, $quizAttempt['QuizAttempt']['currentpage']))	{

							$data = array(
								'id'=>$this->request->data['QuizAttempt']['id'],
								//'attempt'=>$this->request->data['QuizAttempt']['attempt']+1,
							
								'currentpage'=>$this->request->data['QuizAttempt']['currentpage']+1
							);
							//debug($data);
							$this->QuizAttempt->save($data);
						}	else	{
							return $this->redirect(array('controller'=>'quizzes', 'action'=>'summary', $quizAttempt['QuizAttempt']['id'], 'admin'=>false));
						}

						//exit;
						$quizAttempt = $this->QuizAttempt->findById($quizAttempt['QuizAttempt']['id']);
						//$this->QuizAttempt->wasLastpage($this->request->data['QuizAttempt']['currentpage']+1);
					}
				break;
			endswitch;
		}	else	{
			if($quizAttempt)	{
				$this->set('continue_last_attempt', 1);
			}
		}
		if(isset($quizAttempt))	{ 

		//debug($quizAttempt); 
			$questions = $this->Quiz->getQuizQuestions($quizAttempt['QuizAttempt']['quiz_id'], $quizAttempt['QuizAttempt']['currentpage']);
			//debug($questions);
			//pr($questions);
			$this->set(compact('questions'));
			$this->request->data['QuizAttempt'] = $quizAttempt['QuizAttempt'];

			$questionAttempt = null;

			//debug($this->request->data);

			if(isset($this->request->data['QuizAttempt']['id']))	{
				$queAoptions = array('conditions' => array('QuestionAttempt.quiz_attempt_id'=>$this->request->data['QuizAttempt']['id']));
				$questionAttempts = $this->QuestionAttempt->find('all', $queAoptions);
				foreach($questionAttempts as $questionAttempt)	{
					$this->request->data['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']] = $questionAttempt['QuestionAttempt'];
				}
			}

			/*if(!$this->Quiz->isInprogress($quizid, $userid, $quizAttempt['QuizAttempt']['currentpage']))	{

			}*/

		}	else	{
			$this->request->data['QuizAttempt']['quiz_id'] = $quizid;
			$this->request->data['QuizAttempt']['user_id'] = $userid;

			//debug($quizAttempt); debug($this->request->data); exit; 
		}
		//debug($questionAttempt);
		
		$this->set(compact('quiz', 'quizAttempt', 'questionAttempt'));
	}

/**
 * admin_review method
 *
 * @param string $id
 * @return void
 */
	public function admin_review($quiz_attempt_id) {
	/*	if($this->request->is(array('post', 'put')) && isset($this->request->data['Quiz']['pdf_html'])){
			$this->layout= 'pdf';
					
					// initializing mPDF
					 $this->Mpdf->init();
					 $this->Mpdf->debug = true;
							//debug($this->request->data); exit;
						// setting filename of output pdf file
					$this->Mpdf->setFilename($this->request->data['User']['full_name'].'-'.$this->request->data['Quiz']['name'].'.pdf');
						// setting output to I, D, F, S
						$this->set('pdf_html', $this->request->data['Quiz']['pdf_html']);
						$this->Mpdf->setOutput('D');
						$this->render('review_pdf');
			}else{ */

				$isAdmin = true;
				$this->review($quiz_attempt_id, $isAdmin);

				if(isset($this->request->query['export']) && $this->request->query['export']=='pdf'){
					$this->layout= 'pdf';
					// initializing mPDF
					 $this->Mpdf->init();
					 $this->Mpdf->debug = true;
						// setting filename of output pdf file
					$this->Mpdf->setFilename($this->request->data['User']['full_name'].'-'.$this->request->data['Quiz']['name'].'.pdf');
						// setting output to I, D, F, S
						$this->Mpdf->setOutput('D');
				}
				$this->render('review');
		//}
	}

	function review($quiz_attempt_id = null, $isAdmin = false )	{

		$this->loadModel('Training.QuizAttempt');

		//To load other models
		$this->loadModel('Training.QuestionAttempt');
		$quizAttemptId = 'quiz_attempt_id';
		if(isset($this->request->query['test']) && $this->request->query['test']){
				$this->loadModel('Training.TestQuizAttempt');
				$this->loadModel('Training.TestQuestionAttempt');

				$this->QuizAttempt = $this->TestQuizAttempt;
				$this->QuestionAttempt = $this->TestQuestionAttempt;
				$quizAttemptId = 'test_quiz_attempt_id';
				unset($this->TestQuizAttempt);
				unset($this->TestQuestionAttempt);
						}
		//pr($this->QuizAttempt); exit;
		$this->QuizAttempt->id = $quiz_attempt_id;

		if (!$this->QuizAttempt->exists()) {
			throw new NotFoundException(__('Invalid quiz attempt'));
		}
		//debug($isAdmin);
		$options = $isAdmin ? array('conditions'=>array(''.$this->QuizAttempt->alias.'.id'=>$quiz_attempt_id)) : array('conditions'=>array(''.$this->QuizAttempt->alias.'.id'=>$quiz_attempt_id, ''.$this->QuizAttempt->alias.'.archived' => 0));
		//debug($options); exit;
		$this->QuizAttempt->bindModel(array('belongsTo'=>array('Quiz', 'User')));
		if($isAdmin) $this->QuizAttempt->setArchived(true);
		$quizAttempt = $this->QuizAttempt->find('first', $options);
		if(!$isAdmin && empty($quizAttempt)){
			$this->Session->setFlash(__('You are not authorized to see previous test attempts!!'));
			return $this->redirect( $this->referer() );
			}
		//debug($quizAttempt); //exit;
		//if($this->QuizAttempt->alias == 'QuizAttempt') $this->QuizAttempt->updateAttemptSumgrades($quizAttempt[$this->QuizAttempt->alias]['id']);
		//$queAoptions = array('conditions' => array('QuestionAttempt.quiz_attempt_id'=>$quiz_attempt_id), 'order'=>array('QuestionAttempt.slot ASC'));

		$this->loadModel('Training.Question');

		//$this->QuestionAttempt->bindModel(array('belongsTo'=>array('Question')));
		//$questionAttempt = $this->QuestionAttempt->find('all', $queAoptions);

			//debug($quizAttempt);
			$qoptions['joins'] = array(
				array('table' => 'quiz_slots',
					'alias' => 'QuizSlot',
					'type' => 'LEFT',
					'conditions' => array(
							'QuizSlot.question_id = Question.id',
					)
				)
			);

			$qoptions['fields'] = array('Question.*', 'QuizSlot.page', 'QuizSlot.slot', 'QuizSlot.quiz_id', 'QuestionType.*');

			$qoptions['conditions'] = array(
				'QuizSlot.quiz_id' => $quizAttempt[$this->QuizAttempt->alias]['quiz_id']
			);
			//$this->Question->unbindModel(array('hasMany'=>array('QuestionAnswer')));
			$questions = $this->Question->find('all', $qoptions);
			//debug($questions);
			//$this->set(compact('questions'));
			//$this->request->data['QuizAttempt'] = $quizAttempt['QuizAttempt'];

			$questionAttempt = null;

			//debug($this->request->data);
			
			if(isset($quizAttempt[$this->QuizAttempt->alias]['id']))	{
				$queAoptions = array('conditions' => array($this->QuestionAttempt->alias.'.'.$quizAttemptId =>$quizAttempt[$this->QuizAttempt->alias]['id']), 'order'=>array($this->QuestionAttempt->alias.'.slot ASC'));
				$questionAttempts = $this->QuestionAttempt->find('all', $queAoptions);
				foreach($questionAttempts as $i=>$questionAttempt)	{
					$quizAttempt[$this->QuestionAttempt->alias][$questionAttempt[$this->QuestionAttempt->alias]['question_id']] = $questionAttempt[$this->QuestionAttempt->alias];
					foreach($questions as $question)	{

						if($questionAttempt[$this->QuestionAttempt->alias]['question_id']==$question['Question']['id'])	{
							$quizAttempt[$this->QuestionAttempt->alias][$questionAttempt[$this->QuestionAttempt->alias]['question_id']] = array_merge($quizAttempt[$this->QuestionAttempt->alias][$questionAttempt[$this->QuestionAttempt->alias]['question_id']], $question);
							break;
						}
					}
					$quizAttempt[$this->QuestionAttempt->alias][$questionAttempt[$this->QuestionAttempt->alias]['question_id']]['answeroutcome'] = $this->QuestionAttempt->answerOutcome($quizAttempt[$this->QuestionAttempt->alias][$questionAttempt[$this->QuestionAttempt->alias]['question_id']]);
					//debug($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
					$quizAttempt[$this->QuestionAttempt->alias][$questionAttempt[$this->QuestionAttempt->alias]['question_id']]['sumgrades'] = $this->QuizAttempt->calcQuestionGrades($quizAttempt[$this->QuestionAttempt->alias][$questionAttempt[$this->QuestionAttempt->alias]['question_id']]);
					//debug($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
				}
				//debug($quizAttempt);
			}

			//calcQuestionGrades
			$quizAttempt['QuizAttempt'] = $quizAttempt[$this->QuizAttempt->alias];
			$quizAttempt['QuestionAttempt'] = $quizAttempt[$this->QuestionAttempt->alias];
			$this->request->data = $quizAttempt;

		//debug($quizAttempt);

		$this->set(compact('quizAttempt'));
	}
/**
 * admin_settings method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_settings($id = null) {
		if (!$this->Quiz->exists($id)) {
			throw new NotFoundException(__('Invalid quiz'));
		}
		if ($this->request->is(array('post', 'put'))) {
				$this->Quiz->id = $id;
				$total_questions = $this->Quiz->field('question_count');
			if($total_questions < $this->request->data['Quiz']['minpassquestions']){
					$this->Session->setFlash(__('Min. passing questions must be less than total questions in the Quiz. Please, try again.'));
					return $this->redirect($this->referer());
				}
			if ($this->Quiz->save($this->request->data['Quiz'])) {
				$this->Course->updateMinpassQuestionCount( $this->Course->id );
				$this->Session->setFlash(__('Quiz settings has been saved.'));
				$this->log("##authuser## updated quiz settings ##action-view## settings", 'system');
				return $this->redirect(array('action' => 'index', $id));
			} else {
				$this->Session->setFlash(__('The quiz could not be saved. Please, try again.'));
			}
		}
		
		//else {
		$options = array('conditions' => array('Quiz.' . $this->Quiz->primaryKey => $id), 'recursive'=>-1);

		//$this->loadModel('Training.QuestionType');
		//$questionTypes = $this->QuestionType->find('list');
		//$this->set(compact('questionTypes'));
		$this->request->data = $this->Quiz->find('first', $options);
			//debug($this->request->data);
		//}
	}

/**
 * reattempt quiz
 *
 * @throws NotFoundException for Invalid quiz
 * @param string $id
 * @return void
 */
	function reattempt($id)	{
		if (!$this->Quiz->exists((int)$id)) {
			throw new NotFoundException(__('Invalid quiz'));
		}
		$this->Quiz->id = $id;
		if($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['QuizAttempt']['reattempt_yes']))	{
				//debug($this->request->data);
				//exit;
				$this->loadModel('Training.QuizAttempt');
				$this->QuizAttempt->id = $this->request->data['QuizAttempt']['id'];
				$this->QuizAttempt->saveField('archived', 1);
				$this->QuizAttempt->saveField('locked', 1);

				$quizAttempt = $this->Quiz->initQuizAttempt($this->request->data, true); //true means it is a re-attempt
				$this->log("##authuser## started re-attempt to test ##action-review/{$quizAttempt['QuizAttempt']['id']}?course_id={$this->Course->id}##", 'system');
				return $this->redirect(array('action' => 'attempt', $this->request->data['QuizAttempt']['quiz_id']));
			}
		}	else	{
			throw new NotFoundException("Invalid request");
		}
	}

	function admin_edit_marks($question_attempt_id)	{
		$this->layout = 'ajax';

		$this->loadModel('Training.QuestionAttempt');
		$questionAttempt = $this->QuestionAttempt->find('first', array('conditions'=>array('id'=>$question_attempt_id)));

		$questionAttempt['QuestionAttempt']['questiontext'] = 
		substr($questionAttempt['QuestionAttempt']['questionsummary'], 0, strpos($questionAttempt['QuestionAttempt']['questionsummary'], ':'));

		if($this->request->is(array('post', 'put'))) {
			if($this->QuestionAttempt->save($this->request->data))	{
				$this->loadModel('Training.QuizAttempt');
				$this->QuizAttempt->updateAttemptSumgrades($questionAttempt['QuestionAttempt']['quiz_attempt_id']);
				$this->log(sprintf("##authuser## updated marks for a question %s settings", $questionAttempt['QuestionAttempt']['questiontext'] . '(#'.$questionAttempt['QuestionAttempt']['question_id'].')'), 'system');
				$this->Session->setFlash(__('Marks updated.'));
				return $this->redirect($this->referer());
			}
		}	else	{
			$this->request->data = $questionAttempt;
		}

		$minfractions = $this->QuestionAttempt->fractionList();		
		$this->set(compact('minfractions', 'questionAttempt'));
	}

/**
 * Request unlock a quiz attempt
 *
 * @param string $attemptid
 * @return void
 */
	public function requestunlock($attemptid){
		$this->loadModel('Training.QuizAttempt');
		$this->loadModel('User');
		$this->QuizAttempt->id = $attemptid;
		if (!$this->QuizAttempt->exists()) {
			throw new NotFoundException(__('Invalid quiz attempt'));
		}

		if($this->request->is('ajax'))	{
			$this->layout = 'ajax';
		}

		//debug($this->request->data);

		$quizAttempt = $this->QuizAttempt->find('first', array('conditions'=>array('QuizAttempt.id'=>$attemptid), 'recursive'=>2, 'QuizAttempt.archived' => 0));

		//debug($this->request->data);

		if($this->request->is(array('post', 'put'))) {
			if($this->request->data['QuizAttempt']['request_unlock'])	{
				//debug("something");
				$this->loadModel('Training.CoursesEnrollment');
				$creator_id = $this->CoursesEnrollment->field('creator_id', array('CoursesEnrollment.' . $this->CoursesEnrollment->primaryKey => $quizAttempt['QuizAttempt']['courses_enrollment_id']));
				$to = '';
				switch(__SYSTEM_QUIZ_UNLOCK_REQUEST_RECIPIENTS){
					case 'supervisor':
						$to = $this->User->getSupervisorEmailID($quizAttempt['quizAttempt']['user_id']);
						break;
					case 'enroller':
						$to = $this->User->getEmailAddressByUserId($creator_id);
						break;
					case 'administrators':
						$to = $this->User->getEmailsByRolename('administrator');
						break;
					//case '':
							//$to = explode(',', __SYSTEM_QUIZ_UNLOCK_REQUEST_RECIPIENTS);
						//break;
					default:
					if(__SYSTEM_QUIZ_UNLOCK_REQUEST_RECIPIENTS != '')
							$to = explode(',', __SYSTEM_QUIZ_UNLOCK_REQUEST_RECIPIENTS);
					}
				if(!$to) $to = $this->User->getEmailAddressByUserId($creator_id);
				if($to)
					$this->__Email($to, 'Quiz Attempt unlock requested', 'Training.quizattempt-unlock', array('course'=>$this->Course->read(), 'quizAttempt' => $quizAttempt['QuizAttempt']));

				$this->QuizAttempt->saveField('unlock_requested', 1);
				$this->Quiz->id = $this->QuizAttempt->field('quiz_id');
				$this->log("##authuser## requested to unlock quiz attempt ##action-review/{$this->QuizAttempt->id}?course_id={$this->Course->id}##", "system");
				$this->Session->setFlash(__('You have requested to unlock quiz attempt. You will be notified soon.'));
				return $this->redirect($this->referer());
			}
		}
		$this->request->data = $quizAttempt;
	}

/**
 * Change unlock of a quiz attempt
 *
 * @param string $attemptid
 * @return void
 */
	public function admin_switchlock($attemptid)	{
		$this->loadModel('Training.QuizAttempt');
		$this->loadModel('User');
		$this->QuizAttempt->id = $attemptid;
		if (!$this->QuizAttempt->exists()) {
			throw new NotFoundException(__('Invalid quiz attempt'));
		}

		if($this->request->is('ajax'))	{
			$this->layout = 'ajax';
		}

		$this->QuizAttempt->bindModel(array('belongsTo'=>array('User')));
		$quizAttempt = $this->QuizAttempt->find('first', array('conditions'=>array('QuizAttempt.id'=>$attemptid), 'recursive'=>0, 'QuizAttempt.archived' => 0));

		if($this->request->is(array('post', 'put'))) {
			if(!$this->request->data['QuizAttempt']['locked'])	{
				//$this->__setEmailCC($this->User->getEmailsByRolename('administrator'));
				$this->__Email($quizAttempt['User']['email_address'], 'Requested Quiz Attempt unlocked ', 'Training.quizattempt-unlocked', array('course'=>$this->Course->read(), 'quizAttempt' => $quizAttempt));
				$this->QuizAttempt->saveField('unlock_requested', 0);
				$this->QuizAttempt->saveField('unlocked', 1);
				$this->Quiz->id = $this->QuizAttempt->field('quiz_id');
				$this->log("##authuser## unlocked a quiz attempt ##action-review/{$this->QuizAttempt->id}?course_id={$this->Course->id}##", "system");
			}
			$this->QuizAttempt->saveField('locked', $this->request->data['QuizAttempt']['locked']);
			$this->Session->setFlash(__('Quiz locked change.'));
			return $this->redirect( $this->referer() );
		}
		$this->request->data = $quizAttempt;
	}

/**
 * admin_purge method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_purge( $id ) {
		$this->Quiz->id = $id;
		if (!$this->Quiz->exists()) {
			throw new NotFoundException(__('Invalid quiz'));
		}
		$this->request->allowMethod('post', 'delete');

		//$this->Quiz->beforePurge( $id );

		if ($this->Quiz->delete( $id )) {
      $this->log("##authuser## permanently deleted a quiz #" . $id, 'system');
			$this->Session->setFlash(__('Quiz has permanently been deleted.'));
		} else {
			$this->Session->setFlash(__('The quiz could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index', '?'=>array('course_id'=>$this->Course->id)));
	}


/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Quiz->exists((int)$id)) {
			throw new NotFoundException(__('Invalid quiz'));
		}

		$exception = false;

		$this->loadModel('Training.QuizAttempt');
		$options = array('conditions' => array('Quiz.' . $this->Quiz->primaryKey => $id));

		$quiz = $this->Quiz->find('first', $options);
	
		//pr($quiz);


		//if( !$exception )	{

			//set quizAttempt options, to retrieve any existing attempt for this quiz
			$qaoptions = array('conditions'=>array('QuizAttempt.quiz_id'=>$id));

			//look for quizAttempt
			$quizAttemptUsers = $this->QuizAttempt->getQuizAttemptUsers($id, $qaoptions);
			//$quizAttemptUsers = $this->QuizAttempt->find('all', $qaoptions);
			$this->QuizAttempt->setArchived(true);
			$quiz['user_count'] = $this->QuizAttempt->find('count', $qaoptions);
			//pr($quizAttemptUsers);
		

			$this->set(compact('quiz'));
			$this->set( compact('quizAttemptUsers') );
		//}

		$this->set( compact('exception') );
	}



	/**
 * admin_testview method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_testview($id = null) {
		if (!$this->Quiz->exists((int)$id)) {
			throw new NotFoundException(__('Invalid quiz'));
		}

		$exception = false;

		$this->loadModel('Training.TestQuizAttempt');
		$options = array('conditions' => array('Quiz.' . $this->Quiz->primaryKey => $id));
		$quiz = $this->Quiz->find('first', $options);
	
		//pr($quiz);


		//if( !$exception )	{

			//set quizAttempt options, to retrieve any existing attempt for this quiz
			$qaoptions = array('conditions'=>array('TestQuizAttempt.quiz_id'=>$id, 'TestQuizAttempt.archived' => 0));

			//look for quizAttempt
			$quizAttemptUsers = $this->TestQuizAttempt->getTestQuizAttemptUsers($id, $qaoptions);
			//$quizAttemptUsers = $this->TestQuizAttempt->find('all', $qaoptions);
			$quiz['user_count'] = $this->TestQuizAttempt->find('count', $qaoptions);
			//pr($quizAttemptUsers);
		

			$this->set(compact('quiz'));
			$this->set( compact('quizAttemptUsers') );
		//}

		$this->set( compact('exception') );
	}

/**
 * admin_history method
 *
 * @throws NotFoundException
 * @param string $id $user_id
 * @return void
 */
	public function admin_history($id = null, $user_id = null){
			if (!$this->Quiz->exists($id)) {
					throw new NotFoundException(__('Invalid quiz'));
				}

				$exception = false;

				$this->loadModel('Training.QuizAttempt');
				$options = array('conditions' => array('Quiz.' . $this->Quiz->primaryKey => $id));
				$quiz = $this->Quiz->find('first', $options);
	
		$qaoptions = array('conditions'=>array('QuizAttempt.quiz_id'=>$id, 'QuizAttempt.user_id'=>$user_id), 'order' => array('QuizAttempt.archived ASC'));
		$this->QuizAttempt->setArchived(true);
		$quizUserAttempts = $this->QuizAttempt->getQuizAttemptUsers($id, $qaoptions);
		$this->QuizAttempt->setArchived(true);
		$quiz['user_count'] = $this->QuizAttempt->find('count', $qaoptions);
	
		$this->set(compact('quiz'));
		$this->set( compact('quizUserAttempts'));
	
	$this->set( compact('exception') );
	
	}

/**
 * admin_attempt method
 *
 * Method to take test
 *
 * @param string $enrollmentid
 * @return void
 */
	public function admin_testattempt($quizid = null) {

		//$this->loadModel('Training.Course');
		$this->loadModel('Training.TestQuizAttempt');
		$this->loadModel('Training.Question');
		$this->loadModel('Training.TestQuestionAttempt');

		$userid = $this->Auth->user('id'); //current logged in User
		$options = array('conditions'=>array('Quiz.id' => $quizid), 'recursive'=>-1, 'fields'=>array('id', 'name', 'course_id')); //find quiz $options
		$quiz = $this->Quiz->find('first', $options);

		$this->set(compact('quiz'));
			//pr($quiz);
		//if no quiz found die on error!
		if(!$quiz)	{
			throw new NotFoundException(__('Invalid Test/Quiz Request'));
		}

		//Check whether this quiz has questions to be attempted!
		if(!$this->Quiz->hasQuestions($quizid))	{
			return $this->render('no_quiz_questions');
		}

			/*
		if(!$this->request->data){
		$this->request->data['QuizAttempt']['currentpage'] = 0;
		$this->request->data['QuizAttempt']['quiz_id'] = $quizid;
		$this->request->data['QuizAttempt']['user_id'] = $userid;
		} */


		//$courseid = $this->Course->getCourseIdByQuizId($quizid);
		//$verifyEnrol = $this->Course->verifyEnrol($userid, $quiz['Quiz']['course_id']);
		
		//if(!$verifyEnrol)	{
			//throw new NotFoundException(__('You are not enrolled to this course'));
		//}

		//set quizAttempt options, to retrieve any existing attempt for this quiz
		$qaoptions = array('conditions'=>array('TestQuizAttempt.user_id'=>$userid, 'TestQuizAttempt.quiz_id'=>$quizid, 'TestQuizAttempt.archived' => 0));

		//look for quizAttempt
		$quizAttempt = $this->TestQuizAttempt->find('first', $qaoptions);
		//debug($quizAttempt);
		//pr($this->request->data);
		if( !isset($quizAttempt['TestQuizAttempt']) )	{
				$this->request->data['TestQuizAttempt']['id'] = null;
				$this->request->data['TestQuizAttempt']['quiz_id'] = $quizid;
				$this->request->data['TestQuizAttempt']['user_id'] = $userid;
				$this->request->data['TestQuizAttempt']['currentpage'] = 0;
			}
				else	{
				if(!$this->request->data){
				$this->request->data = $quizAttempt;
				}
			}
	
		//pr($this->request->data);
		//debug($quizAttempt); 
		//exit;
		if ($this->request->is(array('post', 'put'))) {
			//debug($this->request->data); //exit;
			$reattempt = false;
			
			if(isset($this->request->data['TestQuizAttempt']['reattempt_yes']) && $quizAttempt['TestQuizAttempt']['state']=='finished')	{
					//debug($this->request->data);
					$this->TestQuizAttempt->id = $this->request->data['TestQuizAttempt']['id'];
					$this->TestQuizAttempt->saveField('archived', 1);
					//$quizAttempt = $this->Quiz->initQuizAttempt($this->request->data, true); //true means it is a re-attempt
					$this->request->data['TestQuizAttempt']['currentpage'] = 0;
					$reattempt = true;
				}
			

			if(isset($this->request->data['TestQuizAttempt']['mark_as_finised']))	{

				if($this->TestQuizAttempt->markAsFinished( $quizAttempt['TestQuizAttempt']['id'] ))	{
										//Check if current attempt is pass or fail.
					$currentAttempt = $this->TestQuizAttempt->findById($quizAttempt['TestQuizAttempt']['id']);
					$currentAttempt['Quiz'] = $this->Quiz->findById($quiz['Quiz']['id'])['Quiz'];
					$this->Training->setQuizSettings($this->Quiz->getQuizSettings());
					$result = $this->Training->getresult($currentAttempt, 'basic'); //result will be 'pass' or 'fail'
					$earnedgrade = grade($currentAttempt['TestQuizAttempt']['sumgrades'], $currentAttempt['Quiz']['sumgrades'], $currentAttempt['Quiz']['grade']);
					$data = array('TestQuizAttempt' =>array('id' => $currentAttempt['TestQuizAttempt']['id'],
																							'total_questions'=> $currentAttempt['Quiz']['question_count'],
																							'result'=> $result,
																							'earned_grade' => float($earnedgrade)
																					)
												);
					$this->TestQuizAttempt->save($data);  //update result in Quiz Attempt

					$this->log("##authuser## completed a test attempt ##action-view##", 'system');
					$this->Session->setFlash(__('Test attempt was saved.'));
					return $this->redirect(array('action'=>'testview', $quiz['Quiz']['id'], '?'=>array('course_id'=>$quiz['Quiz']['course_id'])));
				}	else	{
					die("Error occured while marking an attempt as finished");
				}
			}

			switch($this->request->data['TestQuizAttempt']['currentpage']):
				case "0":
					//debug($this->request->data['QuizAttempt']);
					//exit;
					//$this->Quiz->set( array('QuizAttempt'=>$this->request->data['QuizAttempt'] ));
					//pr($this->request->data); exit;
					$quizAttempt = $this->Quiz->initTestQuizAttempt($this->request->data, $reattempt);
					$this->set(compact('quizAttempt'));
					//exit;
				break;
				case 'continue_attempt':
					if(isset($_GET['page']))	{
						$this->TestQuizAttempt->id = $quizAttempt['TestQuizAttempt']['id'];
						$this->TestQuizAttempt->saveField('currentpage', (int)$_GET['page']);
						$quizAttempt['TestQuizAttempt']['currentpage'] = (int)$_GET['page'];
					}
				break;
				default:
					foreach($this->request->data['TestQuestionAttempt'] as $i=>$qAttempt)	{
						$rightanswer = $this->Question->getRightAnswer($qAttempt['question_id']);
						$this->request->data['TestQuestionAttempt'][$i]['rightanswer'] = $rightanswer;

						//in case of multiple choice-multi answers it will be an array of selelction
						if(is_array($this->request->data['TestQuestionAttempt'][$i]['responsesummary']))	{
							$this->request->data['TestQuestionAttempt'][$i]['responsesummary'] = implode(';', $this->request->data['TestQuestionAttempt'][$i]['responsesummary']);
						}
					if(isset($this->request->data['TestQuestionAttempt'][$i]['pairmatchresponse'])){ //pairmatchresponse
							$this->request->data['TestQuestionAttempt'][$i]['responsesummary'] = serialize($this->request->data['TestQuestionAttempt'][$i]['pairmatchresponse']);
							unset($this->request->data['TestQuestionAttempt'][$i]['pairmatchresponse']);
						}
					}

					//debug($this->request->data);
					//exit;

					$this->Question->bindModel(array('hasMany'=>array('TestQuestionAttempt')));
					if($this->Question->TestQuestionAttempt->saveAll($this->request->data['TestQuestionAttempt']))	{

						//debug($this->Quiz->isLastpage($quizid, $quizAttempt['QuizAttempt']['currentpage']));
						//exit;
						
						if(!$this->Quiz->isLastpage($quizid, $quizAttempt['TestQuizAttempt']['currentpage']))	{
							$data = array(
								'id'=>$this->request->data['TestQuizAttempt']['id'],
								//'attempt'=>$this->request->data['QuizAttempt']['attempt']+1,
								'currentpage'=>$this->request->data['TestQuizAttempt']['currentpage']+1
							);
							//debug($data);
							$this->TestQuizAttempt->save($data);
						}	else	{
								
							return $this->redirect(array('controller'=>'quizzes', 'action'=>'summary', $quizAttempt['TestQuizAttempt']['id']));
						}

						//exit;
						$quizAttempt = $this->TestQuizAttempt->getTestUserAttempt(
							$this->request->data['TestQuizAttempt']['quiz_id'], 
							$this->request->data['TestQuizAttempt']['user_id']
						);
						//$this->QuizAttempt->wasLastpage($this->request->data['QuizAttempt']['currentpage']+1);
					}
				break;
			endswitch;
		}	else	{
				if($quizAttempt)	{
				if($quizAttempt['TestQuizAttempt']['state']=='finished'){
					$this->redirect(array('controller'=>'quizzes', 'action'=>'testview', $quiz['Quiz']['id'], '?'=>array('course_id'=>$quiz['Quiz']['course_id'])));
				}
				$this->set('continue_last_attempt', 1);
				//exit;
			}
		}
		//debug($this->request->data['TestQuizAttempt']['currentpage']); 
		if( $this->request->data['TestQuizAttempt']['currentpage'] == '0'){ //echo "Current page is 0";
		//exit;
		$quizAttempt = $this->Quiz->initTestQuizAttempt($this->request->data, $reattempt = false); 
		$this->set(compact('quizAttempt'));
		}
		if(isset($quizAttempt['TestQuizAttempt']))	{
		//debug($quizAttempt);  exit;
			$questions = $this->Quiz->getTestQuizQuestions($quizAttempt['TestQuizAttempt']['quiz_id'], $quizAttempt['TestQuizAttempt']['currentpage']);
			//$questions = $this->Quiz->getQuizQuestions($quizid, 1);
			//debug($questions);
			//exit;
			$this->set(compact('questions'));
			$this->request->data['TestQuizAttempt'] = $quizAttempt['TestQuizAttempt'];

			$questionAttempt = null;

			//debug($this->request->data);

			if(isset($this->request->data['TestQuizAttempt']['id']))	{
				$queAoptions = array('conditions' => array('TestQuestionAttempt.test_quiz_attempt_id'=>$this->request->data['TestQuizAttempt']['id']));
				$questionAttempts = $this->TestQuestionAttempt->find('all', $queAoptions);
				foreach($questionAttempts as $questionAttempt)	{
					$this->request->data['TestQuestionAttempt'][$questionAttempt['TestQuestionAttempt']['question_id']] = $questionAttempt['TestQuestionAttempt'];
				}
			}

			/*if(!$this->Quiz->isInprogress($quizid, $userid, $quizAttempt['QuizAttempt']['currentpage']))	{

			}*/

		}	else	{
			$this->request->data['TestQuizAttempt']['quiz_id'] = $quizid;
			$this->request->data['TestQuizAttempt']['user_id'] = $userid;
		}

		

		//debug($this->request->data);
		//exit;
		$this->set(compact('quiz', 'quizAttempt', 'questionAttempt'));
		//$this->render('attempt');
	}


	public function admin_summary( $id ) {
		//$this->loadModel('Training.Quiz');
		$this->loadModel('Training.Course');
		$this->loadModel('Training.TestQuizAttempt');
		$this->loadModel('Training.Question');
		$this->loadModel('Training.TestQuestionAttempt');

		//debug($id); exit;
		$this->TestQuizAttempt->bindModel(array('belongsTo'=>array('Quiz')));
		$quizAttempt = $this->TestQuizAttempt->find('first', array('conditions'=>array('TestQuizAttempt.id'=>$id, 'TestQuizAttempt.archived' => 0)));
		//debug($quizAttempt);
		$this->request->data = $quizAttempt;
		
		$course = $this->Course->find('first', array('conditions'=>array('Course.id'=>$quizAttempt['Quiz']['course_id']), 'recursive'=>-1, 'fields'=>array('Course.name', 'Course.id', 'Course.signature')));
		//debug($course );

		$this->TestQuestionAttempt->bindModel(array('belongsTo'=>array('Question')));
		$questionAttempt = $this->TestQuestionAttempt->find('all', array('conditions'=>array('TestQuestionAttempt.test_quiz_attempt_id'=>$id), 'order'=>array('TestQuestionAttempt.slot ASC')));

		//debug($questionAttempt);

		$course_id = $quizAttempt['Quiz']['course_id'];
		$this->set(compact('course', 'quizAttempt', 'questionAttempt', 'course_id'));
	}

/**
 * aunlock all locked quiz attempt
 *
 * @param void
 * @return void
 */
	public function admin_switchAllLock(){
		if($this->request->is(array('post', 'put'))) {
			if($this->request->data['QuizAttempt']['unlock_ids']){
					$this->loadModel('Training.QuizAttempt');
					$this->loadModel('User');
					$adminEmials = $this->User->getEmailsByRolename('administrator');
					$locked_ids = explode(',', $this->request->data['QuizAttempt']['unlock_ids']);
					foreach($locked_ids as $attemptid){
						$this->QuizAttempt->id = $attemptid;
						$this->QuizAttempt->saveField('unlock_requested', 0);
						$this->QuizAttempt->saveField('unlocked', 1);
						$this->QuizAttempt->saveField('locked', 0);

						$this->QuizAttempt->bindModel(array('belongsTo'=>array('User')));
						$quizAttempt = $this->QuizAttempt->find('first', array('conditions'=>array('QuizAttempt.id'=>$attemptid), 'recursive'=>0, 'QuizAttempt.archived' => 0));
						$this->__setEmailCC($adminEmials);
						$this->__Email($quizAttempt['User']['email_address'], 'Requested Quiz Attempt unlocked ', 'Training.quizattempt-unlocked', array('course'=>$this->Course->read(), 'quizAttempt' => $quizAttempt));
					}
					$this->log("##authuser## Unlocked multiple quiz attempts for ##Course:{$this->Course->id}##", "system");
					$this->Session->setFlash(__('Quizzes are unlocked.'));
				}else{
				$this->Session->setFlash(__('Quizzes are already unlocked.'));
			}
		return $this->redirect( $this->referer() );
		
		}
	}


	/**
 * admin_question_sequence method
 *
 * @throws NotFoundException
 * @param string $quizid
 * @return void
 */
	public function admin_question_sequence($quizid)	{
		$this->Quiz->id = $quizid;
		if (!$this->Quiz->exists()) {
			throw new NotFoundException(__('Invalid quiz'));
		}

		if ($this->request->is(array('post', 'put'))) {

			foreach($this->request->data['QuizSlot'] as $i=>$entry)	{
				if(!isset($entry['question_id']))	{
					unset($this->request->data['QuizSlot'][$i]);
				}
			}
			if($this->request->data['QuizSlot'])	{
				//debug($this->request->data['QuizSlot']);exit;
				if ($this->Quiz->QuizSlot->saveAll($this->request->data['QuizSlot'])) {
					$this->Quiz->updateSumgrades( $this->Quiz->id );
					$this->Quiz->updateQuestionCount( $this->Quiz->id );
					$this->Course->updateQuizQuestionCount( $this->Course->id );
					$this->Session->setFlash(__('The sequence of questions for quiz has been saved.'));
					$this->log("##authuser## changed sequence of questions for quiz ##Quiz:{$quizid}##", 'system');
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('The sequence of questions for quiz could not be saved. Please, try again.'));
				}
			}
		}

		$this->request->data = $quiz = $this->Quiz->read();

		//debug($quiz); //exit;
		$this->loadModel('Training.Question');
		$options = array('conditions'=>array('Question.course_id'=>$quiz['Quiz']['course_id'], 'Question.deleted'=>false));

		//$this->paginate = $options;
		$questions = $this->Question->find('all', $options);
		$this->set(compact('questions', 'quiz'));
	}



}