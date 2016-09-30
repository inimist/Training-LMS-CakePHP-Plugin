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

class CoursesLearnLog extends TrainingAppModel {
		//public $useTable = 'courses_learn_log';
	
	/*
	* update_learn_log - update entry if a user visit learn course page.
	* @params int $course_id and int $user_id
	*	@return void
	*/
	function update_learn_log($course_id, $user_id, $enrol_id){
			if(!$course_id || !$enrol_id) return;
			if(!$user_id) return;
			$data = array('course_id'=>$course_id, 
				'user_id'=>$user_id,
				'courses_enrollment_id'=>$enrol_id,
				'viewcount'=> 1);
			//debug($data);
			if($this->isLearned($course_id, $user_id, $enrol_id)){ 
				$this->updateAll(array('CoursesLearnLog.viewcount' => 'viewcount +1'), array('CoursesLearnLog.course_id'=>$course_id, 'CoursesLearnLog.user_id'=>$user_id, 'CoursesLearnLog.courses_enrollment_id'=>$enrol_id));
			}else{
				$this->create();
				$this->save($data);
			}
		return;
	}

	function isLearned($course_id, $user_id, $enrol_id){
			if(!$course_id) return false;
			if(!$user_id) return false;
		$count = $this->find('count', array('conditions'=>array('CoursesLearnLog.course_id'=>$course_id, 'CoursesLearnLog.user_id'=>$user_id, 'CoursesLearnLog.courses_enrollment_id'=>$enrol_id)));
		return $count > 0 ? true : false;
	}

	function clearCourseLog($course_id){
		if(!$course_id) return;
		$this->deleteAll(array('CoursesLearnLog.course_id'=>$course_id));
	}

 }
