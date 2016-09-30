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
 * QuestionType Model
 *
 * @property Training $QuestionType
 */
 class QuestionType extends AppModel {

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
    );*/

	function beforeSave($options = array())	{
		return true;
	}

	function isMultipleChoice($id)	{
		return $id==5 || $id==6;
	}

	function isMatchingPairs($id)	{
		return $id==7;
	}

	function isTrueFalse($id)	{
		return $id==4;
	}

	function isTextAnswer($id)	{
		return !$this->isMultipleChoice($id) && !$this->isTrueFalse($id);
	}

	function onlyMultipleTypes($questionTypes)	{
		foreach($questionTypes as $id =>  $type)	{
			if(!$this->isMultipleChoice($id))	{
				unset($questionTypes[$id]);
			}
		}
		return $questionTypes;
	}

	function onlyTFTypes($questionTypes)	{
		foreach($questionTypes as $id =>  $type)	{
			if(!$this->isTrueFalse($id))	{
				unset($questionTypes[$id]);
			}
		}
		return $questionTypes;
	}

	function filterTypesForEdit($questionTypes, $currentid)	{
		foreach($questionTypes as $id =>  $type)	{
			if($this->isMultipleChoice($currentid) && !$this->isMultipleChoice($id))	{
				unset($questionTypes[$id]);
			}
			if($this->isTrueFalse($currentid) && !$this->isTrueFalse($id))	{
				unset($questionTypes[$id]);
			}
			if($this->isTextAnswer($currentid) && !$this->isTextAnswer($id))	{
				unset($questionTypes[$id]);
			}
			if($this->isMatchingPairs($currentid) && !$this->isMatchingPairs($id))	{
				unset($questionTypes[$id]);
			}
		}
		return $questionTypes;
	}
}