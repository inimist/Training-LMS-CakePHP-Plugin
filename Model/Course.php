<?php
App::uses('TrainingAppModel', 'Training.Model');
/**
 * Course Model
 *
 * @property User $User
 */
class Course extends TrainingAppModel {

public $actsAs = array(
		'FileUpload.FileUpload' => array(
				'fileModel' => 'Course',
				'fileVar' => 'attachments',
				'uploadDir' => 'files',
				'forceWebroot' => true,
				'allowedTypes' => array(
				  'jpg' => array('image/jpeg', 'image/pjpeg'),
				  'jpeg' => array('image/jpeg', 'image/pjpeg'), 
				  'gif' => array('image/gif'),
				  'png' => array('image/png','image/x-png'),
				  'pdf',
				  'txt' => 'text/plain',
				  'doc', 'docx', 'xls', 'xlsx', 'xlsm', 'msg', 'ppt', 'pptx'
				),
				'required' => false,
				'maxFileSize' => '20000000', //it is bytes (10000 bytes = 10KB)
				'unique' => true,
				'fileNameFunction' => 'sha1'
			),
		'Dateformat',
	'SoftDelete'
);//, 'HabtmCounterCache.HabtmCounterCache'
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * is_reattempt field
 *
 * @var boolean
 */
	public $is_reattempt = false;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter name for the module',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Creator' => array(
			'className' => 'User',
			'foreignKey' => 'creator_id',
			'conditions' => '',
			'fields' => array('id', 'username', 'first_name', 'last_name'),
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Upload.model' => 'Course'),
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		/*'CourseModule' => array(
				'className' => 'Training.CourseModule',
				'joinTable' => 'course_modules',
				'dependent' => true
		),*/
		'CoursesEnrollment' => array(
				'className' => 'Training.CoursesEnrollment',
				'joinTable' => 'courses_enrollments',
				'dependent' => true
		),
		'Quiz' => array(
				'className' => 'Training.Quiz',
				//'joinTable' => 'courses_enrollments',
				'dependent' => true
		),

	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	*/
	public $hasAndBelongsToMany = array(
			'User' => array(
					'className' => 'User',
					'joinTable' => 'courses_enrollments',
					'foreignKey' => 'course_id',
					'associationForeignKey' => 'user_id',
					'counterCache' => true,
					'conditions' => '',
					'fields' => '',
					'order' => '',
					'limit' => '',
					'offset' => '',
					'finderQuery' => '',
					'deleteQuery' => '',
					'insertQuery' => ''
			)
	);

  private $sourceTypes = array('Video'=>'Video', 'Powerpoint'=>'Powerpoint', 'Document'=>'Upload File');

  function getSourceTypes() {
    return $this->sourceTypes;
  }

	function beforeSave($options = array())	{

		//convert date fields in the form data to sql compatible before saving.
		$this->__ToSQL(array('startdate', 'enddate'));
	//calculating enddate from duration is no more needed. commented by surinder
	/*	if(isset($this->data[$this->alias]['duration']))	{
			if(trim($this->data[$this->alias]['duration'])!="")	{
					$this->data[$this->alias]['enddate'] = date('Y-m-d', strtotime($this->data[$this->alias]['startdate']  . ' +' . $this->data[$this->alias]['duration'] . ' months'));
					return true;
			}	else	{
				$this->data[$this->alias]['enddate'] = null;
			}
		} */
		return true;
	}

	function __ToSQL($dateFields)	{
		foreach($dateFields as $field)	{
			if($this->data && isset($this->data[$this->alias][$field]))	{
				if(!class_exists('CakeTime'))	{
					App::uses('CakeTime', 'Utility');
				}
				$this->data[$this->alias][$field] = CakeTime::toServer($this->data[$this->alias][$field], null, 'Y-m-d H:i:s');
			}
		}
	}

	function record_last_access($courseid, $userid)	{
		$this->CoursesEnrollment->updateAll(array('CoursesEnrollment.last_access_date'=>'now()'), array('course_id'=>$courseid, 'user_id'=>$userid));
	}

	function getCourseIdByQuizId($quizid)	{
		return $this->Quiz->find('first', array('conditions'=>array('Quiz.id'=>$quizid), 'fields'=>array('course_id'), 'recursive'=>-1))['Quiz']['course_id'];
	}

	function verifyEnrol($userid, $course)	{
		return (int) $this->CoursesEnrollment->find('count', array('conditions'=>array('CoursesEnrollment.user_id'=>$userid, 'CoursesEnrollment.course_id'=>$course), 'recursive'=>-1)) > 0;
	}

	function getCurrentEnrollment($user_id, $course_id, $enrol_id = null )	{
		if($enrol_id != null) 
				return $this->CoursesEnrollment->findById($enrol_id);

		//first try to get the un-completed one
		$enrollment = $this->CoursesEnrollment->find('first', array('conditions'=>array('CoursesEnrollment.user_id'=>$user_id, 'CoursesEnrollment.course_id'=>$course_id, 'CoursesEnrollment.startdate <=' => date('Y-m-d'), 'CoursesEnrollment.completed_date IS NULL'), 'recursive'=>-1));
		
		//if none, try to get one by latest enddate
		if(!$enrollment) {
			$enrollment = $this->CoursesEnrollment->find('first', array('conditions'=>array('CoursesEnrollment.user_id'=>$user_id, 'CoursesEnrollment.course_id'=>$course_id, 'CoursesEnrollment.startdate <=' => date('Y-m-d')), 'order'=>array('CoursesEnrollment.completed_date DESC'))); //if all enrollments are completed then it must show last enrollment result only.
		}

		//if none, try to get one by latest ID
		if(!$enrollment) {
			$enrollment = $this->CoursesEnrollment->find('first', array('conditions'=>array('CoursesEnrollment.user_id'=>$user_id, 'CoursesEnrollment.course_id'=>$course_id, 'CoursesEnrollment.startdate <=' => date('Y-m-d')), 'order'=>array('CoursesEnrollment.id DESC')));
		}

		return $enrollment;
	}

	function record_view($id)	{
		$this->id = $id;
		$this->updateAll(array('Course.views'=>'Course.views + 1'), array('Course.id'=>$id));
	}

	function resetEnrollmentCount( $id )	{
		$this->id = $id;
		$this->saveField('user_count', $this->getEnrollmentCount( $id ));
	}

	function getEnrollmentCount( $id )	{
		return $this->CoursesEnrollment->find('count', array('fields' => 'DISTINCT CoursesEnrollment.user_id', 'conditions'=>array('course_id'=>$id)));
	}

	/**
	 * makeEnddate - calculate an Enddate from start date by adding duration (months)
	 * @param array $data
	 * @return mixed array|null
	*/
	function makeEnddate( &$data )	{ 
		if(!USESTARTDATE) return;
		if(isset($data['autocalcenddate']) && !$data['autocalcenddate']) return $data;
		$data['enddate'] = date('Y-m-d', strtotime($data['startdate'] . ' +' . $data['duration'] . ' months'));
		return $data;
	}

	/**
	 * makeStartdate - calculate an StartDate from end date by subtracting leaddays
	 * @param array $data
	 * @return mixed array|null
	*/
	function makeStartdate( &$data )	{
		if(USESTARTDATE) return;
		if(isset($data['autocalcenddate']) && !$data['autocalcenddate']) return $data;
		$data['startdate'] = date('Y-m-d', strtotime($data['enddate'] . ' -' . $data['leaddays'] . ' days'));
		return $data;
	}

	/**
	 * questionCount - get questionCount for a course
	 * @param int $id
	 * @return int
	*/
	function questionCount( $id )	{
		$this->bindModel(array('hasMany'=>array('Question')));
		return $this->Question->find('count', array('conditions'=>array('Question.course_id'=>$id)));
	}

	/**
	 * updateQuestionCount - update questionCount for a course
	 * @param int $id
	 * @return void
	 */
	function updateQuestionCount( $id )	{
		$this->id = $id;
		$this->saveField('question_count', $this->questionCount( $id ));
	}

	/**
	 * updateAllQuestionCount - update questionCount for all available courses
	 * @param int $id
	 * @return void
	 */
	function updateAllQuestionCount()	{		
		foreach($this->find('all', array('fields'=>array('id'))) as $course)	{
			$this->updateQuestionCount( $course['Course']['id'] );
		}
	}

/**
	 * quizzesQuestionCount - get all quizes questions count for a course
	 * @param int $id
	 * @return int
	*/
	function quizzesQuestionCount( $id )	{ //for all quizzes of a course
		$question_count = $this->Quiz->find('first',  array('fields'=>array('sum(Quiz.question_count) as question_count'), 'conditions'=>array('course_id'=>$id)));
		return (int) $question_count[0]['question_count'];
	}

	/**
	 * updateQuizzesQuestionCount - update overall quizes questions count for a course
	 * @param int $id
	 * @return int
	*/
	function updateQuizzesQuestionCount( $id )	{ //for all quizzes of a course
		$this->id = $id;
		$this->saveField('quizquestion_count', $this->quizzesQuestionCount( $id ));
	}

	/**
	 * updateQuizQuestionCount - update default quiz questions count for a course
	 * @param int $id
	 * @return int
	*/
	function updateQuizQuestionCount( $id )	{ //only for default of a course
		$question_count = (int) $this->Quiz->field('question_count', array('Quiz.is_default'=>'1', 'Quiz.course_id'=>$id));
		$this->id = $id;
		$this->saveField('quizquestion_count', $question_count);
	}


/**
	 * minpassQuestionCount - get all quizes questions count for a course
	 * @param int $id
	 * @return int
	*/
	function minpassQuestionCount( $id )	{ //for all quizzes of a course
		$question_count = $this->Quiz->find('first',  array('fields'=>array('sum(Quiz.minpassquestions) as minpassquestions'), 'conditions'=>array('course_id'=>$id, 'is_default'=>true)));
		return (int) $question_count[0]['minpassquestions'];
	}

	/**
	 * updateMinpassQuestionCount - update overall quizes questions count for a course
	 * @param int $id
	 * @return int
	*/
	function updateMinpassQuestionCount( $id )	{ //for all quizzes of a course
		$this->id = $id;
		$this->saveField('minpassquestion_count', $this->minpassQuestionCount( $id ));
	}

	/**
	 * markEnrollmentAsCompleted 
	 * @param int $id, int $userid, string $result
	 * @return void
	*/
	function markEnrollmentAsCompleted($course_id, $enrol_id, $userid, $result = 'fail')	{ //for all quizzes of a course
			if(!$this->CoursesEnrollment->id) $this->CoursesEnrollment->id = $enrol_id; //set enrolment id
		 if($this->is_reattempt) {
				$this->CoursesEnrollment->saveField('completed_date', date('Y-m-d H:i:s'));
				$this->CoursesEnrollment->saveField('result', $result); // save result status of enrollment, same as last completed attempt.
				$enrollment = $this->CoursesEnrollment->read();
				if($enrollment['Course']['repeats'] && $result == 'pass') $this->makeNewEnrollment($enrollment);
				return; //TODO, to put in a more logical place and more actions
		 }

		//if($completed)	{
			//We assume that when user completes all quizzes in a course we shall find an incomplete enrollment in the courses_enrollments table and mark it as completed by entering a completed_date. We believe that there is just one active enrollment for a user for a course at a given time.
			//$db = $this->getDataSource();
			$options =  array('conditions'=>array('CoursesEnrollment.id'=>$enrol_id, 'CoursesEnrollment.completed_date IS NULL'));
			$enrollment = $this->CoursesEnrollment->find('first', $options);
			//debug($enrollment);
			//$attended_earlier = $this->CoursesEnrollment->find('count', array('conditions' =>array('course_id'=>$course_id, 'user_id'=>$userid, 'completed_date IS NOT NULL')));
			if($enrollment)	{ //There must be one!
				$this->CoursesEnrollment->id = $enrollment['CoursesEnrollment']['id'];
				$this->CoursesEnrollment->saveField('completed_date', date('Y-m-d H:i:s'));
				$this->CoursesEnrollment->saveField('result', $result); // save result status of enrollment, same as last completed attempt.
				//Update no. of Users attempted the Course.
				$attended = $this->CoursesEnrollment->find('count', array('fields' => 'DISTINCT CoursesEnrollment.user_id', 'conditions'=>array('course_id'=>$course_id, 'completed_date IS NOT NULL')));
				$this->query("UPDATE `courses` SET `completed_count` = $attended WHERE `id`= $course_id;");
				//Once the above enrollment has been updated we need to create a new enrollment for the next semester, if the course is set to have repeating semesters
				
				if($enrollment['Course']['repeats'] && $result == 'pass')	{ //if it's a recurring course. And if it was "unlocked" by an admin it is an existing attempt and user is just updating it
					$this->makeNewEnrollment($enrollment);
				}
			}
		//}
	}
	

	/**
	* make new enrollment Enrollment
	* @param $enrollment array
	* @return void
	*/
	function makeNewEnrollment($enrollment){
					if(!$enrollment) return;
					//prepare new enrollment
					$startdate = date('Y-m-d', strtotime(date('m/d/Y') .' + '.$enrollment['Course']['frequency'].' month'));
					$data = array(
						'course_id' => 	$enrollment['CoursesEnrollment']['course_id'],
						'user_id' => 	$enrollment['CoursesEnrollment']['user_id'],
						'creator_id' => 	$enrollment['CoursesEnrollment']['creator_id'],
						'department_id' => 	$enrollment['CoursesEnrollment']['department_id'],
						'startdate' => 	$startdate, //start date will be after n months after completing date, n is course repeat frequency
						'enddate' => date('Y-m-d', strtotime($startdate . ' +' . $enrollment['CoursesEnrollment']['leaddays'] . ' days')),
						'duration' => $enrollment['CoursesEnrollment']['duration'],
						'leaddays' => $enrollment['CoursesEnrollment']['leaddays']
					);

				$incompleteEnrollment = (int) $this->CoursesEnrollment->find('count', array('conditions'=>array('user_id'=>$enrollment['CoursesEnrollment']['user_id'], 'course_id'=>$enrollment['CoursesEnrollment']['course_id'], 'completed_date IS NULL')));

			if(!$incompleteEnrollment){ //if there is no incomplete enrollment, only then create new
				$this->CoursesEnrollment->create();
				$this->CoursesEnrollment->save($data, $validate = false);
			}
		}

	/**
	 * can remove an Enrollment
	 * @param $enrollment_id int Enrollment Id
	 * @return void
	*/
	function canRemoveEnrollment($enrollment_id)	{
		$this->CoursesEnrollment->id = $enrollment_id;
		$completed  = $this->CoursesEnrollment->field('completed_date') ? true : false;
		$QuizAttempt = ClassRegistry::init('Training.QuizAttempt');
		$hasAttempts = $QuizAttempt->find('count', array('conditions'=>array('QuizAttempt.courses_enrollment_id'=> $enrollment_id)));
		return !($completed || $hasAttempts);
	}

	/**
	 * Completely remove an Enrollment
	 * @param $enrollment_id int Enrollment Id
	 * @return void
	*/
	function removeEnrollment($enrollment_id)	{
		$this->CoursesEnrollment->id = $enrollment_id;
		$course_id = $this->CoursesEnrollment->field('course_id');
		//debug($course_id);exit;
		$this->CoursesEnrollment->delete( $enrollment_id );
		$this->resetEnrollmentCount( $course_id );
	}

	/**
	 * Make course module when creating new course
	 * @param $data array ($this->request->data)
	 * @return void
	*/
	function makeCourseModule($data)	{
		$courseModule = array('startdate'=>$data[$this->alias]['startdate']);
		if($data['Course']['repeats'])	{
			$courseModule['enddate'] = $data[$this->alias]['enddate'] = date('Y-m-d', strtotime($data[$this->alias]['startdate']  . ' +' . $data[$this->alias]['frequency'] . ' months'));
		}
		//pr($courseModule);
		return $courseModule;
		
	}

	/**
	 * Get current module for a course
	 * @param $id string Course ID
	 * @param $getrow bool return CourseModule row if true
	 * @return mixed int|array
	*/
	function getCurrentModule( $id, $getrow = false )	{
		$this->id = $id;
		$course_module_id = $this->field('current_module');
		if( !$getrow ) return $course_module_id;
		return $this->CourseModule->find('first', array('conditions'=>array('CourseModule.id'=>$course_module_id),  'recursive'=>-1));
	}

	/**
	 * Checke whether it is a reattempt
	 * @param $enrollmentid string Course ID
	 * @return bool
	*/
	function isReattempt( $enrollmentid ){
		$options = array('conditions'=>array('CoursesEnrollment.id'=>$enrollmentid, 'CoursesEnrollment.completed_date IS NOT NULL'), 'recursive'=>-1);
		return (int) $this->CoursesEnrollment->find('count', $options) > 0;
	}


	/**
	 * Checke whether it is a reattempt
	 * @param $enrollmentid string Course ID
	 * @return bool
	*/
	function checkReattempt( $enrollmentid ){
		$this->CoursesEnrollment->id = $enrollmentid;
		$this->is_reattempt = $this->isReattempt( $enrollmentid );
		return;
	}


/**
 * calendar - Basically a calendar month view
 *
 * @return void
 */
	function calendar($date, $type_id, $calendar_view_id, $user_id = null, $department_id = null) {
		
		//set start end Date according to Calendar view
		$this->_setCalendarStartEndDate($date, $calendar_view_id);
		$start = $this->calandar_startdate;
		$end = $this->calandar_enddate;

		$conditions = array(
						//'Course.user_count >= '=> 1
			);

		 $conditions_to = array(
			'CoursesEnrollment.enddate >=' => $start,
			'CoursesEnrollment.enddate <=' => $end,
      'Course.deleted !=' => 1,
		);
       
		//debug($start);
		//debug($end);

		$this->CoursesEnrollment->Behaviors->load('Containable');
		$options = array(
			'conditions' => $conditions_to,
			'contain'=>array('User'=>array('id', 'first_name', 'last_name', 'full_name'), 'Course')
		); //, 'TaskCompletion'=>array('Upload')
		//pr($options);  //pr($type_id); exit;
		switch($type_id)	{
			case 1: //for logged in user only
				$options['conditions']['CoursesEnrollment.user_id'] = $user_id;
				$upcoming_options['conditions']['CoursesEnrollment.user_id'] = $user_id;
			break;
			case 2: //for logged in user's team or department
				//$department_id = $user_field;
				$options['conditions']['CoursesEnrollment.department_id'] = $department_id;
				$upcoming_options['conditions']['CoursesEnrollment.department_id'] = $department_id;
			break;
			case 3:
				
			break;
		} 

		//pr($options);
		$this->CoursesEnrollment->recursive = 0;
		$courses = $this->CoursesEnrollment->find('all', $options);
		
	//pr($courses);
		$events = array();

		foreach($courses as $course)	{
			//pr($course);
			//if($course['Course']['user_count']>=1){
				//foreach($course['CoursesEnrollment'] as $enrollment){
				//	pr($enrollment);
					//$enrollmentchange = $this->_changeDate($enrollment, 'm/d/Y');
					$enrollment = $this->CoursesEnrollment->_changeDate($course['CoursesEnrollment'], 'm/d/Y');
					//pr($enrollment); //exit;
					$vartemp = array();
					$vartemp['Course'] = $course['Course'];
					//$vartemp['Client'] = $task['Client'];
					$vartemp['CoursesEnrollment'] = $enrollment;
					$events[$enrollment['enddate']][] = $vartemp;
				}
		
			//pr($events);	
		
		//pr($events); exit;
		return $events;
	}
	

/**
 * getOverdueCourses - Basically a calendar month view
 *
 * @return void
 */
function getOverdueCourses($type_id, $user_id, $department_id, $date )  {

		$this->CoursesEnrollment->Behaviors->load('Containable');
				 //, 'TaskCompletion'=>array('Upload')
		//$this->CoursesEnrollment->bindModel('hasMany'=>array('QuizAttempt'=>array('className'=>'Training.QuizAttempt','foreignKey'=>'courses_enrollemnt_id')));

		$options = array(
			'conditions' => array('CoursesEnrollment.enddate <' =>	$date, 'OR'=>array(array('CoursesEnrollment.completed_date IS NULL'), array('CoursesEnrollment.result' => 'fail')), 'CoursesEnrollment.disabled !='=> 1, 'Course.deleted !=' => 1),
			'contain'=>array('User'=>array('id', 'first_name', 'last_name', 'full_name'), 'Course'=>array('id', 'name'))
		);

		switch($type_id)	{
			case 1: //for logged in user only
				$options['conditions']['CoursesEnrollment.user_id'] = $user_id;
			break;
			case 2: //for logged in user's team or department
				$options['conditions']['CoursesEnrollment.department_id'] = $department_id;
			break;
			case 3:
				
			break;
		}
		//debug($options);
		//$this->CoursesEnrollment->recursive = 0;
		$QuizAttempt = ClassRegistry::init('Training.QuizAttempt');
		$Quiz = ClassRegistry::init('Training.Quiz');
		$course_enrollments = $this->CoursesEnrollment->find('all', $options);
		if($course_enrollments){
			foreach($course_enrollments as $key => $enrollment){
				//debug($enrollment);
			/*	$quiz = $Quiz->getdefaultQuiz($enrollment['Course']['id']);
				if($quiz){
					$default_qa = $QuizAttempt->find('first', array('conditions'=>array(
															'QuizAttempt.courses_enrollment_id'=>$enrollment['CoursesEnrollment']['id'],
															'QuizAttempt.quiz_id'=> $quiz['Quiz']['id']
															), 'order'=>'QuizAttempt.timefinish DESC'));
					if(($default_qa && $default_qa['QuizAttempt']['result'] != 'fail')){
						unset($course_enrollments[$key]); 
						continue;
					}
				}
				*/
			$count = $QuizAttempt->find('count', array('conditions'=>array('QuizAttempt.courses_enrollment_id'=>$enrollment['CoursesEnrollment']['id'], 'QuizAttempt.state'=>'inprogress')));
				$course_enrollments[$key]['CoursesEnrollment']['state'] = (int) $count ? 'In Progress' : 'Not Started';
			}
		}
		//pr($course_enrollments); exit;
		return $course_enrollments;
	
  }

/**
 * getCourseIdsByUser - Get all the course IDs which user is enrolled to
 *
 * @return void
 */

  function getCourseIdsByUser($userid) {
    $this->CoursesEnrollment->recursive = -1;
    $userEnrollments = $this->CoursesEnrollment->find('all', array('conditions' =>array('user_id'=>$userid), 'fields'=>array('course_id')));
    if($userEnrollments)  {
      $courses = [];
      foreach($userEnrollments as $enrollment)  {
        array_push($courses, $enrollment['CoursesEnrollment']['course_id']);
      }
      return $courses;
    }
  }

/*
* disablePendingTrainings - disable all future trainings
* @params int $user_id
* @return void
*/

 function disablePendingTrainings($user_id){
	 if(!$user_id) return;

		$conditions = array('CoursesEnrollment.user_id'=>$user_id, 'OR'=>array(array('CoursesEnrollment.completed_date IS NULL'), array('CoursesEnrollment.enddate <='=> date('Y-m-d'), 'CoursesEnrollment.result'=>'fail')));
		$this->CoursesEnrollment->updateAll(array('disabled'=>true), $conditions);
	
	}

/*
* retrieveDisabledTrainings - enable all future trainings
* @params int $user_id
* @return void
*/
 function retrieveDisabledTrainings($user_id){
	 if(!$user_id) return;

		$conditions = array('CoursesEnrollment.user_id'=>$user_id, 'OR'=>array(array('CoursesEnrollment.completed_date IS NULL'), array('CoursesEnrollment.enddate <='=> date('Y-m-d'), 'CoursesEnrollment.result'=>'fail')));
		$this->CoursesEnrollment->updateAll(array('disabled'=>false), $conditions);
	
	}


/**
 * stats - to find current stats of Courses
 * @param string $date
 * @return array
 */
 public function stats($date=null){
	if(!$date) $date = date('Y-m-d');
	$date = date('Y-m-d', strtotime($date));
	$result = array();
	 $result = array('total'=> $this->getTotalCourseCount($date),
									'deleted' => $this->getDeletedCourseCount($date),
		 							'enrollments' =>$this->getTotalEnrollmentCount($date),
									'overdue enrollments' =>$this->getOverdueEnrollmentCount($date)
									//'completed' => $this->getCompletedControlsCount($date)
											); 
	return $result;
 }

function getTotalCourseCount($date){
		if(!$date) $date = date('Y-m-d');
		return $this->find('count', array('conditions'=>array('Course.created <='=>$date), 'recursive'=> -1));
 }

function getDeletedCourseCount($date){
		if(!$date) $date = date('Y-m-d');
		return $this->find('count', array('conditions'=>array('Course.deleted'=>1, 'Course.deleted_date <='=>$date), 'recursive'=> -1));
 }

function getTotalEnrollmentCount($date){
		if(!$date) $date = date('Y-m-d');
		return $this->CoursesEnrollment->find('count', array('conditions'=>array('CoursesEnrollment.created <='=>$date), 'recursive'=> -1));
 }

function getOverdueEnrollmentCount($date){
		if(!$date) $date = date('Y-m-d');
		return $this->CoursesEnrollment->find('count', array('conditions'=>array('CoursesEnrollment.created <='=>$date, 'CoursesEnrollment.enddate <='=>$date,  'OR'=>array(array('CoursesEnrollment.completed_date IS NULL'), array('CoursesEnrollment.completed_date >'=>$date), array('CoursesEnrollment.completed_date <='=>$date, 'CoursesEnrollment.result'=>'fail'))), 'recursive'=> -1));
 }


}
