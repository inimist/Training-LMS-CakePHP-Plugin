<?php
App::uses('TrainingAppModel', 'Training.Model');
/**
 * Course Model
 *
 * @property Enrollment $Enrollment
 */
 class CoursesEnrollment extends TrainingAppModel {
	

	public $useTable = 'courses_enrollments';

	public $actsAs = array('Dateformat');

	public $belongsTo = array(
		'Course', 
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => array('id', 'username', 'full_name', 'first_name', 'last_name', 'email_address'),
			//'order' => 'first_name DESC',
			//'dependent' => true
		),
		'Creator' => array(
			'className' => 'User',
			'foreignKey' => 'creator_id',
			'conditions' => '',
			'fields' => array('id', 'username', 'full_name', 'first_name', 'last_name', 'email_address'),
			//'order' => 'first_name DESC',
			//'dependent' => true
		)
	);

/* public $hasOne = array(
	'Creator' => array(
			'className' => 'User',
			'foreignKey' => 'creator_id',
			'conditions' => '',
			'fields' => array('id', 'username', 'full_name', 'first_name', 'last_name', 'email_address'),
			//'order' => 'first_name DESC',
			//'dependent' => true
		)
	);
   */
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'enddate' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter an enddate for enrollment',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	function beforeSave($options = array())	{
		//calculating enddate from duration is no more needed. commented by surinder
	/*	if(isset($this->data[$this->alias]['duration']))	{
			if(trim($this->data[$this->alias]['duration'])!="")	{
				$this->data[$this->alias]['enddate'] = date('Y-m-d', strtotime($this->data[$this->alias]['startdate']  . ' +' . $this->data[$this->alias]['duration'] . ' months'));
			}	else	{
				$this->data[$this->alias]['enddate'] = null;
			}
		}
		*/
	}

public function beforeFind($queryData){

$defaultConditions = array('CoursesEnrollment.disabled !=' => 1); //if enrollment is disabled i.e. user is inactive
$queryData['conditions'] = array_merge($defaultConditions, $queryData['conditions']);

	return $queryData;

} 

}