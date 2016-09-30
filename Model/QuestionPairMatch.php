<?php
App::uses('TrainingAppModel', 'Training.Model');
/**
 * QuestionPairMatch Model
 *
 * @property Question $Question
 * @property QuestionPairLeft $QuestionPairLeft
 * @property QuestionPairRight $QuestionPairRight
 */
class QuestionPairMatch extends TrainingAppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Question' => array(
			'className' => 'Training.Question',
			'foreignKey' => 'question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/*
	 public $hasOne = array(
		'QuestionPairLeft' => array(
			'className' => 'Training.QuestionPairLeft',
			'foreignKey' => 'question_pair_match_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'QuestionPairRight' => array(
			'className' => 'Training.QuestionPairRight',
			'foreignKey' => 'question_pair_match_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		));
 */
}
