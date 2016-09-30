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
 * Courses Controller
 *
 * @property Course $Course
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CoursesController extends TrainingAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Spreadsheet.PhpExcel', 'Mpdf.Mpdf', 'Training.Training', 'Comments.Comments' => array(
			'userModelClass' => 'User', // Customize the User class
			'displayType' => 'threaded'
			)
		/*'GoogleAPI.YouTube' => array(
    'Service' => array(, 'FileUpload.FileUpload', 
        'YouTube'
    )
  )*/
		);
/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('admin_enroluserview'));
	}
/**
 * beforeRender
 *
 * @return void
 */
	public function beforeRender()	{
		$this->set('_quizSettings', $this->Course->Quiz->getQuizSettings());
		parent::beforeRender();
	}

  public $helpers = array('TinyMCE.TinyMCE');

/**
 * index method
 *
 * @return void
 */
	public function admin_index() {
		
		//set custom pagination limit
		$this->_setPaginationLimit('Course');

		$this->Course->recursive = 0;
		$options = array('order'=>'Course.name ASC');
		if(isset($this->request->data['Course']['search']) && trim($this->request->data['Course']['search_keyword']) != ''){
			$options['conditions'] = array('Course.name LIKE'=>'%'.$this->request->data['Course']['search_keyword'].'%');
		}
		$this->paginate = array_merge_recursive($options, $this->paginate);
		
		$this->set('courses', $this->Paginator->paginate());
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		return $this->redirect(array('action' => 'my'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Course->exists($id)) {
			throw new NotFoundException(__('Invalid course'));
		}

		$userid = $this->Auth->user('id');

		$options = array('conditions' => array('Course.' . $this->Course->primaryKey => $id), 'recursive'=>-1);
		$course = $this->Course->find('first', $options);
		//pr($course);
		//$QuizAttempt = ClassRegistry::init('Training.QuizAttempt');

		//debug(get_class($QuizAttempt));exit;
		if(isset($this->request->query['enid'])){
			$enrol_id = $this->request->query['enid'];
			$this->Course->CoursesEnrollment->recursive = -1;
			$current_enrollment = $this->Course->CoursesEnrollment->findById($enrol_id);
		}
		else{
				$current_enrollment = $this->Course->getCurrentEnrollment($userid, $id);
				$enrol_id = $current_enrollment['CoursesEnrollment']['id'];
		 }

		if($this->Course->verifyEnrol( $userid, $id ))	{
			//debug($this->Course->Quiz->getQuizSettings());
			$this->set('_quizSetting', $this->Course->Quiz->getQuizSettings());
			$this->Course->Quiz->bindModel(array('hasOne'=>array('QuizAttempt'=>array('className'=>'Training.QuizAttempt','conditions'=>array('QuizAttempt.user_id'=>$userid, 'QuizAttempt.courses_enrollment_id'=>$enrol_id)))));
			//debug($this->Course->Quiz);exit;
			//$this->Course->Quiz->QuizAttempt->unbindModel()
			$course['Quizzes'] = $this->Course->Quiz->find('all', array('conditions'=>array('Quiz.course_id'=>$id), 'recursive'=>0));
			foreach($course['Quizzes'] as $i=>$quiz)	{
				if( !$quiz['Quiz']['is_default'] )	{
					//debug(get_class($this->Course->Quiz->QuizAttempt));
					if(!$this->Course->Quiz->QuizAttempt->getUserAttempt($quiz['Quiz']['id'], $userid, [], 'count'))	{
						$course['Quizzes'][$i]['Quiz']['no_default_no_attempt'] = '1'; //this is neither default nor user has a previous attempt
					}
				}
			}
		}

		//debug($course['Quizzes']); exit;
		$course['CoursesEnrollment'] = $current_enrollment['CoursesEnrollment'];
		$this->set(compact('course'));
		$this->Course->record_last_access($id, $userid);
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function lesson($id = null) {
		if (!$this->Course->exists($id)) {
			throw new NotFoundException(__('Invalid course'));
		}
		$options = array('conditions' => array('Course.' . $this->Course->primaryKey => $id), 'recursive'=>-1);
		$this->set('course', $this->Course->find('first', $options));
		//$this->Course->record_last_access($id, $this->Auth->user('id'));
	}

/**
 * add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Course->create();
			//debug($this->request->data);
		//	pr($this->request->data);
			
			$course_module = $this->Course->makeCourseModule($this->request->data);
			
			$this->request->data['CourseModule'][] = $course_module;
	//	pr($this->request->data); 
			if ($this->Course->saveAll($this->request->data)) {
				$this->Course->saveField('current_module', $this->Course->CourseModule->id);

				$this->log("##authuser## added new course ##action-view##", "system");
				$this->Session->setFlash(__('The course has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The course could not be saved. Please, try again.'));
			}
		}
    $source_types = $this->Course->getSourceTypes();
    $this->set(compact('source_types'));
	}
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Course->exists($id)) {
			throw new NotFoundException(__('Invalid course'));
		}
		if ($this->request->is(array('post', 'put'))) {
			//debug($this->request->data);exit;
			if ($this->Course->save($this->request->data, false)) {
				//if there there is any modification in course content and user choose to clear course learn log
				if($this->request->data['Course']['clear_course_log']){
						$this->loadModel('Training.CoursesLearnLog');
						$this->CoursesLearnLog->clearCourseLog($this->Course->id);
				}
				$this->log("##authuser## updated course ##action-view##", "system");
				$this->Session->setFlash(__('The course has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				//debug($this->request->data);exit;
				//debug($this->Course->validationErrors);
				$this->Session->setFlash(__('The course could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Course.' . $this->Course->primaryKey => $id), 'recursive'=>1);
			$this->request->data = $this->Course->find('first', $options);
		}
		//debug($this->request->data);
    $source_types = $this->Course->getSourceTypes();
    $this->set(compact('source_types'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Course->id = $id;
		if (!$this->Course->exists()) {
			throw new NotFoundException(__('Invalid course'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Course->delete()) {
			$this->log("##authuser## marked a course ##action-view## as deleted", "system");
			$this->Session->setFlash(__('The course has been deleted.'));
		} else {
			$this->Session->setFlash(__('The course could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_undelete method
 *
 * @description Un-delete a course
 * @param string $id
 * @return redirect
 */

	public function admin_undelete($id) {
		$this->Course->id = $id;
		if (!$this->Course->exists()) {
			throw new NotFoundException(__('Invalid Course'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Course->undelete($id)) {
      $this->log("##authuser## retrieved a course ##action-view##", 'system');
			$this->Session->setFlash(__('Course has been retrieved.'));
		} else {
			$this->Session->setFlash(__('Course could not be retrieved. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_purge method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_purge($id) {
		$this->Course->id = $id;
		if (!$this->Course->exists()) {
			throw new NotFoundException(__('Invalid course'));
		}
		$this->request->allowMethod('post', 'delete');

		//$this->Course->beforePurge( $id );

		if ($this->Course->delete( $id )) {
      $this->log("##authuser## permanently deleted a course #" . $id, 'system');
			$this->Session->setFlash(__('Course has permanently been deleted.'));
		} else {
			$this->Session->setFlash(__('The course could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_enrol method
 *
 * Course assignment screen. Admin can assign, remove users to a course. 
 * Find users by search or list them all to select from
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

	function admin_enrol($id)	{

		$this->Course->id = $id;
		if(!$this->Course->exists())	{
			throw new NotFoundException(__('Invalid course'));
		}
		//$this->Course->resetEnrollmentCount( $id );
		$this->Course->quizzesQuestionCount( $id );

		$this->Training->setQuizSettings($this->Course->Quiz->getQuizSettings()); //set quiz settings
		//$current_module = $this->Course->getCurrentModule($id,1);
		//debug($current_module);
		
		$this->Course->recursive = -1;
		$this->Course->unbindAll();

		$options = array('conditions' => array('Course.' . $this->Course->primaryKey => $id), 'fields'=>array('Course.*'));
		$course = $this->Course->find('first', $options);

		$options = array('conditions' => array('CoursesEnrollment.course_id' => $id), 'fields'=>array('CoursesEnrollment.*', 'User.*'), 'recursive'=>1, 'order'=>'User.full_name ASC');

		$this->Course->CoursesEnrollment->unbindModel(array('belongsTo'=>array('Course')));
		$this->Course->CoursesEnrollment->User->unbindModel(array('hasAndBelongsToMany'=>array('Course')));
		
		//set custom pagination limit
		$this->_setPaginationLimit('CoursesEnrollment');

		$this->paginate = array(
			'CoursesEnrollment'=>array_merge_recursive($options, $this->paginate)
		);
		//debug($this->paginate);
		$courses_enrollments = $this->Paginator->paginate('CoursesEnrollment');
		//pr($courses_enrollments); //exit;
		$this->loadModel('Training.QuizAttempt');
		foreach($courses_enrollments as $j=>$userEnrollment)	{

							$qa_options['joins'] = array(
								array(
									'table' => 'quizzes',
									'alias' => 'Quiz',
									'type' => 'LEFT',
									'conditions' => array(
										'Quiz.id=QuizAttempt.quiz_id'
									)
								)
							);
							//debug($userEnrollment['CoursesEnrollment']['id']);
							//$qa_options['conditions']= array('QuizAttempt.user_id'=>$userid['User']['id'], 'Quiz.course_id'=>$id);
							$qa_options['conditions']= array('QuizAttempt.courses_enrollment_id'=>$userEnrollment['CoursesEnrollment']['id']);
							$qa_options['fields'] = array('QuizAttempt.*', 'Quiz.sumgrades', 'Quiz.grade', 'Quiz.minpassgrade', 'Quiz.minpassquestions', 'Quiz.question_count');
							// quiz attempt options
							///debug($qa_options);
							$this->QuizAttempt->setArchived(true);
							$quizAttempts = $this->QuizAttempt->find('all', $qa_options);
							//debug($quizAttempts);
							foreach($quizAttempts as $qzattempt ){
								$this->__setAttemptResult($qzattempt);
							}

							$quizAttempts = $this->QuizAttempt->find('first', $qa_options);
							//debug($quizAttempts);
							$courses_enrollments[$j]['CoursesEnrollment'] = array_merge($courses_enrollments[$j]['CoursesEnrollment'], $quizAttempts);
							//$courses_enrollments[$i]['QuizAttempt'] = $quizAttempts;

							//debug($userEnrollment);
						}


		$users = $this->Course->User->find('all');
		$this->loadModel('Department');
		$departments = $this->Department->find('list');
		$this->set(compact('users', 'course', 'courses_enrollments', 'departments'));
	}

/**
 * admin_enroluserview method
 *
 * Course assignment screen from user view i.e. user view page or admin_index in UsersController.
 * Listing user or users and courses side by side and making enrollments.
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

  public function admin_enroluserview($userid = null)	{

    if ($this->request->is('ajax')) {
       $this->layout = 'ajax';
    }

    if ($this->request->is(array('post', 'put'))) {

      if(!isset($this->request->data['Course']['course_id']) || !isset($this->request->data['Course']['user_id'])) {
        $this->Session->setFlash(__('You must select both users and courses'), 'default', array('class'=>'btn-danger'));
        $this->redirect($this->referer());
        exit;
      }
      $usersToEnrol = $this->request->data['Course']['user_id']; //assign the users to enroll to new variable, so that user list remain unaffected for later courses.
      if(sizeof($this->request->data['Course']['course_id'])) {
        foreach($this->request->data['Course']['course_id'] as $courseid) {
          $selected = array();
          $this->request->data['Course']['user_id'] = $usersToEnrol; //reset users list with actual selected users to be enroll;
          foreach($this->request->data['Course']['user_id']  as $_userid)  {
            $exists = $this->Course->CoursesEnrollment->find('count', array('conditions'=>array('user_id'=>$_userid, 'course_id'=>$courseid), 'limit'=>1));
            if($exists)	{
              array_push($selected, $_userid);
            }
          }
          $this->request->data['Course']['existing'] = $selected;
          $this->__enrolUsersToCourse( $courseid );
        }
        $this->Session->setFlash(__('Selected users were enrolled to selected courses'));
        $this->redirect($this->referer());
        exit;
      }
    }

    $this->loadModel('User');
    $this->User->recursive = 0;
    //$this->User->unbindModel(array('hasMany'=>array('Course')));

    $usercond = array();
    $coursecond = array();
    $userenrolledcourses = array();

    if($userid) {
      $usercond = array('User.id'=>$userid);
      $userenrolledcourses = $this->Course->getCourseIdsByUser( $userid );
    }

    $users = $this->User->find('all', array('fields'=>['first_name', 'last_name'], 'conditions'=>$usercond));
    //debug($users);
    $this->Course->recursive = -1;

    $courses = $this->Course->find('list', array('order'=>'name ASC'));
    $this->set(compact('users', 'courses', 'userenrolledcourses', 'userid'));
  }

/**
 * admin_assigncourse method
 *
 * Enroll users to single course
 *
 * @param string $id course id
 * @return void
 */
	public function admin_assigncourse($id) {
	
		$this->Course->id = $id;
		if(!$this->Course->exists())	{
			throw new NotFoundException(__('Invalid course'));
		}
		$this->Course->recursive = -1;

		if ($this->request->is(array('post', 'put'))) {
			$this->__enrolUsersToCourse($id);
		}	else	{
			$this->Session->setFlash(__('Invalid request. Try Again'));
		}
		$this->redirect($this->referer());
	}

/**
 * __filteroutexisting method
 *
 * To filter already enrolled users from new enrolling list.
 *
 */

	function __filteroutexisting()	{
		if(!$this->request->data['Course']['user_id']) return;
		if($this->request->data['Course']['existing'])	{
      if(!is_array($this->request->data['Course']['existing'])) {
        $existing = explode(',', $this->request->data['Course']['existing']);
      } else  {
        $existing = $this->request->data['Course']['existing'];
      }
			foreach($existing as $userId)	{
				//debug($userId);
				$pos = array_search($userId, $this->request->data['Course']['user_id']);
				//debug($pos !== false);
				if($pos !== false)	{
					unset($this->request->data['Course']['user_id'][$pos]);
				}
			}
		}
	}

/**
 * __enrolUsersToCourse method
 *
 * Enroll users to single course
 *
 * @param string $id course id
 * @return void
 */

  function __enrolUsersToCourse($id) {
    //debug($this->request->data['Course']['user_id']);
    $this->__filteroutexisting();
    //debug($this->request->data['Course']['user_id']);
    //exit;
    //check if enddate is empty then set deafult enddate.
    if(!isset($this->request->data['Course']['enddate']) || trim($this->request->data['Course']['enddate'])=="") 
      $this->request->data['Course']['enddate'] = date('Y-m-d', strtotime(date('Y-m-d') . ' +' . $this->request->data['Course']['leaddays'] . ' days'));

    /* $minEndDate = date('Y-m-d', strtotime($this->Course->field('startdate') . ' +' . $this->request->data['Course']['leaddays'] . ' days'));
    if($this->request->data['Course']['enddate'] < $minEndDate){
        $this->Session->setFlash(__('Enrollment can't start before course start date. Try extending Complete By date'));
        $this->redirect($this->referer());
      } */

    if(USESTARTDATE) $this->Course->makeEnddate( $this->request->data['Course'] );
    if(!USESTARTDATE) $this->Course->makeStartdate($this->request->data['Course']);	
    
    //debug($this->request->data['Course']); exit;
    if($this->request->data['Course']['user_id'])	{
      $data = array('id' => $id); $tos = [];
			$creator_id = $this->Auth->user('id');
      foreach($this->request->data['Course']['user_id'] as $user_id)  {
        $dept_id = $this->Course->User->field('department_id', array('id'=>$user_id));
        $data['CoursesEnrollment'][] = array(
												'user_id'=>$user_id, 
												'department_id'=>$dept_id,
												'creator_id'=>$creator_id,
												'course_id'=>$id,
												'course_module_id' => $this->Course->getCurrentModule( $id ),
												'startdate'=>$this->request->data['Course']['startdate'], 
												'enddate'=>$this->request->data['Course']['enddate'], 
												//'duration'=>@$this->request->data['Course']['duration'], 
												'leaddays'=>$this->request->data['Course']['leaddays'],
												'set_reminder' => $this->request->data['Course']['set_reminder'],
												'reminder_date' => $this->request->data['Course']['reminder_date'],
												'rem_pre_month' => $this->request->data['Course']['rem_pre_month'],
												'rem_pre_week' => $this->request->data['Course']['rem_pre_week'],
												'rem_pre_day' => $this->request->data['Course']['rem_pre_day'],
												'rem_today' => $this->request->data['Course']['rem_today'],
												'rem_post_daily' => $this->request->data['Course']['rem_post_daily'],
												'rem_post_weekly' => $this->request->data['Course']['rem_post_weekly'],
												'set_post_specific_date' => $this->request->data['Course']['set_post_specific_date'],
												'rem_post_date' => $this->request->data['Course']['rem_post_date'],
												);
          }
      //debug( $tos );
      //debug($data);
      //exit;
      if($this->Course->saveAll( $data ))	{
        $this->Course->resetEnrollmentCount( $id );
        $course = $this->Course->read(); 
        foreach($data['CoursesEnrollment'] as $enrolment )  {
												if($enrolment['startdate'] > date('Y-m-d')) continue;  //if enrollment is not started yet, don't send email
          $enrol['CoursesEnrollment'] = $enrolment;
          $enrol['Course'] = $course['Course'];
          $enrol['User'] = $this->Course->User->find('first', array('conditions'=>array('User.id'=>$enrolment['user_id']), 'fields'=>array('id', 'username', 'first_name', 'last_name', 'email_address', 'full_name')))['User'] ;
          $to = $enrol['User']['email_address'];
          if($to)
							$this->__Email($to, 'You are enrolled to a training!', 'Training.user-enrolled-tocourse', array('data' => $enrol));
        }
				$this->log(sprintf("##authuser## enrolled %s users to course ##Course:{$id}##", sizeof($data['CoursesEnrollment'])), "system");
        $this->_flash(sprintf(__('%s users were enrolled to training %s.'), sizeof($data['CoursesEnrollment']), $course['Course']['name']));
      }	else	{
        $this->_flash(__('Users couldnt be enrolled to training course(s). Try Again'));
      }
    }
  }

/**
 * searchusers method
 *
 * A processor for search user operations on Enrolment screen
 * Called using ajax, usually
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

	public function searchusers($id) {

		$this->Course->id = $id;
		if(!$this->Course->exists())	{
			throw new NotFoundException(__('Invalid course'));
		}

		$course = $this->Course->read();

		if($this->request->is('ajax'))	{
			$this->layout = 'ajax';
		}

		$selected = array(); $users = array(); $options = array();

		if ($this->request->is(array('post', 'put'))) {
			//debug($this->request->data);
		if(isset($this->request->data['department_id']) && $this->request->data['department_id'])	{
					$options['conditions'] = array('User.department_id'=>$this->request->data['department_id']);
				}
			
			if(isset($this->request->data['Course']['s']))	{
				$s = $this->request->data['Course']['s'];
				if(strlen(trim($s)) > 1)	{
					$options['conditions'] = array("OR"=>array("User.first_name LIKE "=>"%$s%", "User.last_name LIKE "=>"%$s%"));
				}
			}

			if(isset($this->request->data['Course']['listall']))	{
				$options['conditions'] = null;
			}
			//$options['order'] = 'full_name ASC';
			$this->loadModel('User');
			$this->User->recursive = -1;
			$userlist = $this->User->find('all', $options);
			$this->loadModel('QuizAttempt');
			foreach($userlist as $user)	{

				$exists = $this->Course->CoursesEnrollment->find('count', array('conditions'=>array('user_id'=>$user['User']['id'], 'course_id'=>$id)));

				$incompleteEnrollment = $this->Course->CoursesEnrollment->find('count', array('conditions'=>array('user_id'=>$user['User']['id'], 'course_id'=>$id, 'completed_date IS NULL')));
				
				$completedEnrollments = $this->Course->CoursesEnrollment->find('all', array('conditions'=>array('user_id'=>$user['User']['id'], 'course_id'=>$id, 'completed_date IS NOT NULL')));
				$incompleteAttempt = false;
				if($completedEnrollments){
					foreach($completedEnrollments as $enrol){
					/* $count = (int) $this->QuizAttempt->find('count', array('conditions'=>array('OR' =>array(
																									array('courses_enrollment_id'=> $enrol['CoursesEnrollment']['id'], 'state'=>'inprogress'),
																									array('courses_enrollment_id'=> $enrol['CoursesEnrollment']['id'], 'result'=>'fail')
																																													)
																																											)
																																	)
																											); */
					if($enrol['CoursesEnrollment']['result']!= 'pass') $incompleteAttempt = true;
					}
				}
				//$repeatingCourse = $this->Course->field('repeats', array('id' => $id));
				//debug(!!$incompleteEnrollment); 
				//debug($incompleteAttempt); 
				//debug($user['User']['first_name']);
				if($exists && (!!$incompleteEnrollment || $incompleteAttempt)){
					array_push($selected, $user['User']['id']);
				}

				$users[$user['User']['id']] = $user['User']['full_name'];
			}
			$months = months12();
			$this->set(compact('course', 'selected', 'users', 'months'));
		}
	}

/**
 * admin_removeuser method
 *
 * Method to show remove user from course
 *
 * @param string $id
 * @return void
 */

	public function admin_removeuser($id)	{
		$this->Course->id = $id;
		if(!$this->Course->exists())	{
			throw new NotFoundException(__('Invalid course'));
		}

		if($this->request->is('ajax'))	{
			$this->layout = 'ajax';
		}
		//debug($this->request->data); exit;

		if ($this->request->is(array('post', 'put'))) {
			if($this->request->data['id']){
				if($this->Course->canRemoveEnrollment($this->request->data['id']))	{
					$this->Course->removeEnrollment($this->request->data['id']);
					$this->log("##authuser## remove an enrollment from course ##Course:{$id}##", "system");
					if(!$this->request->is('ajax')) $this->Session->setFlash(__('User was removed from training course.'));
					echo 'deleted';
					exit;
				}	else	{
					echo "Cannot remove a completed enrollment ";
					exit;
				}
			}
		if(isset($this->request->data['CoursesEnrollment'])){
			$user_count = 0;
				foreach($this->request->data['CoursesEnrollment'] as $enrol_id){
					if($this->Course->canRemoveEnrollment($enrol_id)){
						$this->Course->removeEnrollment($enrol_id);
							$user_count++;
						}
					}
				$this->log(sprintf("##authuser## removed %s enrollments from course ##Course:{$id}##", $user_count), "system");
				$this->Session->setFlash(__( $user_count.' Enrollments were removed from training course.'));
			}else{
				$this->Session->setFlash(__('No Enrollments was removed'));
			}
			$this->redirect($this->referer());
		}
		echo 'error';
		exit;
	}

/**
 * my method
 *
 * Method to show user courses, attempts & results
 *
 * @param string $userid
 * @return void
 */

	public function my($userid = null) {
		if(!$userid) $userid = $this->Auth->user('id');
		$this->Course->CoursesEnrollment->unbindAll(true);
		$this->Course->CoursesEnrollment->bindModel(array('belongsTo'=>array('Course')));
		$options = array('conditions'=>array('CoursesEnrollment.user_id'=>$userid), 'fields'=>array('CoursesEnrollment.course_id', 'Course.*'), 'group'=>'CoursesEnrollment.course_id');
		
		//get my courses from enrollments table
		$mycourses = $this->Course->CoursesEnrollment->find('all', $options);
		$this->Training->setQuizSettings($this->Course->Quiz->getQuizSettings()); //set quiz settings for calculation of results later
		//debug($mycourses);
		foreach($mycourses as $i => $course)	{
			$en_options = array('conditions'=>array('CoursesEnrollment.user_id'=>$userid, 'CoursesEnrollment.course_id'=>$course['CoursesEnrollment']['course_id'],  'CoursesEnrollment.startdate <='=>date('Y-m-d')));
			$this->Course->CoursesEnrollment->unbindAll(true); //unbind unwanted models
			$enrollments = $this->Course->CoursesEnrollment->find('all', $en_options);
			//debug($course);
			if(!$enrollments || $course['Course']['deleted']){ 
				unset($mycourses[$i]); 
				continue;
			}

			$this->loadModel('Training.QuizAttempt');
			//$this->QuizAttempt('all')
			/*$this->Course->Quiz->displayField = 'id';
			$quizzes = $this->Course->Quiz->find('list', array('conditions'=>array('Quiz.course_id'=>$course['Course']['id']), 'recursive'=>-1)); //get quiz ids which are 
			$quizAttempts = $this->QuizAttempt->find('all', array('conditions'=>array('QuizAttempt.quiz_id'=>$quizzes), 'order'=>'QuizAttempt.id ASC'));
			debug($quizAttempts);*/
			//$this->QuizAttempt->bindModel(array('belongsTo'=>array('Quiz')), false);
			foreach($enrollments as $j=>$enrollment)	{
				$qa_options['joins'] = array(
					array(
						'table' => 'quizzes',
						'alias' => 'Quiz',
						'type' => 'LEFT',
						'conditions' => array(
							'Quiz.id=QuizAttempt.quiz_id'
						)
					)
				);

				$qa_options['conditions']= array('QuizAttempt.courses_enrollment_id'=>$enrollment['CoursesEnrollment']['id'], 'QuizAttempt.archived !='=>1);
				$qa_options['fields'] = array('QuizAttempt.*', 'Quiz.sumgrades', 'Quiz.grade', 'Quiz.minpassgrade', 'Quiz.minpassquestions', 'Quiz.showpassfail');
				// quiz attempt options
				///debug($qa_options);
				$quizAttempts = $this->QuizAttempt->find('first', $qa_options);
				//debug($quizAttempts);
				$pass_fail = (isset($quizAttempts['QuizAttempt']['result']) && $quizAttempts['QuizAttempt']['result'] != NULL) ? $quizAttempts['QuizAttempt']['result'] : $this->Training->getresult($quizAttempts, 'basic'); 
				if($quizAttempts && $pass_fail == 'pass'){
					unset($enrollments[$j]); //unset enrollment because user has passed and completed enrollment.
					continue; //if attempt exists and user have passed.
				}else{
					$enrollments[$j] = array_merge($enrollments[$j], $quizAttempts);
				}
			}
			$mycourses[$i]['CoursesEnrollment'] = $enrollments;
		}

		//debug($mycourses);

		$this->set(compact('mycourses'));
		$this->set('_quizSettings', $this->Course->Quiz->getQuizSettings());
	}

/**
 * enrollment method
 *
 * Method to show user enrollment by enrollment ID
 *
 * @param string $id
 * @return void
 */

	public function admin_enrollment( $id ) {
		$this->Course->CoursesEnrollment->id = $id;
		if(!$this->Course->CoursesEnrollment->exists())	{
			throw new NotFoundException(__('Invalid Enrollment'));
		}

		if ($this->request->is(array('post', 'put'))) {
			if(USESTARTDATE) $this->Course->makeEnddate($this->request->data['CoursesEnrollment']);  //incase of USESTARTDATE is true.
			if(!USESTARTDATE) $this->Course->makeStartdate($this->request->data['CoursesEnrollment']);
			//debug($this->request->data); exit;
			if($this->Course->CoursesEnrollment->save($this->request->data)) {
				$data = $this->Course->CoursesEnrollment->read();
				if($data['CoursesEnrollment']['startdate'] < date('Y-m-d')){ //if enrollment is already started then send email notification.
					//$to = $this->Course->User->getEmailAddressByUserId( $data['User']['id'] );
					//$this->__Email($to, 'Enrollment details are updated!', 'Training.user-enrollment-updated', array('data' => $data));
				}
				$this->log("##authuser## updated an enrollment for course ##Course:{$data['CoursesEnrollment']['course_id']}##", "system");
				$this->Session->setFlash(__('The enrollment was saved.'));
				return $this->redirect(array('action'=>'enrol', $this->request->data['CoursesEnrollment']['course_id']));
			}	else	{
				$this->Session->setFlash(__('The enrollment could not be saved. Try Again'));
			}
		}

		$this->request->data = $enrollment = $this->Course->CoursesEnrollment->read();
			//debug($enrollment);
		$course['Course'] = @$enrollment['Course'];

		$months = months12();
		//debug($this->request->data);
		$this->set(compact('enrollment', 'months', 'course'));
	}

/**
 * admin_view method
 *
 * View All possible stats for a Course
 *
 * @param string $id
 * @return void
 */
	public function admin_view( $id ) {
		$this->Course->id = $id;
		if(!$this->Course->exists())	{
			throw new NotFoundException(__('Invalid course'));
		}
		 $this->Course->resetEnrollmentCount( $id );

		 $this->Training->setQuizSettings($this->Course->Quiz->getQuizSettings()); //set quiz settings
		//$limit = 20;
		//if(isset($this->request->data['Course']['limit'])) $limit = $this->request->data['Course']['limit']; uncomment when page filter need to applied.
//debug($this->request->data); exit;
		$this->Course->recursive = -1;
		$this->Course->CoursesEnrollment->recursive = -1;
		//$this->Course->unbindAll();
		$options = array('conditions' => array('Course.' . $this->Course->primaryKey => $id), 'fields'=>array('Course.*'));
		$course = $this->Course->find('first', $options);

		//options to get user ids of enrolled users. Once we have IDs we will query the enrollments table to get more specific data
		$options = array('conditions' => array('CoursesEnrollment.course_id' => $id), 'fields'=>array('User.id','User.username', 'User.first_name', 'User.last_name', 'User.role_id'), 'recursive'=>-1, 'order'=>'User.full_name ASC', 'group'=>'User.id');
		$options['joins'] = array(
			array(
				'table' => 'users',
				'alias' => 'User',
				'type' => 'LEFT',
				'conditions' => array(
						'User.id=CoursesEnrollment.user_id'
				)
			)
		);
		
		//set custom pagination limit
		$this->_setPaginationLimit('CoursesEnrollment');

		if(isset($this->request->data['Export']) && $this->request->data['Export'] =='Export'){
				$courses_enrollments = $this->Course->CoursesEnrollment->find('all', $options);
			}else{
				$this->paginate = array(
					'CoursesEnrollment'=>array_merge_recursive($options, $this->paginate)
					);

				//debug($this->paginate); exit;
				//array_merge_recursive($this->paginate, array('CoursesEnrollment'=>$options));
				$courses_enrollments = $this->Paginator->paginate('CoursesEnrollment');
			}
		//debug($courses_enrollments);
		$this->loadModel('Training.QuizAttempt');

		if($courses_enrollments)	{
			foreach($courses_enrollments as $i=>$userid)	{

				$eu_options = ['conditions'=>['CoursesEnrollment.user_id'=>$userid['User']['id'], 'CoursesEnrollment.course_id'=>$id]];// enrolled users options
				$courses_enrollments[$i]['CoursesEnrollment'] = $this->Course->CoursesEnrollment->find('all', $eu_options);

					if( $courses_enrollments[$i]['CoursesEnrollment'] )	{

						foreach($courses_enrollments[$i]['CoursesEnrollment'] as $j=>$userEnrollments)	{

							$qa_options['joins'] = array(
								array(
									'table' => 'quizzes',
									'alias' => 'Quiz',
									'type' => 'LEFT',
									'conditions' => array(
										'Quiz.id=QuizAttempt.quiz_id'
									)
								)
							);
							//debug($userEnrollments['CoursesEnrollment']['id']);
							//$qa_options['conditions']= array('QuizAttempt.user_id'=>$userid['User']['id'], 'Quiz.course_id'=>$id);
							$qa_options['conditions']= array('QuizAttempt.courses_enrollment_id'=>$userEnrollments['CoursesEnrollment']['id']);
							$qa_options['fields'] = array('QuizAttempt.*', 'Quiz.sumgrades', 'Quiz.grade', 'Quiz.minpassgrade', 'Quiz.minpassquestions', 'Quiz.question_count');
							// quiz attempt options 
							///debug($qa_options);
							//Save Quiz Attempts result.
							$this->QuizAttempt->setArchived(true);
							$quizAttempts = $this->QuizAttempt->find('all', $qa_options);
							//debug($quizAttempts);
							foreach($quizAttempts as $qzattempt ){
								$this->__setAttemptResult($qzattempt);
							}
						
							$quizAttempt = $this->QuizAttempt->find('first', $qa_options);

							$courses_enrollments[$i]['CoursesEnrollment'][$j] = array_merge($courses_enrollments[$i]['CoursesEnrollment'][$j], $quizAttempt);
							//$courses_enrollments[$i]['QuizAttempt'] = $quizAttempt;

							//debug($userEnrollments);
						}
				}

				$courses_enrollments[$i]['User']['Role'] = $this->Course->User->Role->findById($userid['User']['role_id'])['Role'];
				$courses_enrollments[$i]['User']['Supervisor'] = $this->Course->User->getSupervisor($userid['User']['id']);
				if($this->Course->User->getAvatar($userid['User']['id']))
					$courses_enrollments[$i]['User']['Upload'][] = $this->Course->User->getAvatar($userid['User']['id']);
				else $courses_enrollments[$i]['User']['Upload'] = Null;
			}
		}

		//debug($courses_enrollments);

		//debug(get_included_files());
		//$this->CoursesEnrollment->recursive = 2;
		//debug(get_class($this->CoursesEnrollment));
		//debug($this->CoursesEnrollment);
		//$memberships = $this->CoursesEnrollment->find('all', array('recursive'=>2));
		//debug($memberships);
		//$this->loadModel('User');		
		//$users = $this->Course->User->find('all');

		$this->set(compact('course', 'courses_enrollments'));
		$this->set('_quizSettings', $this->Course->Quiz->getQuizSettings());

		//Export data in Excel
		if(isset($this->request->data['Export']) && $this->request->data['Export'] =='Export'){
				//debug($course);
				$this->Training->setQuizSettings($this->Course->Quiz->getQuizSettings());
				$results = $courses_enrollments;
				
				$this->PhpExcel->createWorksheet() ->setDefaultFont('Calibri', 12);
				//$this->PhpExcel->mergeCells('A1:L1');
				//$this->PhpExcel->getAlignment('A1')->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->PhpExcel->getActiveSheet(0)->mergeCells('A1:M1');
				$this->PhpExcel->getActiveSheet(0)->getStyle('A1:M1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
				$this->PhpExcel->getActiveSheet(0)->getStyle('A1:M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->PhpExcel->getActiveSheet(0)->getStyle('A1:M1')->getFont()->setBold(true);
				$this->PhpExcel->getActiveSheet(0)->getStyle('A1:M1')->getFill()->getStartColor()->setARGB('FF8080');
				$this->PhpExcel->addTableRow(array('Course Stats for "'.$course['Course']['name'].'"'),  array('bold' => true, 'align'=>'center'));

				$table = array(
									array('label' => __('User Code'), 'filter' => true),
									array('label' => __('Name'), 'filter' => true),
									array('label' => __('Manager')),
									array('label' => __('User Type')),
									array('label' => __('Start on')),
									array('label' => __('Completed on')),
									array('label' => __('Pass/Fail'), 'filter' => true),
									array('label' => __('Pass %'), 'filter' => true),
									array('label' => __('Locked')),
									array('label' => __('Signed')),
									array('label' => __('Complete By')),
									array('label' => __('Lead Days')),
									array('label' => __('Signature'))
							);
					$this->PhpExcel->addTableHeader($table, array('name' => 'CourseEnrollmentReport', 'bold' => true));
					$i=0; $pass = 0; $fail = 0;
					
					$manager = "";
					foreach ($results as $result) {
							if(count($result['CoursesEnrollment'])>0)	{
								$attended = false;
								$pass_fail = '';
								$startDate = $completeDate = $endDate = $leadDays ="";
								$locked = "";
								$percent_result = $showResult = "";
								$signed = "";
								foreach($result['CoursesEnrollment'] as $module)	{
									$percent = 0;
									$startDate .= "".date('m/d/Y', strtotime($module['CoursesEnrollment']['startdate']))."\n" ;
									if($module['CoursesEnrollment']['completed_date']){ 
										$attended = true;
										//$pass_fail = $this->Training->getresult($module, 'short');
										$pass_fail = (isset($module['QuizAttempt']['result']) && $module['QuizAttempt']['result'] != NULL) ? substr($module['QuizAttempt']['result'], 0, 1) : $this->Training->getresult($module, 'short');
										$showResult = $pass_fail == 'p' ? " PASS" : " FAIL" ;
										$completeDate .= "".date('m/d/Y', strtotime($module['CoursesEnrollment']['completed_date']));
												$percent = ($module['QuizAttempt']['sumgrades'] / $module['Quiz']['sumgrades']) * 100;
										$percent_result .=  number_format($percent).'% ';
										
										}
									$showResult .= "\n";
									$completeDate .= "\n";
									$percent_result .= "\n";
									if($module['CoursesEnrollment']['enddate']) $endDate .= "".date('m/d/Y', strtotime($module['CoursesEnrollment']['enddate']));
									$endDate .= "\n";
									$leadDays .= "".$module['CoursesEnrollment']['leaddays']."\n" ;

									if(!isset($module['QuizAttempt'])){ 
												$locked .= " No Attempt ";
										}else{ if($module['QuizAttempt']['locked']) $locked .= " Locked ";
														else $locked .= " Unlocked ";
													if($module['QuizAttempt']['unlock_requested']) $locked .= " & unLock Requested";
											}
										$locked .= "\n";
										//check if signature required or not
									if($course['Course']['signature']){
											if(isset($module['QuizAttempt'])) $signed .= $module['QuizAttempt']['signed_by_user']? "Yes" : "No";
											}else{
											$signed .= " Not required ";
										}
									$signed .= "\n";
								}
								if($attended){
											$pass_fail == 'p' ? $pass++ : $fail++ ;
										}
								}
							//check if user has any supervisor
							if(isset($result['User']['Supervisor']) && $result['User']['Supervisor']){
								$manager = $result['User']['Supervisor']['full_name'];
								}

						$tmpdata = array ($result['User']['username'],
										$result['User']['full_name'],
										$manager,
										$result['User']['Role']['role_name'],
										$startDate,
										$completeDate,
										$showResult,
										$percent_result,
										$locked,
										$signed,
										$endDate,
										$leadDays
									);

					$this->PhpExcel->addTableRow($tmpdata);
																				// Draw signature 
							/*	if($course['Course']['signature'] && $quizAttempt['QuizAttempt']['state'] != 'inprogress'){
													//debug($enrollment['user_id']);
													$num_rows = $this->PhpExcel->getActiveSheet(0)->getHighestRow();
													if($this->Training->isSignExists($enrollment['user_id'])){
															$objDrawing = new PHPExcel_Worksheet_Drawing();
															$objDrawing->setName('Signature');
															$objDrawing->setDescription('Signature');
															$path = $this->Training->getSignPath($enrollment['user_id']) ; // Provide path to your file
															$objDrawing->setPath($path);
															//$objDrawing->setOffsetX(8);    // setOffsetX works properly
															//$objDrawing->setOffsetY(300);  //setOffsetY has no effect
															$objDrawing->setCoordinates('M'.$num_rows.'');
															$objDrawing->setHeight(50); // logo height
															$objDrawing->setWorksheet($this->PhpExcel->getActiveSheet(0));
															$this->PhpExcel->getActiveSheet(0)->getRowDimension($num_rows)->setRowHeight(50);
														}else{
														$this->PhpExcel->getActiveSheet(0)->setCellValue('M'.$num_rows.'', "No Signature exists");
														}
											} */
					}
				$num_rows = $this->PhpExcel->getActiveSheet(0)->getHighestRow();
				$newrow = $num_rows + 1;
				$this->PhpExcel->getActiveSheet(0)->getStyle("A".$newrow)->getFont()->setBold(true);
				//$this->PhpExcel->getActiveSheet(0)->getStyle('A1:K1')->getFill()->getStartColor()->setARGB('FF808080');
				$this->PhpExcel->addTableRow(array('Summery:'),  array('bold' => true, 'align'=>'center'));
				
				//debug($this->PhpExcel->getActiveSheet(0)->getHighestRow()); exit;
				$table = array(
									array('label' => __('Total Users Assigned')),
									array('label' => __('Total Attended')),
									array('label' => __('Pass')),
									array('label' => __('Fail')),
									array('label' => __('% Pass')),
							);
				$this->PhpExcel->addTableHeader($table, array('name' => 'CourseEnrollmentStats', 'bold' => true));

				$pass_percent = (float) ($pass / $course['Course']['completed_count']) * 100 ;
				$pass_percent = number_format($pass_percent, 2);
				$tmpdata = array($course['Course']['user_count'],
										$course['Course']['completed_count'],
										$pass,
										$fail,
										$pass_percent."%"
									);
					$this->PhpExcel->addTableRow($tmpdata);
				//$this->PhpExcel->getActiveSheet()->insertNewRowBefore(1, 1);
				$this->PhpExcel->addTableFooter()->output('EnrollmentReport-'.$course['Course']['name'].'-'.date("Fj-Y").'.xlsx');
		}
	}

/*
* __setAttemptResult method for updating the quiz attempt result for them.
* params $attempt array
* return void
*
*/

function __setAttemptResult($attempt){
//debug($attempt); exit;
if(!isset($attempt['QuizAttempt']) || $attempt['QuizAttempt']['state']=='inprogress')  return;
if($attempt['QuizAttempt']['result'] != NULL ) return;
$pass_fail = $this->Training->getresult($attempt, 'basic');
$earnedgrade = grade($attempt['QuizAttempt']['sumgrades'], $attempt['Quiz']['sumgrades'], $attempt['Quiz']['grade']);
$earnedgrade = float( $earnedgrade );
$this->QuizAttempt->id = $attempt['QuizAttempt']['id'];
$resultData = array('id'=> $attempt['QuizAttempt']['id'] ,
							'total_questions'=> $attempt['Quiz']['question_count'],
							'result'=> $pass_fail,
							'earned_grade' => $earnedgrade 
							);
$this->QuizAttempt->save($resultData);
//$this->Course->CoursesEnrollment->id = $attempt['QuizAttempt']['courses_enrollment_id'];
//$this->Course->CoursesEnrollment->saveField('result', $pass_fail);
 return;
}


/**
 * admin_enrolledusers method
 *
 * View All enrolled users and their stats for a Course
 *
 * @param string $id
 * @return void
 */
public function admin_enrolledusers() {

	}

	function youtube()	{
		
    $yt = & $this->YouTube->Service();

		//$html = $this->YouTube->uploadVideo();

		//$response = $this->YouTube->getResponse();

		//debug($response);

		try{

			//debug($this->YouTube->getVideos());

			/*$listResponse = $yt->channels->listChannels('brandingSettings', array(
				'mine' => 'true',
			));*/

			//UCqugOebn9yAzBrJclac0pFQ

			//$responseChannel = $listResponse[0];
			//$responseChannel['brandingSettings']['image']['bannerExternalUrl'] = $thumbnailUrl;

			pr($listResponse['items'][0]['id']);

			/*$searchResponse = $yt->search->listSearch('id,snippet', array(
					'type' => 'video',
					//'q' => $_GET['q'],
					//'location' =>  $_GET['location'],
					//'locationRadius' =>  $_GET['locationRadius'],
					'maxResults' => 10
			));*/

			debug($searchResponse);

			//debug($yt->videos->getChannels());
			/*$results = $yt->videos->listVideos('snippet', array());
			foreach ($results['items'] as $item) {
					echo $item['snippet']['title'] . "<br /> \n";
			}*/
		}	catch(Google_Exception $e)	{
			echo $response = $e->getMessage();
		}
	}

	function listvideos()	{
		$this->layout = 'ajax';

		$cached = $this->Session->check('listvideos-cached');
		//$cached = false;

		if($cached)	{
			$this->render('listvideos--cached');
			return;
		}

		$yt = & $this->YouTube->Service();

		try{
			$videos = $this->YouTube->getVideos();

			$response = $this->YouTube->getResponse();
			//debug($response);
			
			$this->Session->write('listvideos-cached', true);
			$this->set(compact('videos'));
		}	catch(Google_Exception $e)	{
			echo $response = $e->getMessage();
		}
	}

	public function learn($id) {
		if (!$this->Course->exists($id)) {
			throw new NotFoundException(__('Invalid course'));
		}

		$userid = $this->Auth->user('id');

		$this->Course->Behaviors->load('Containable');

		$options = array('conditions' => array('Course.' . $this->Course->primaryKey => $id), 'recursive'=>0, 
			'contain'=>array('Creator', 'Upload')
		);
		$course = $this->Course->find('first', $options);

		/*if($this->Course->verifyEnrol($userid, $id))	{
			$course['Quizzes'] = $this->Course->Quiz->find('all', array('conditions'=>array('Quiz.course_id'=>$id), 'recursive'=>-1));
		}*/

		if(isset($this->request->query['enid'])){
			$enrol_id = $this->request->query['enid'];
			$this->Course->CoursesEnrollment->recursive = -1;
			$current_enrollment = $this->Course->CoursesEnrollment->findById($enrol_id);
			}else{
			$current_enrollment = $this->Course->getCurrentEnrollment($userid, $id);
		}
		//pr($course);
		$this->loadModel('Training.CoursesLearnLog');
		$this->CoursesLearnLog->update_learn_log($id, $userid, $current_enrollment['CoursesEnrollment']['id']);
		$this->Course->record_view($id);
		$course['CoursesEnrollment'] = $current_enrollment['CoursesEnrollment'];
		$this->set(compact('course'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_activequestions( $id = null ) {

		if (!$this->Course->exists($id)) {
			throw new NotFoundException(__('Invalid course'));
		}

		$options = array('conditions' => array('Course.' . $this->Course->primaryKey => $id), 'recursive'=>-1);
		$course = $this->Course->find('first', $options);

		$course['Quizzes'] = $this->Course->Quiz->find('all', array('conditions'=>array('Quiz.course_id'=>$id), 'recursive'=>-1));

		foreach($course['Quizzes'] as $i=>$quiz)	{
			$option = array('fields' => array('Question.*'), 'recursive'=>-1);
			$course['Quizzes'][$i]['Questions'] = $this->Course->Quiz->getQuizQuestions($quiz['Quiz']['id'], null, $option);
		}

		//debug($course);exit;

		$this->set(compact('course'));
	}

/*
	public function admin_overduecourse()
	{

		$this->set('skipsubmenu', true);
		$this->User->recursive = 0;
		$types = array(1=>'My Compliance'); //by default, , 2=>'My Team’s Compliance', 3=>'All Compliance'

		$user_field = $this->Auth->user('id');
		//pr($this->Auth->user);
		//exit;
		if($this->Auth->user('view_team_compliance'))  {
			$types[2] = 'My Department\'s Compliance';
			$user_field = $this->Auth->user('department_id');
		}
		if($this->Auth->user('view_all_compliance'))  {
			$types[3] = 'All Compliance';
		}
		

		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$type_id = 3;
    $period_id = '1 MONTH';
		$date = $year . '-' . $month . '-' . $day;

		debug($date);
		
		//$this->loadModel('Training.Course');
		$trainings = $this->Course->calendar($date, 1, $user_field);
		//pr($trainings);

		$overdue_courses = $this->Course->getOverdueCourses( $date );

	}
*/

/**
 * test method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

 public function test( $id = null ){
	$this->redirect(array('action' => 'view', 'admin'=>false, $id));
 }


/**
 * admin_testrun method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

 public function admin_testrun( $id = null ){
	if (!$this->Course->exists($id)) {
			throw new NotFoundException(__('Invalid course'));
				}

		$userid = $this->Auth->user('id');

		$options = array('conditions' => array('Course.' . $this->Course->primaryKey => $id), 'recursive'=>-1);
		$course = $this->Course->find('first', $options);
		//pr($course);
		$this->set(compact('course'));
		//$QuizAttempt = ClassRegistry::init('Training.QuizAttempt');

		//debug(get_class($QuizAttempt));exit;

		//if($this->Course->verifyEnrol( $userid, $id ))	{
			//debug($this->Course->Quiz->getQuizSettings());
			$this->set('_quizSetting', $this->Course->Quiz->getQuizSettings());
			//$this->Course->Quiz->bindModel(array('hasOne'=>array('QuizAttempt'=>array('className'=>'Training.QuizAttempt','conditions'=>array('QuizAttempt.user_id'=>$userid)))));
			//debug($this->Course->Quiz);exit;
			//$this->Course->Quiz->QuizAttempt->unbindModel()
			$course['Quizzes'] = $this->Course->Quiz->find('all', array('conditions'=>array('Quiz.course_id'=>$id), 'recursive'=>0));
			//debug($course['Quizzes']);  exit;
			
			if(!$course['Quizzes']) {
					return $this->render('no_course_quiz');
				}
			foreach($course['Quizzes'] as $i=>$quiz)	{
				if($quiz['Quiz']['is_default'])	{
					//pr($quiz['Quiz']['id']); exit;
					$this->redirect(array('controller'=>'quizzes', 'action'=>'testattempt', $quiz['Quiz']['id'] ,'?'=>array('course_id'=>$id)));
					//debug(get_class($this->Course->Quiz->QuizAttempt));
					//pr($this->Course->Quiz->QuizAttempt->getUserAttempt($quiz['Quiz']['id'], $userid, [], 'count'));
					//if(!$this->Course->Quiz->QuizAttempt->getUserAttempt($quiz['Quiz']['id'], $userid, [], 'count'))	{
						
						//$course['Quizzes'][$i]['Quiz']['no_default_no_attempt'] = '1'; //this is neither default nor user has a previous attempt
					//}
				}
			}
			

		//}
		//pr($course); exit;
		//debug($course['Quizzes']);

		
		//$this->Course->record_last_access($id, $userid);
 }

/*
* admin_LearnerTranscript method for exporting whole history of a user
* @params $user_id int
* @return void
*
*/ 
 public function admin_learnertranscript($user_id = null){
			if (!$this->Course->User->exists($user_id)) {
				throw new NotFoundException(__('Invalid User'));
				}
				$this->log("##authuser## has checked Training History for user ##User:{$user_id}##", "system");
				$this->Course->User->id = $user_id;
				$user = $this->Course->User->read();
			if(isset($this->request->query['export']) && $this->request->query['export']=='pdf'){
							$this->layout= 'pdf';
						// initializing mPDF
						 $this->Mpdf->init();
						 $this->Mpdf->debug = true;

							// setting filename of output pdf file
						$this->Mpdf->setFilename($user['User']['full_name'].'-LearnerTranscript.pdf');

							// setting output to I, D, F, S
						$this->Mpdf->setOutput('D');
						}

			//if ($this->request->is('post')) {
					$this->Course->Behaviors->load('Containable'); //do it as containable
					$conditions = array(); $conditions_en = array();//init conditions
					$conditions_en['CoursesEnrollment.user_id'] = $user_id;

					$this->loadModel('Training.Quiz');
					$this->loadModel('Training.QuizAttempt');
					//$this->Course->bindModel(array('hasMany'=>array('CoursesEnrollment', 'Quiz')));
					$this->Course->CoursesEnrollment->bindModel(array('belongsTo' => array('User', 'Department')));
					$options = array(
						'conditions' => $conditions,
						'contain'=>array('CoursesEnrollment'=>array('conditions'=>$conditions_en//'User'=>array('id', 'first_name', 'last_name', 'full_name'), 'Department'=>array('id', 'name')
							)),
						'recursive'=>0
					);
					$results = $this->Course->find('all', $options);

					//pr($results); //exit;
					$this->set('_quizSettings', $this->Quiz->getQuizSettings());
					$quizAttempts = array();
					//bind Quiz model to QuizAttempt
					$this->QuizAttempt->bindModel(array('belongsTo'=>array('Quiz')));
					$this->loadModel('Training.Question');
					$this->loadModel('Training.QuestionAttempt');
					$courses = array();
					foreach($results as $i=>$course){
						//debug($course);
						if(sizeof($course['CoursesEnrollment'])>0){
							foreach($course['CoursesEnrollment'] as $j=>$coursesEnrollment){
								$qaoptions = array('conditions'=>array('QuizAttempt.user_id'=> $coursesEnrollment['user_id'], 'QuizAttempt.courses_enrollment_id'=>$coursesEnrollment['id']));
								$this->QuizAttempt->setArchived(true);
								$quizAttempts = $this->QuizAttempt->find('all', $qaoptions); //fetch all quiz attempt.
								$attempts = array();
								if($quizAttempts){
								//Get Quiz Attempt Related Data
								foreach($quizAttempts as $qz=>$quizAttempt){
									
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
											'QuizSlot.quiz_id' => $quizAttempt['QuizAttempt']['quiz_id']
										);
									$questions = $this->Question->find('all', $qoptions);
									//debug($questions);
									//$questionAttempt = null;
									if(isset($quizAttempt['QuizAttempt']['id'])){
											$quizAttempt['Quiz'] = $this->Quiz->find('first', array('conditions'=>array('Quiz.id'=>$quizAttempt['QuizAttempt']['quiz_id'])))['Quiz'];

										$queAoptions = array('conditions' => array('QuestionAttempt.quiz_attempt_id' =>$quizAttempt['QuizAttempt']['id']), 'order'=>array('QuestionAttempt.slot ASC'));
										$questionAttempts = $this->QuestionAttempt->find('all', $queAoptions);
									//debug($questionAttempts);
										foreach($questionAttempts as $i=>$questionAttempt){
												$quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']] = $questionAttempt['QuestionAttempt'];
												foreach($questions as $question){
													if($questionAttempt['QuestionAttempt']['question_id']==$question['Question']['id']){
															$quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']] = array_merge($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']], $question);
															break;
														}
												}
												$quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]['answeroutcome'] = $this->QuestionAttempt->answerOutcome($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
												//debug($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
												$quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]['sumgrades'] = $this->QuizAttempt->calcQuestionGrades($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
												//debug($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
										}
										//debug($quizAttempt);
									}
									$attempts[$qz] = $quizAttempt;
								}
								} //if $quizAttempts end
							$course['CoursesEnrollment'][$j]['QuizAttempt'] = $attempts;
							}
							//unset($course['CoursesEnrollment']);
							//debug($course);
							$courses[] = $course;
						}
					}

					//exit;
				$this->set(compact('courses', 'user'));

			//}
	 }

/*
* admin_exportQuizzes method for exporting all quiz attempt in a course for selected users
* @params $course_id int
* @return void
*
*/ 
 public function admin_exportquizzes($course_id = null){
		if(!$this->Course->exists($course_id)){
				throw new NotFoundException('Invalid Course');
			}
			$this->Course->id = $course_id;
			$this->Course->recursive = -1;
			$course = $this->Course->read();
			//debug($course);
			$results = array();
		if($this->request->is(array('put','post'))){
				$this->loadModel('Training.Quiz');
				$this->loadModel('Training.QuizAttempt');
				$this->loadModel('Training.Question');
				$this->loadModel('Training.QuestionAttempt');
			if(isset($this->request->data['CoursesEnrollment'])){
						$this->layout= 'pdf';
						// initializing mPDF
						 $this->Mpdf->init();
						 $this->Mpdf->debug = true;
							// setting filename of output pdf file
						$this->Mpdf->setFilename($course['Course']['name'].'- Quizzes Report.pdf');
							// setting output to I, D, F, S
						$this->Mpdf->setOutput('D');
					$user_id = array();
					foreach($this->request->data['CoursesEnrollment'] as $enrollment){
								$this->Course->CoursesEnrollment->id = $enrollment['id'];
								$user_id[] = $this->Course->CoursesEnrollment->field('user_id');
							}
						$user_id = array_unique($user_id); //filter for duplicate value;
						$this->Quiz->recursive = -1;
						$quizzes = $this->Quiz->find('all', array('conditions'=>array('Quiz.course_id'=>$course['Course']['id'])));
						foreach($user_id as $uid){
								$this->Course->User->recursive = 0;
							 $user = $this->Course->User->find('first', array('conditions'=> array('User.id'=>$uid)));
							foreach($quizzes as $key=>$quiz){
								$qzOptions = array('conditions'=>array('QuizAttempt.user_id' =>$uid, 'QuizAttempt.quiz_id' =>$quiz['Quiz']['id'], 'QuizAttempt.state'=>'finished'));
								$this->QuizAttempt->setArchived(true);
								$this->QuizAttempt->bindModel(array('belongsTo'=>array('CoursesEnrollment')));
								$quizAttempts = $this->QuizAttempt->find('all', $qzOptions);
								$attempts = array();
								//Get Question Attempts for each Quizattempt
								foreach($quizAttempts as $qz=>$quizAttempt){
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
											'QuizSlot.quiz_id' => $quizAttempt['QuizAttempt']['quiz_id']
										);
									$questions = $this->Question->find('all', $qoptions);
									if(isset($quizAttempt['QuizAttempt']['id'])){
										$queAoptions = array('conditions' => array('QuestionAttempt.quiz_attempt_id' =>$quizAttempt['QuizAttempt']['id']), 'order'=>array('QuestionAttempt.slot ASC'));
										$questionAttempts = $this->QuestionAttempt->find('all', $queAoptions);
									//debug($questionAttempts);
										foreach($questionAttempts as $i=>$questionAttempt){
												$quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']] = $questionAttempt['QuestionAttempt'];
												foreach($questions as $question){
													if($questionAttempt['QuestionAttempt']['question_id']==$question['Question']['id']){
															$quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']] = array_merge($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']], $question);
															break;
														}
												}
												$quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]['answeroutcome'] = $this->QuestionAttempt->answerOutcome($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
												//debug($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
												$quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]['sumgrades'] = $this->QuizAttempt->calcQuestionGrades($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
												//debug($quizAttempt['QuestionAttempt'][$questionAttempt['QuestionAttempt']['question_id']]);
										}
										//debug($quizAttempt);
									}
									$attempts[$qz] = $quizAttempt;
								}
								$quiz['QuizAttempt'] = $attempts;
								$user['Quiz'][] = $quiz ;
							}
							$results[] = $user;
						}
					$this->log("##authuser## exported Quizzes details for ##Course:{$course_id}##", "system");
					//debug($results); exit;
				}else{
				$this->Session->setFlash(__('No User selected'));
				return $this->redirect($this->referer());
				}
		}
		$this->set(compact('course', 'results'));
}

}
