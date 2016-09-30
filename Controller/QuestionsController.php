<?php
class QuestionsController extends TrainingAppController {
	var $name = 'Questions';
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Spreadsheet.PhpExcel');

  public $helpers = array('TinyMCE.TinyMCE');

	var $course_id = null;
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->_validateRequest();
	}

	function beforeRender() {
		$this->loadModel('Training.Quiz');
		$this->set('_quizSettings', $this->Quiz->getQuizSettings());
		parent::beforeRender();
	}

/**
 * admin_index method
 *
 * @return void
 */
	function admin_index(){
		$this->Question->recursive = 0;
		if($this->Course->id)	{
			$options = array('conditions'=>array('course_id'=>$this->Course->id));
			$this->paginate = $options;
		}	else	{
			throw new NotFoundException(__('Missing Course ID!'));
		}
		//pr($this->Paginator->paginate());
		//pr($this->Question->find('all'));
			$questions = $this->Paginator->paginate();
			foreach($questions as $i=>$question){
				$correctAnswer = $this->Question->QuestionAnswer->getCorrectAnswer($question['Question']['id']);
				$questions[$i]['QuestionAnswer'] = $correctAnswer ;
			}
		$this->set('questions', $questions);
		$this->set(compact('course_id'));
	}

/**
 * admin_add method
 *
 * @return void
 */

	function admin_add()	{

		if ($this->request->is('post')) {
			$this->Question->create();
			//debug($this->request->data);exit;
			if ($this->Question->save($this->request->data)) {

				$this->Question->Course->updateQuestionCount( $this->Course->id );
				
				$this->log("##authuser## added a question ##action-edit## to course ##Course:{$this->Course->id}##", "system");
				$this->Session->setFlash(__('The Question has been saved.'));

				$is_multiple_choice = $this->Question->QuestionType->isMultipleChoice($this->request->data['Question']['question_type_id']);
				$is_matching_pairs = $this->Question->QuestionType->isMatchingPairs($this->request->data['Question']['question_type_id']);
				$is_truefalse = $this->Question->QuestionType->isTrueFalse($this->request->data['Question']['question_type_id']);

				if($is_truefalse)	{
					//insert default answers for True/False type
					$answers = array(
						array('question_id'=>$this->Question->id, 'answer'=>'True', 'fraction'=>1),
						array('question_id'=>$this->Question->id, 'answer'=>'False', 'fraction'=>0)
					);
					$this->Question->QuestionAnswer->saveAll($answers);
				}

				if($is_multiple_choice || $is_truefalse || $is_matching_pairs)	{
					return $this->redirect(array('action' => 'edit', $this->Question->id));
				}
				
				return $this->redirect(array('action' => 'index', '?'=>array('course_id'=>$this->request->data['Question']['course_id'])));
			} else {
				$this->Session->setFlash(__('The Question could not be saved. Please, try again.'));
			}
		}
		$courses = $this->Course->find('list');
		$questionTypes = $this->Question->QuestionType->find('list');
		$this->set(compact('questionTypes', 'courses'));

		$this->request->data['Question']['course_id'] = $this->Course->id;
	}

	/**
 * stats method
 *
 * @return void
 */
	public function admin_questions_stats($id = null)  {
		if($this->Course->id){
			$courseId = $this->Course->id;
			}else	{
			throw new NotFoundException(__('Missing Course ID!'));
		}
		$this->Question->unbindModel(array('belongsTo'=>array('Course')));
		$this->Question->bindModel(array('hasMany'=>array('QuestionAttempt')));
		$this->loadModel('Training.Question');
		$questions = $this->Question->find('all', array('conditions'=>array('Question.course_id'=>$courseId)));
	//	$tempresult=array('correct','incorrect');
			$temp=array();
		  foreach( $questions as $question )  {

			 foreach($question['QuestionAttempt'] as $questionAttempt )  {
				$_question = $question;
				$_question['QuestionAttempt'] = $questionAttempt;
				$answer = $this->Question->question_check_correct($_question );
				//debug($question);
				//debug($answer);
				//debug($question['Question']);
				$question_id = $question['Question']['id'];
				
				if(!isset($temp[$question_id]))	{
					$temp[$question['Question']['id']] = array_merge($question['Question'], array('correct'=>0, 'incorrect'=>0));
				}

				if($answer == 'correct')  {
					$temp[$question_id]['correct']++;
				} 	else {
					$temp[$question_id]['incorrect']++;
				}
			}
		}



	/*	 $correct = array();
		foreach ($temp as $key => $row)
		{  
			foreach ($row as $ke => $val) {
				
			if($ke=='correct')  {
				$correct['correct'][$row['id']] =  $val;
			}else {
				$correct['incorrect'][$row['id']] =  $val;

			}

			}
			
		} rsort($correct);  */
		//pr($correct);
		//array_multisort($correct, SORT_DESC, $temp);
		//pr($correct);
		//pr($temp);
		//$this->set('questions', $this->Paginator->paginate());
		//pr($$row);
	
		$this->set('questions', $temp);

		//Export Question Stats
		if(isset($this->request->data['Export']) && $this->request->data['Export'] =='Export Stats'){
				$results = $temp;
				$coursename = $this->Question->Course->field('name', array('id'=>$courseId));
				$this->PhpExcel->createWorksheet() ->setDefaultFont('Calibri', 12);
				//$this->PhpExcel->mergeCells('A1:L1');
				//$this->PhpExcel->getAlignment('A1')->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->PhpExcel->getActiveSheet(0)->mergeCells('A1:L1');
				$this->PhpExcel->addTableRow(array('Question Stats for "'.$coursename.'"'),  array('bold' => true));

				$table = array(
									array('label' => __('#'), 'filter' => true),
									array('label' => __('Title'), 'filter' => true),
									array('label' => __('Total Attempts')),
									array('label' => __('Correct'), 'width' => 50, 'wrap' => true),
									array('label' => __('Incorrect')),
									array('label' => __('% Passed'))
							);
					$this->PhpExcel->addTableHeader($table, array('name' => 'AuditFindingReport', 'bold' => true));
					$i=0;
					foreach ($results as $result) {
							$i++;
							$total = $result['correct'] + $result['incorrect'];
							$percent = (int) (($result['correct']/$total)*100);
						$tmpdata = array ($i,
										$result['title'],
										$total,
										$result['correct'],
										$result['incorrect'],
										$percent.'%'
									);

					$this->PhpExcel->addTableRow($tmpdata);
					}
				//$this->PhpExcel->getActiveSheet()->insertNewRowBefore(1, 1);
				$this->PhpExcel->addTableFooter()->output('QuestionStats-'.$coursename.'-'.date("Fj-Y").'.xlsx');
		}
			
	}
		
	
/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Question->exists($id)) {
			throw new NotFoundException(__('Invalid question'));
		}

    $question_has_attempts = false;

		$num_answers = 5; //used in case of multiple choice questions
		if ($this->Question->hasAttempts( $id )) {
			$this->Session->setFlash(__('Question has attempts'));
			//return $this->redirect( $this->referer() );
      $question_has_attempts = true;
		}
		if ($this->request->is(array('post', 'put')) && !$question_has_attempts) {
			//debug($this->request->data); exit;
			
			if ($this->Question->save($this->request->data)) {

				switch($this->request->data['Question']['submit']):
					case 'Add 3 More Choices';
						$num_answers = $this->request->data['Question']['num_answers'] + 3;
					break;
					case 'Save Changes':
						$removables = array(); 
						if(isset($this->request->data['Question']['remove_answers']) && $this->request->data['Question']['remove_answers']) 
						$removables = explode(',', $this->request->data['Question']['remove_answers']); //if user has removed any anwer then assigne ids to removal array.
						if(isset($this->request->data['QuestionAnswer'])){
							foreach($this->request->data['QuestionAnswer'] as $i => $answer){
								if(isset($answer['answer']) && trim($answer['answer'])=="") {
									//if it is a previously filled answer field we need to get the id so this question..
									//..can be deleted from the database table
									if(@$this->request->data['QuestionAnswer'][$i]['id'])	{
										$removables[] = $this->request->data['QuestionAnswer'][$i]['id'];
									}
									unset($this->request->data['QuestionAnswer'][$i]);
									//unset($this->request->data['QuestionAnswer'][$i]);
								}
							}
							$existsCorrectChoice = false;
							if($this->request->data['Question']['question_type_id'] == 5){
								foreach($this->request->data['QuestionAnswer'] as $i => $answer){
									if(isset($answer['correct_anwer']) && $answer['correct_anwer']){
											$this->request->data['QuestionAnswer'][$i]['fraction'] = 1;
											$existsCorrectChoice = true;
										}else{
											$this->request->data['QuestionAnswer'][$i]['fraction'] = 0;
										}
								}
							}
							if($this->request->data['Question']['question_type_id'] == 6){
								$correct_ans_num = isset($this->request->data['Question']['correct_ans_num']) ? $this->request->data['Question']['correct_ans_num'] : 1;
								$correct_choice_grade = number_format( 1/$correct_ans_num , 5);
								foreach($this->request->data['QuestionAnswer'] as $i => $answer){
									if(isset($answer['correct_anwer']) && $answer['correct_anwer']){
											$this->request->data['QuestionAnswer'][$i]['fraction'] = $correct_choice_grade;
											$existsCorrectChoice = true;
										}else{
											$this->request->data['QuestionAnswer'][$i]['fraction'] = 0;
										}
									}
								}
							if($this->request->data['Question']['question_type_id'] == 4){
								$existsCorrectChoice = true;
								}
							if(!$existsCorrectChoice){
							$this->Session->setFlash(__('Question has atleast one correct answer'));
							return $this->redirect( $this->referer() );
								}
						}

						if(isset($this->request->data['QuestionPairMatch'])){
								//debug($this->request->data); exit;
								$completePairs = 0;
							foreach($this->request->data['QuestionPairMatch'] as $i => $answer){
								//check for empty options
								if((isset($answer['question_pair_left']) && trim($answer['question_pair_left'])=="") || (isset($answer['question_pair_right']) && trim($answer['question_pair_right'])=="")) {
									if(@$this->request->data['QuestionPairMatch'][$i]['id']){
										$removables[] = $this->request->data['QuestionPairMatch'][$i]['id'];
									}
									unset($this->request->data['QuestionPairMatch'][$i]);
								}else{
									$completePairs++;
								}
							}
							if($completePairs <= 0){
								$this->Session->setFlash(__('Question has atleast one complete match pair'));
								$this->redirect( $this->referer() );
							}
						}
						//debug($removables);
						//debug($this->request->data); exit;
						if($this->Question->saveAll($this->request->data)) {
							//exit;
							if($removables)	{
								if($this->request->data['Question']['question_type_id'] == 7) $this->Question->QuestionPairMatch->deleteAll(array('id'=>$removables));
								else $this->Question->QuestionAnswer->deleteAll(array('id'=>$removables));
							}
							$this->log("##authuser## updated a question ##action-edit## in course ##Course:{$this->Course->id}##", "system");
							$this->Session->setFlash(__('The question has been saved.'));
							return $this->redirect(array('action' => 'index', '?'=>array('course_id' => $this->request->data['Question']['course_id'])));
						} else {
							$this->Session->setFlash(__('The question could not be saved. Please, try again.'));
						}
					break;
				endswitch;
			} else {
				$this->Session->setFlash(__('The question could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Question.' . $this->Question->primaryKey => $id));
			$this->request->data = $this->Question->find('first', $options);
      if($question_has_attempts) {
        $this->Session->setFlash(__('Cannot save question. Question has attempts'));
      }
		}

		//debug($this->request->data);
		$is_matching_pairs = $this->Question->QuestionType->isMatchingPairs($this->request->data['Question']['question_type_id']);
		if($is_matching_pairs){
				$stored_num_answers = sizeof($this->request->data['QuestionPairMatch']); //if exists
				if($stored_num_answers > 0 && !isset($this->request->data['Question']['num_answers']))	{
						$num_answers = $stored_num_answers;
				}
			}else{
				$stored_num_answers = sizeof($this->request->data['QuestionAnswer']); //if exists
				if($stored_num_answers > 0 && !isset($this->request->data['Question']['num_answers']))	{
						$num_answers = $stored_num_answers;
				}
			}
		//$questionTypes = $this->Question->QuestionType->find('list');
		$fractions = $this->Question->QuestionAnswer->fractionList();
		$penalties = $this->Question->QuestionAnswer->penalityList();

		$questionTypes = $this->Question->QuestionType->find('list');

		$is_multiple_choice = $this->Question->QuestionType->isMultipleChoice($this->request->data['Question']['question_type_id']);

		if($is_multiple_choice)	{
			//$questionTypes = $this->Question->QuestionType->onlyMultipleTypes($questionTypes);
		}
		
		$is_truefalse = $this->Question->QuestionType->isTrueFalse($this->request->data['Question']['question_type_id']);

		if($is_truefalse)	{
			//$questionTypes = $questionTypes = $this->Question->QuestionType->onlyTFTypes($questionTypes);
		}

		$questionTypes = $this->Question->QuestionType->filterTypesForEdit($questionTypes, $this->request->data['Question']['question_type_id']);

		$this->set(compact('fractions', 'penalties', 'num_answers', 'questionTypes', 'is_multiple_choice', 'is_truefalse', 'is_matching_pairs', 'question_has_attempts'));
	}

/**
 * admin_answers method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_answers($id = null) {
		if (!$this->Question->exists($id)) {
			throw new NotFoundException(__('Invalid question'));
		}

		$options = array('conditions' => array('Question.' . $this->Question->primaryKey => $id));
		$question =  $this->Question->find('first', $options);

		$num_answers = 5;

		if ($this->request->is(array('post', 'put'))) {

			//debug($this->request->data);exit;

			switch($this->request->data['Question']['submit']):

				case 'Add 3 More Choices';
					$num_answers = $this->request->data['Question']['num_answers'] + 3;
				break;
				case 'Save Choices':

					$removables = array();

					foreach($this->request->data['QuestionAnswer'] as $i => $answer)	{
						if(trim($answer['answer'])=="") {

							//if it is a previously filled answer field we need to get the id so this question..
							//..can be deleted from the database table
							if(@$this->request->data['QuestionAnswer'][$i]['id'])	{
								$removables[] = $this->request->data['QuestionAnswer'][$i]['id'];
							}

							unset($this->request->data['QuestionAnswer'][$i]);
							unset($this->request->data['QuestionAnswer'][$i]);
						}
					}

					//debug($this->request->data);exit;

					if ($this->Question->saveAll($this->request->data)) {
						
						if($removables)	{
							$this->Question->QuestionAnswer->deleteAll(array('id'=>$removables));
						}
						$this->log("##authuser## updated answers for question ##action-edit##", "system");
						$this->Session->setFlash(__('The question has been saved.'));
						return $this->redirect(array('action' => 'index'));
					} else {
						$this->Session->setFlash(__('The question could not be saved. Please, try again.'));
					}
				break;
			endswitch;
		} else {
			$this->request->data = $question;
		}

		$stored_num_answers = sizeof($this->request->data['QuestionAnswer']); //if exists
		if($stored_num_answers > 0 && $stored_num_answers > $num_answers)	{
				$num_answers = $stored_num_answers;
		}
		$questionTypes = $this->Question->QuestionType->find('list');
		$fractions = $this->Question->QuestionAnswer->fractionList();
		//$this->set(compact('questionTypes'));
		$this->set(compact('question', 'questionTypes', 'fractions', 'num_answers'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Question->id = $id;
		if (!$this->Question->exists()) {
			throw new NotFoundException(__('Invalid question'));
		}
		if ($this->Question->hasAttempts( $id )) {
			$this->Session->setFlash(__('Question has attempts'));
			return $this->redirect( $this->referer() );
		}
		$course_id = $this->Question->field('course_id');
		$this->request->allowMethod('post', 'delete');
		if ($this->Question->delete()) {
			$this->log("##authuser## deleted question ##action-edit## from course ##Course:{$course_id}##", "system");
			$this->Session->setFlash(__('The question has been deleted.'));
		} else {
			$this->Session->setFlash(__('The question could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index', '?'=>array('course_id'=>$this->Course->id)));
	}

/**
 * admin_undelete method
 *
 * @description Un-delete a question
 * @param string $id
 * @return redirect
 */

	public function admin_undelete($id) {
		$this->Question->id = $id;
		if (!$this->Question->exists()) {
			throw new NotFoundException(__('Invalid Question'));
		}

		$this->request->allowMethod('post', 'delete');
		if ($this->Question->undelete( $id )) {
      $this->log("##authuser## retrieved question ##action-edit##", 'system');
			$this->Session->setFlash(__('Question has been retrieved.'));
		} else {
			$this->Session->setFlash(__('Question could not be retrieved. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index', '?'=>array('course_id'=>$this->Course->id)));
	}

/**
 * admin_purge method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_purge($id) {
		$this->Question->id = $id;
		if (!$this->Question->exists()) {
			throw new NotFoundException(__('Invalid question'));
		}

		if ($this->Question->hasAttempts( $id )) {
			$this->Session->setFlash(__('Question has attempts'));
			return $this->redirect( $this->referer() );
		}

		$this->request->allowMethod('post', 'delete');

		$this->Question->beforePurge( $id );

		if ($this->Question->delete( $id )) {
      $this->log("##authuser## permanently deleted a question #" . $id, 'system');
			$this->Session->setFlash(__('Question has permanently been deleted.'));
		} else {
			$this->Session->setFlash(__('The question could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index', '?'=>array('course_id'=>$this->Course->id)));
	}	


/*
 * admin_copy_question method
 *
 * A processor for getting Questions for a selected course.
 * Called using ajax, usually
 *
 * @return void
 */
	function admin_get_questions(){

		if($this->request->is('ajax')){
			$this->layout= 'ajax';
		}

		if($this->request->is(array('post', 'put'))){
			if(isset($this->request->data['course_id']) && $this->request->data['course_id']){
				$options =array('conditions'=> array('Question.course_id'=>$this->request->data['course_id']));
				$questions = $this->Question->find('list', $options);
			
				$this->set(compact('questions'));
			}
		}
	}

/**
 * admin_copy_questions method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_copy_questions() {
		if (!$this->Course->exists()) {
			throw new NotFoundException(__('Invalid Course'));
		}
		$course_id = $this->Course->id;
		if ($this->request->is('post')) {
			 $data = $this->request->data;
			 if(isset($data['Questions']) && $data['Question']['course_id']){
				 $count = 0;
				foreach($data['Questions'] as $question){
					$options = array('conditions'=>array('Question.id'=>$question['question_id'], 'Question.course_id'=>$data['Question']['course_id']));
					$copyQuestion = $this->Question->find('first', $options);

					unset($copyQuestion['QuestionType'], $copyQuestion['Course'], $copyQuestion['Question']['id']);
					$copyQuestion['Question']['course_id'] = $course_id;
					$copyQuestion['Question']['creator_id'] = $this->Auth->user('id');
								 //debug($copyQuestion); exit;
					if($copyQuestion['Question']['question_type_id'] != 7){
							if(isset($copyQuestion['QuestionPairMatch'])) unset($copyQuestion['QuestionPairMatch']);
							foreach($copyQuestion['QuestionAnswer'] as $i=>$answer){
								unset($answer['id'], $answer['question_id']);
								$copyQuestion['QuestionAnswer'][$i] = $answer;
							}
						}else{
							if(isset($copyQuestion['QuestionAnswer'])) unset($copyQuestion['QuestionAnswer']);
							foreach($copyQuestion['QuestionPairMatch'] as $k=>$pair){
								unset($pair['id'], $pair['question_id']);
								$copyQuestion['QuestionPairMatch'][$k] = $pair;
							}
						}
					if($this->Question->saveAssociated($copyQuestion)) $count++;
					}
				$this->log("##authuser## copied ".$count." questions from course ##Course:{$data['Question']['course_id']}## to ##Course:{$course_id}##", "system");
				$this->Session->setFlash(__($count.' Questions copied successfully.'));
			 }
			$this->redirect(array('action'=>'index'));
		}
	}

	/**
 * admin_matching_question method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
/* public function admin_matching_question(){

	} */


}