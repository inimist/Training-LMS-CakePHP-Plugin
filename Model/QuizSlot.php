<?php
App::uses('TrainingAppModel', 'Training.Model');
/**
 * QuizSlot Model
 *
 * @property QuizSlot $QuizSlot
 */
 class QuizSlot extends AppModel {

	public $useTable = 'quiz_slots';

	public $belongsTo = array(
		'Quiz', 
		//'Question'
	);

	public function noDuplicates($data)	{
		foreach($data as $k=>$row)	{
			$exists = $this->find('count', array('conditions'=>array('quiz_id'=>$row['quiz_id'], 'question_id'=>$row['question_id'])));
			if($exists >= 1) {unset($data[$k]);}
		}
		return $data;
	}
}