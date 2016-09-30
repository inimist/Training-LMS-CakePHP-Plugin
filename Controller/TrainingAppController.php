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
App::uses('AppController', 'Controller');

/**
 * Training AppController
 *
 * @package Training
 */
class TrainingAppController extends AppController {

	function beforeFilter() {

		if(!isset($this->Course))	{
			$this->loadModel('Training.Course');
		}

		if($this->name != 'Courses')	{ //just dont want it to run on it's own controller
			//$this->Auth->allow(array('*'));
			if(isset($this->request->query['course_id']) && (int)$this->request->query['course_id'])	{
				$this->Course->id = $this->request->query['course_id'];
				$this->Session->write('Training.course_id', $this->request->query['course_id']);
			}	else	{
				if($this->Session->check('Training.course_id'))	{
					$this->Course->id = $this->Session->read('Training.course_id');
				}
			}
			if((int)$this->Course->id)	{
				$this->Course->recursive = -1;
				$course = $this->Course->read();
				$this->set('course', $course);
				$this->set('course_id', $this->course_id);
			}
		}

		parent::beforeFilter();
	}

	function _validateRequest()	{
		if(!isset($this->Course->id))	{
			throw new BadRequestException(__('Invalid course; "No course detected"'));
		}
	}
}
