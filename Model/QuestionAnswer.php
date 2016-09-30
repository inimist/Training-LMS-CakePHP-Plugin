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

 class QuestionAnswer extends TrainingAppModel {

		//public $useTable = 'courses_enrollments';

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

	function beforeSave($options = array())	{
		return true;
	}*/

	function getCorrectAnswer($id = null){
		if(!$id) return;
		return $this->find('all', array('conditions'=>array('question_id'=>$id, 'fraction !='=>'0')));
	}


}