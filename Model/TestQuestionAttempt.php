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
 * TestQuestionAttempt Model
 *
 * @property Training $TestQuestionAttempt
 */

 class TestQuestionAttempt extends TrainingAppModel {

		public $useTable = 'test_question_attempts';

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
	function answerOutcome($TestQuestionAttempt)	{
		//debug($TestQuestionAttempt);
		return $TestQuestionAttempt['rightanswer']==$TestQuestionAttempt['responsesummary'] ? 'correct' : 'incorrect';
	}


	function sumgrades($quiz_attempt_id)	{
		//$QuizAttempt = ClassRegistry::init('Training.QuizAttempt');
		//$QuizAttempt->id = $quiz_attempt_id;
		//debug($quiz_attempt_id);
		$this->bindModel(array('belongsTo'=>array('Question')));
		$options = array('conditions'=>array('TestQuestionAttempt.quiz_attempt_id'=>$quiz_attempt_id));
		$TestQuestionAttempts = $this->find('all', $options);
		//debug($TestQuestionAttempts);
	}
}