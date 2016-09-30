<?php 
class TrainingSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
		'alias' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $aros = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
		'alias' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $aros_acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
		'aro_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'index'),
		'aco_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
		'_create' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'_read' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'_update' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'_delete' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'ARO_ACO_KEY' => array('column' => array('aro_id', 'aco_id'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $courses = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'category_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'creator_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'percentage' => array('type' => 'integer', 'null' => false, 'default' => '60', 'length' => 4, 'unsigned' => false),
		'created' => array('type' => 'timestamp', 'null' => false),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'signature' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'repeats' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'frequency' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 2, 'unsigned' => false, 'comment' => 'Every Month for now'),
		'source_type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 15, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'source_file_embeded' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'source_file_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'startdate' => array('type' => 'date', 'null' => true, 'default' => null),
		'due_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'views' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'user_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'quiz_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'completed_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'notcompleted_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'pastdue_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'question_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'quizquestion_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'minpassquestion_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'current_module' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'upload_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'master_course' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $courses_enrollments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'course_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'department_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'startdate' => array('type' => 'date', 'null' => true, 'default' => null),
		'enddate' => array('type' => 'date', 'null' => true, 'default' => null),
		'duration' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'utf8_general_ci', 'comment' => 'in 
months', 'charset' => 'utf8'),
		'last_access_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'creator_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'completed_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'result' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'leaddays' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'disabled' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'for inactive users only'),
		'set_reminder' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'reminder_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'rem_pre_month' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'rem_pre_week' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'rem_pre_day' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'rem_today' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'rem_post_daily' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'rem_post_weekly' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'rem_post_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'set_post_specific_date' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $courses_learn_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'course_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'courses_enrollment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'viewcount' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $question_answers = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'answer' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'fraction' => array('type' => 'decimal', 'null' => true, 'default' => '0.00000', 'length' => '10,5', 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $question_attempts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'slot' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false),
		'quiz_attempt_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'variant' => array('type' => 'integer', 'null' => true, 'default' => '1', 'unsigned' => false),
		'maxmark' => array('type' => 'decimal', 'null' => true, 'default' => '1.0000000', 'length' => '12,7', 'unsigned' => false),
		'minfraction' => array('type' => 'decimal', 'null' => true, 'default' => '0.0000000', 'length' => '12,7', 'unsigned' => false),
		'maxfraction' => array('type' => 'decimal', 'null' => true, 'default' => '1.0000000', 'length' => '12,7', 'unsigned' => false),
		'questionsummary' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'rightanswer' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'responsesummary' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $question_pair_matches = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'question_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'question_pair_left' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'question_pair_right' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'pair_order' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'display_order' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 2, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $question_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'qtype' => array('type' => 'string', 'null' => true, 'default' => 'single', 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 1, 'unsigned' => false),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $questions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'course_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'defaultmark' => array('type' => 'decimal', 'null' => true, 'default' => '1.00000', 'length' => '10,5', 'unsigned' => false),
		'penalty' => array('type' => 'decimal', 'null' => true, 'default' => '0.3333333', 'length' => '12,7', 'unsigned' => false),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'question_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'creator_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $quiz_attempts = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
		'quiz_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
		'courses_enrollment_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'attempt' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 6, 'unsigned' => false),
		'currentpage' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'preview' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 3, 'unsigned' => false),
		'state' => array('type' => 'string', 'null' => false, 'default' => 'inprogress', 'length' => 16, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'signed_by_user' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'timestart' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'timefinish' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'timecheckstate' => array('type' => 'biginteger', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'sumgrades' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,5', 'unsigned' => false),
		'correctquestions' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => false),
		'total_questions' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => false),
		'result' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'earned_grade' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '5,2', 'unsigned' => false),
		'archived' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'locked' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'unlock_requested' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'unlocked' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $quiz_slots = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'slot' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'quiz_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'page' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'maxmarks' => array('type' => 'decimal', 'null' => true, 'default' => '1.00000', 'length' => '10,5', 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $quizzes = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'course_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'is_default' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'timeopen' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'timeclose' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'timelimit' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'graceperiod' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'attempts' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'questionsperpage' => array('type' => 'integer', 'null' => true, 'default' => '1', 'unsigned' => false),
		'sumgrades' => array('type' => 'decimal', 'null' => true, 'default' => '0.00000', 'length' => '10,5', 'unsigned' => false),
		'grade' => array('type' => 'decimal', 'null' => true, 'default' => '10.00000', 'length' => '10,5', 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'minpassgrade' => array('type' => 'decimal', 'null' => false, 'default' => '6.00000', 'length' => '10,5', 'unsigned' => false),
		'minpassquestions' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => false),
		'showpassfail' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'question_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $test_question_attempts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'slot' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false),
		'test_quiz_attempt_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'variant' => array('type' => 'integer', 'null' => true, 'default' => '1', 'unsigned' => false),
		'maxmark' => array('type' => 'decimal', 'null' => true, 'default' => '1.0000000', 'length' => '12,7', 'unsigned' => false),
		'minfraction' => array('type' => 'decimal', 'null' => true, 'default' => '0.0000000', 'length' => '12,7', 'unsigned' => false),
		'maxfraction' => array('type' => 'decimal', 'null' => true, 'default' => '1.0000000', 'length' => '12,7', 'unsigned' => false),
		'questionsummary' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'rightanswer' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'responsesummary' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $test_quiz_attempts = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
		'quiz_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
		'courses_enrollment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'attempt' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 6, 'unsigned' => false),
		'currentpage' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'preview' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3, 'unsigned' => false),
		'state' => array('type' => 'string', 'null' => false, 'default' => 'inprogress', 'length' => 16, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'timestart' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'timefinish' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'timecheckstate' => array('type' => 'biginteger', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => false),
		'sumgrades' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,5', 'unsigned' => false),
		'correctquestions' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => false),
		'total_questions' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => false),
		'result' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'earned_grade' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '5,2', 'unsigned' => false),
		'archived' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'locked' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'unlock_requested' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'unlocked' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}
