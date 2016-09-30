<?php
App::uses('Sanitize', 'Utility');
class TrainingHelper extends AppHelper {

/**
 * uses Html & Form helper
 */
	
	public $helpers = array('Html', 'Form', 'TinyMCE.TinyMCE', 'Utility'); //'TinyMCE.TinyMCE', 'Session', 'Time'

/**
 * @var $_quizSettings
 */
 
	public $_quizSettings;

	function renderQuestion ( $question )	{
		//debug($question);
		//debug($this->request->data);
		$i = $question['Question']['id'];
		$html = '';
		$html .= '<p><strong>'.$question['QuizSlot']['slot'].'.</strong> ' . $question['Question']['title']. '</p>
		<div class="q-desc">' . $question['Question']['description'] .'</div>
		<div class="section-answer padBottom10 padLeft10">';
		
		$questiontype = $this->getQType( $question );

		$html .= $this->Form->hidden('QuestionAttempt.'.$i.'.question_id', array('value'=>$question['Question']['id']));
		$html .= $this->Form->hidden('QuestionAttempt.'.$i.'.rightanswer', array('value'=>'unknown'));
		//debug($questiontype);

		switch( $questiontype ):
			case 'truefalse':
				$html .= $this->Form->radio('QuestionAttempt.'.$i.'.responsesummary', array('True'=>'True', 'False'=>'False'), array('label'=>false, 'legend'=>false, 'separator' => '&nbsp;&nbsp;&nbsp;'));
			break;
			case 'multiplechoice-one':
				$response = array();
				foreach($question['QuestionAnswer'] as $answer)	{
					$response[$answer['answer']] = $answer['answer'];
				}
				$html .= $this->Form->radio('QuestionAttempt.'.$i.'.responsesummary', $response, array('label'=>false, 'legend'=>false, 'between'=>'&nbsp;&nbsp;', 'separator' => '<br />'));
			break;
			case 'multiplechoice-multi':
				$response = array();
				foreach($question['QuestionAnswer'] as $answer)	{
					$response[$answer['answer']] = $answer['answer'];
				}
				//debug($response);
				$selected = array();
				if(@isset($this->request->data['QuestionAttempt'][$i]))	{
					$selected = explode(';', $this->request->data['QuestionAttempt'][$i]['responsesummary']);
				}
				//debug($selected);

				$html .= $this->Form->input('QuestionAttempt.'.$i.'.responsesummary', array('multiple'=>'checkbox', 'label'=>false, 'options'=>$response, 'selected' => $selected, 'legend'=>false, 'between'=>'&nbsp;&nbsp;', 'separator' => '<br />'));
			break;
			case 'multiplelines-rich':
				$html .= $this->Form->textarea('QuestionAttempt.'.$i.'.responsesummary', array('label'=>false, 'legend'=>false));
				$this->TinyMCE->editor(array(
        'theme' => 'modern', 
        'skin'=>'lightgray', 
        'mode' => "exact", 
        'elements' => 'QuestionAttempt'.$i.'Responsesummary',
        'plugins' => 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste'
        ));
			break;
			case 'multiplelines':
				$html .= $this->Form->input('QuestionAttempt.'.$i.'.responsesummary', array('label'=>false, 'class'=>'form-control'));
			break;
			case 'singleline':
				$html .= $this->Form->input('QuestionAttempt.'.$i.'.responsesummary', array('type'=>'text', 'label'=>false, 'class'=>'form-control'));
			break;
			case 'matching-pairs':
				$html .= $this->renderMatchpairQuestion($question, $i );
				$html .= $this->Form->hidden('QuestionAttempt.'.$i.'.responsesummary', array('id'=>'matchingPairResponse'));
			break;

		endswitch;

		$html .= '</div>';

		$html .= $this->Form->hidden('QuestionAttempt.'.$i.'.id');
		$html .= $this->Form->hidden('QuestionAttempt.'.$i.'.maxmark');
		$html .= $this->Form->hidden('QuestionAttempt.'.$i.'.minfraction');
		$html .= $this->Form->hidden('QuestionAttempt.'.$i.'.maxfraction');
		$html .= $this->Form->hidden('QuestionAttempt.'.$i.'.slot', array('value'=>$question['QuizSlot']['slot']));
		$html .= $this->Form->hidden('QuestionAttempt.'.$i.'.quiz_attempt_id', array('value'=>$this->request->data['QuizAttempt']['id']));

		echo $html;
	}

	function renderMatchpairQuestion($matching_pair, $i, $testattempt = false){
		//debug($matching_pair); 
		$html ="";
		$questionAttempt = $testattempt ? 'TestQuestionAttempt' : 'QuestionAttempt';
		$html .= '<div id="matchingQuestion" class="matching-question review col-xs-18 col-md-12">
								<div class="row matching-pairs">
								<div class="col-xs-4 col-md-3 padRight0">';
		foreach($matching_pair['QuestionPairMatch'] as $key => $pair){
				 $pair_order = $pair['pair_order'];
				$html .= '<p class="leftpair" id="leftpair_'.$key.'">'.$pair['question_pair_left'].'</p>';
				$html .= $this->Form->hidden($questionAttempt.'.'.$i.'.pairmatchresponse.'.$pair_order.'.0', array('value'=>$pair['question_pair_left']));
				$html .= $this->Form->hidden($questionAttempt.'.'.$i.'.pairmatchresponse.'.$pair_order.'.1', array('class'=>'leftpair_'.$key.'-answer'));
   		}
		
		$html .= '</div>
							<div class="col-xs-4 col-md-3 pull-right">';
			$rightpairs =  $matching_pair['QuestionPairMatch']; //before shuffling the array copy it.
			//shuffle($rightpairs);
			//debug($rightpairs);
			usort($rightpairs, function ($a, $b){
					return $a['display_order'] - $b['display_order'];
			});
			
			foreach($rightpairs as $key => $pair){
				
				$html .= '<p class="rightpair" id="rightpair_'.$key.'">'.$pair['question_pair_right'].'</p>';
   		}
		
		$html .= '</div>
							</div>
						</div>';
		return $html;

	}



		function renderTestQuestion ( $question )	{
		//debug($question);
		//debug($this->request->data);
		$i = $question['Question']['id'];
		$html = '';
		$html .= '<p><strong>'.$question['QuizSlot']['slot'].'.</strong> ' . $question['Question']['title']. '</p>
		<div class="q-desc">' . $question['Question']['description'] .'</div>
		<div class="section-answer padBottom10 padLeft10">';
		
		$questiontype = $this->getQType( $question );

		$html .= $this->Form->hidden('TestQuestionAttempt.'.$i.'.question_id', array('value'=>$question['Question']['id']));
		$html .= $this->Form->hidden('TestQuestionAttempt.'.$i.'.rightanswer', array('value'=>'unknown'));
		//debug($questiontype);

		switch( $questiontype ):
			case 'truefalse':
				$html .= $this->Form->radio('TestQuestionAttempt.'.$i.'.responsesummary', array('True'=>'True', 'False'=>'False'), array('label'=>false, 'legend'=>false, 'separator' => '&nbsp;&nbsp;&nbsp;'));
			break;
			case 'multiplechoice-one':
				$response = array();
				foreach($question['QuestionAnswer'] as $answer)	{
					$response[$answer['answer']] = $answer['answer'];
				}
				$html .= $this->Form->radio('TestQuestionAttempt.'.$i.'.responsesummary', $response, array('label'=>false, 'legend'=>false, 'between'=>'&nbsp;&nbsp;', 'separator' => '<br />'));
			break;
			case 'multiplechoice-multi':
				$response = array();
				foreach($question['QuestionAnswer'] as $answer)	{
					$response[$answer['answer']] = $answer['answer'];
				}
				//debug($response);
				$selected = array();
				if(@isset($this->request->data['TestQuestionAttempt'][$i]))	{
					$selected = explode(';', $this->request->data['TestQuestionAttempt'][$i]['responsesummary']);
				}
				//debug($selected);

				$html .= $this->Form->input('TestQuestionAttempt.'.$i.'.responsesummary', array('multiple'=>'checkbox', 'label'=>false, 'options'=>$response, 'selected' => $selected, 'legend'=>false, 'between'=>'&nbsp;&nbsp;', 'separator' => '<br />'));
			break;
			case 'multiplelines-rich':
				$html .= $this->Form->textarea('TestQuestionAttempt.'.$i.'.responsesummary', array('label'=>false, 'legend'=>false));
				$this->TinyMCE->editor(array(
        'theme' => 'modern', 
        'skin'=>'lightgray', 
        'mode' => "exact", 
        'elements' => 'TestQuestionAttempt'.$i.'Responsesummary',
        'plugins' => 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste'
        ));
			break;
			case 'multiplelines':
				$html .= $this->Form->input('TestQuestionAttempt.'.$i.'.responsesummary', array('label'=>false, 'class'=>'form-control'));
			break;
			case 'singleline':
				$html .= $this->Form->input('TestQuestionAttempt.'.$i.'.responsesummary', array('type'=>'text', 'label'=>false, 'class'=>'form-control'));
			break;
			case 'matching-pairs':
				$html .= $this->renderMatchpairQuestion($question, $i, true);
				$html .= $this->Form->hidden('TestQuestionAttempt.'.$i.'.responsesummary', array('id'=>'matchingPairResponse'));
			break;
		endswitch;

		$html .= '</div>';

		$html .= $this->Form->hidden('TestQuestionAttempt.'.$i.'.id');
		$html .= $this->Form->hidden('TestQuestionAttempt.'.$i.'.maxmark');
		$html .= $this->Form->hidden('TestQuestionAttempt.'.$i.'.minfraction');
		$html .= $this->Form->hidden('TestQuestionAttempt.'.$i.'.maxfraction');
		$html .= $this->Form->hidden('TestQuestionAttempt.'.$i.'.slot', array('value'=>$question['QuizSlot']['slot']));
		$html .= $this->Form->hidden('TestQuestionAttempt.'.$i.'.test_quiz_attempt_id', array('value'=>$this->request->data['TestQuizAttempt']['id']));

		echo $html;
	}


/**
 * getQType Get question type
 *
 * @param $question mixed
 * @return string
 */

	function getQType ( $question )	{
		if( isset($question['QuestionType']) ) return $question['QuestionType']['qtype'];
		if( isset($question['qtype']) ) return $question['qtype'];
	}

/**
 * setQuizSettings Get Quiz settings
 *
 * @param $question mixed
 * @return void
 */

	function setQuizSettings ( $quizSettings )	{
		$this->_quizSettings = $quizSettings;
	}

	function renderQuestionAttempt ( $questionAttempt , $layout=null)	{
		
		$i = $questionAttempt['Question']['id'];
		$questiontype = $this->getQType( $questionAttempt );
		$answerStatus = $this->getQAStatus( $questionAttempt );
		//debug($answerStatus);
		$printStatus = $this->formatQAStatus( $answerStatus );
    //debug($printStatus);
		//$marksObtained = 

	?>

    <div class="que multichoice deferredfeedback incorrect">
      <div class="info">
        <h3 class="no">Question <span class="qno"><?php echo $questionAttempt['slot']; ?></span></h3>
        <div class="state"><?php echo $printStatus; ?></div>

				<?php if($this->_quizSettings['usegrading']): ?>

        <div class="grade">Mark <?php echo float($questionAttempt['sumgrades']); ?> out of <?php echo float($questionAttempt['maxmark'], 0); ?><?php

				if($this->Utility->hasPermission('quizzes.admin_edit_marks') && $this->editMarkAllowed($questionAttempt['QuestionType']['qtype']))	{
					echo '<span class="edit-marks">&nbsp;(';
					echo $this->Html->link(__('Edit'), array('controller'=>'quizzes', 'action'=>'edit_marks', $questionAttempt['id'], 'admin'=>true), array("data-toggle"=>"modal", "data-target"=>"#editMarks", 'onclick'=>'return false'));
					echo ')</span>';
				}
		?></div>
				<?php endif; ?>
				</div>

      <div class="content">
        <div class="formulation">
          <h4 class="accesshide">Question text</h4>
          <div class="qtext">
            <p><?php echo $questionAttempt['Question']['title']; ?></p>
          </div>
          <div class="ablock">
		<?php
		$mp_answerClass = '';
		$answerHTML = '';	$prompt = '';
		switch( $questiontype ):
			case 'truefalse':
			//debug($questionAttempt['rightanswer']);
			//debug($questionAttempt['responsesummary']);
				foreach($questionAttempt['QuestionAnswer'] as $answer)	{

					//get correctness for individual answers
					$correctness = $this->getAnswersCorrectness($questionAttempt, $answer);
					$checked = $questionAttempt['responsesummary'] == $answer['answer'] ? 'checked="checked"' : '';
					$roclass = $this->getCorrectnessCSS($checked, $correctness);

					//debug($correctness);

					$answerHTML .= '<div class="r0 '.$roclass.'"><input type="radio" name="QuestionAttempt.'.$i.'.responsesummary" disabled="disabled" value="'.$answer['answer'].'" '.$checked.'><label for="q11:3_answer2">'.$answer['answer'].'</label>';
					$answerHTML .= $this->correctnessIcon($checked, $correctness);					
					$answerHTML .= '</div>';
				}
				$prompt = 'Select one:';
			break;
			case 'multiplechoice-one':

				foreach($questionAttempt['QuestionAnswer'] as $answer)	{
					//get correctness for individual answers
					$correctness = $this->getAnswersCorrectness($questionAttempt, $answer);
					$checked = $questionAttempt['responsesummary'] == $answer['answer'] ? 'checked="checked"' : '';

					$roclass = $this->getCorrectnessCSS($checked, $correctness);

					$answerHTML .= '<div class="r0 '.$roclass.'"><input type="radio" name="QuestionAttempt.'.$i.'.responsesummary" disabled="disabled" value="'.$answer['answer'].'" '.$checked.'><label for="q11:3_answer2">'.$answer['answer'].'</label>';
					$answerHTML .= $this->correctnessIcon($checked, $correctness);
					$answerHTML .= '</div>';
				}

				/*$response = array();
				foreach($questionAttempt['QuestionAnswer'] as $answer)	{
					$response[$answer['answer']] = $answer['answer'];
				}
				$answerHTML .= $this->Form->radio('QuestionAttempt.'.$i.'.responsesummary', $response, array('label'=>false, 'disabled'=>'disabled', 'legend'=>false, 'between'=>'&nbsp;&nbsp;', 'separator' => '<br />'));*/
				$prompt = 'Select one:';
			break;
			case 'multiplechoice-multi':
				$prompt = 'Select one or more:';
				//debug($questionAttempt);
				foreach($questionAttempt['QuestionAnswer'] as $answer)	{

					$checked = $this->getMultiAnswersCheckedness($answer['answer'], $questionAttempt['responsesummary'], $questionAttempt['rightanswer']);

					$correctness = $this->getAnswersCorrectness($questionAttempt, $answer);

					//debug($correctness);

					$roclass = $this->getCorrectnessCSS($checked, $correctness);

					$answerHTML .= '<div class="r0 '.$roclass.'"><input type="checkbox" name="QuestionAttempt.'.$i.'.responsesummary" disabled="disabled" value="'.$answer['answer'].'" '.$checked.'><label for="q11:3_answer2">'.$answer['answer'].'</label>';
					$answerHTML .= $this->correctnessIcon($checked, $correctness);
					$answerHTML .= '</div>';
				}
				/*$response = array();
				foreach($questionAttempt['QuestionAnswer'] as $answer)	{
					$response[$answer['answer']] = $answer['answer'];
				}
				//debug($response);
				$selected = array();
				if(@isset($this->request->data['QuestionAttempt'][$i]))	{
					$selected = explode(';', $this->request->data['QuestionAttempt'][$i]['responsesummary']);
				}
				//debug($selected);

				$answerHTML .= $this->Form->input('QuestionAttempt.'.$i.'.responsesummary', array('multiple'=>'checkbox', 'label'=>false, 'disabled'=>'disabled', 'options'=>$response, 'selected' => $selected, 'legend'=>false, 'between'=>'&nbsp;&nbsp;', 'separator' => '<br />', ));*/
			break;
			case 'multiplelines-rich':
				$answerHTML .= $this->Form->textarea('QuestionAttempt.'.$i.'.responsesummary', array('label'=>false, 'legend'=>false, 'disabled'=>'disabled'));
			break;
			case 'multiplelines':
				$answerHTML .= $this->Form->input('QuestionAttempt.'.$i.'.responsesummary', array('label'=>false, 'class'=>'form-control','disabled'=>'disabled'));
			break;
			case 'singleline':
				$answerHTML .= $this->Form->input('QuestionAttempt.'.$i.'.responsesummary', array('type'=>'text', 'label'=>false, 'class'=>'form-control', 'disabled'=>'disabled'));
			break;
			case 'matching-pairs':
				//debug($questionAttempt);
				$mpq_response = json_encode(unserialize($questionAttempt['responsesummary']));
				$mpq_correctanswer = json_encode(unserialize($questionAttempt['rightanswer']));
				$q_id = $questionAttempt['question_id'];
				$mp_answerClass = 'macthing-question-answers';
				$rightanswer = '';
				foreach(unserialize($questionAttempt['rightanswer']) as $pair){
					$rightanswer .= $pair[0].' <-> '.$pair[1].', ';
					}
				$questionAttempt['rightanswer'] = rtrim($rightanswer, ', ');
				//debug($rightanswer);
					$answerHTML .= "<input type='hidden' value='$q_id' class='mp-question-id'>";
					$answerHTML .= "<input type='hidden' value='$mpq_correctanswer' class='mpq-correctanswer'>";
					$answerHTML .= "<input type='hidden' value='$mpq_response' class='mpq-responsesummary'>";
				$answerHTML .= $this->renderMatchpairQuestionAttempt($questionAttempt);
			break;

		endswitch;
		
		if($prompt) echo '<div class="prompt">' . $prompt . '</div>';
		?>
		<div class="answer <?php echo $mp_answerClass ?>">
		<?php
			echo $answerHTML;
		?>
		</div>
          </div>
        </div>
        <div class="outcome">
          <h4 class="accesshide">Feedback</h4>
          <div class="feedback">
            <div class="specificfeedback">Your answer is <i><?php echo $printStatus; ?></i>.</div>
            <div class="generalfeedback">
              <?php echo $questionAttempt['Question']['description']; ?>
            </div>
            <div class="rightanswer">The correct answer is: <?php echo str_replace(';', ', ', $questionAttempt['rightanswer']); ?></div>
          </div>
        </div>
      </div>
    </div>
		<?php
		}

function renderMatchpairQuestionAttempt($questionAttempt, $testattempt = false){
		$html ="";
		$html .= '<div class="matching-question review">';
		$html .= '<div class="row matching-pairs">
								<div class="col-xs-4 col-md-3 padRight0 match-pair-left-box-pdf">';
		foreach($questionAttempt['QuestionPairMatch'] as $key => $pair){
				 $pair_order = $pair['pair_order'];
				$html .= '<p class="leftpair" id="'.$questionAttempt['question_id'].'leftpair_'.$key.'">'.$pair['question_pair_left'].'</p>';
   		}
		
		$html .= '</div>
							<div class="col-xs-4 col-md-3 pull-right">';
			$rightpairs =  $questionAttempt['QuestionPairMatch']; //before shuffling the array copy it.
			usort($rightpairs, function ($a, $b){
					return $a['display_order'] - $b['display_order'];
			});
			foreach($rightpairs as $key => $pair){
				$html .= '<p class="rightpair" id="'.$questionAttempt['question_id'].'rightpair_'.$key.'">'.$pair['question_pair_right'].'</p>';
   		}
		
		$html .= '</div>
							</div>
						</div>';
		return $html;

}

	public function getQAStatus($attempt)	{
		$qtype = $this->getQType ( $attempt );
		switch($qtype):
				case 'truefalse':
				case 'multiplechoice-one';
					return $attempt['responsesummary'] == $attempt['rightanswer'] ? 'correct' : 'incorrect';
				break;
				case 'multiplechoice-multi';
					$ralist = explode(';', $attempt['rightanswer']);
					$rslist = explode(';', $attempt['responsesummary']);

          if(sizeof($ralist) != sizeof($rslist))  {
            $correct = false;$incorrect = true;
          } else  {
            $ranswers = [];
            $correct = $incorrect = false;
            foreach($rslist as $response)	{
              if(in_array($response, $ralist))	{
                array_push($ranswers, trim($response));
                $correct = true;
              }	else	{
                $incorrect = true;
              }
            }
            if(!$this->_quizSettings['usegrading'])	{
              if(sizeof($ranswers)!=sizeof($ralist)) {
                $correct = false;$incorrect = true;
              }
            }	else	{
              if($correct && $incorrect) {
                if(!$this->_quizSettings['usegrading'])	return 'incorrect';
                return 'partiallycorrect';
              }
            }
          }
					if($correct) return 'correct';
					if($incorrect) return 'incorrect';
				break;
			case 'multiplelines-rich':
			case 'multiplelines':
			case 'singleline':
				if($attempt['rightanswer']=='marked')	{
					return float($attempt[MANMARKSFLD] * 100, 0) . '% correct';
				}
				return 'tbd'; //to be decided manually
			break;
			case 'matching-pairs';
					return $attempt['responsesummary'] == $attempt['rightanswer'] ? 'correct' : 'incorrect';
				break;
		endswitch;
	}

	function getCorrectnessCSS($flag, $correctness) {
		//if(!$flag) return;
		$class = '';$spacer=' ';
		return $spacer . $correctness;
	}

	function getAnswersCorrectnessIcon($correctness)	{
		if($correctness=='correct')	{
			return $this->Html->image(Router::url('/training/img/grade_correct.svg', true), array('alt'=>"Correct", 'class'=>"questioncorrectnessicon"));
		} else	if($correctness=='incorrect')	{
			return $this->Html->image(Router::url('/training/img/grade_incorrect.svg', true), array('alt'=>"Incorrect", 'class'=>"questioncorrectnessicon"));
		}
	}

	function correctnessIcon($flag, $correctness)	{
		if($flag==false) return;
		return $this->getAnswersCorrectnessIcon($correctness);
	}

	function getAnswersCorrectness($attempt, $answer)	{
		$qtype = $this->getQType ( $attempt );
		switch($qtype):
				case 'truefalse':
				case 'multiplechoice-one';
					return $attempt['rightanswer'] == $answer['answer'] ? 'correct' : 'incorrect';
				break;
				case 'multiplechoice-multi';
					$ralist = explode(';', $attempt['rightanswer']);
          //debug($ralist);
          //debug($answer['answer']);
					if(in_array($answer['answer'], $ralist))	{
						return 'correct';
					}	else return 'incorrect';
				break;
		endswitch;
	}

	function getMultiAnswersCheckedness($answer, $responsesummary)	{

		$rslist = explode(';', $responsesummary);
		//$ralist = explode(';', $rightanswer);
		
		if(in_array($answer, $rslist))	{
				//this answer is present in the response by the user, 
				//so lets check whether it is also present in the correct answers
				return 'checked="checked"';
				/*if(in_array($answer, $ralist))	{
					//now this is also present in the right answer list so this is a correct answer
					$class = 'correct';
				}	else	{
					$class = 'incorrect';
				}*/
		}
	}

	function getMultiAnswersCorrectness($answer, $rightanswer)	{
		$ralist = explode(';', $rightanswer);		
		if(in_array($answer, $ralist))	{
				//present in the correct answers
				return 'correct';
		}	else	return 'incorrect';
	}
	function formatQAStatus($status)	{
		switch($status):
			case 'correct':
				return __('Correct');
			break;
			case 'incorrect':
				return __('Incorrect');
			break;
			case 'partiallycorrect':
				return __('Partially Correct');
			break;
			case 'tbd':
				return __('To be decided');
			break;
		endswitch;
		return __($status);
	}


	/**
	 * passorfail - get pass or fail result
	 * @param $earnedgrade - grade earned by user
	 * @param $threshold - threshold for passing a quiz
	 */
	function passorfail($earnedgrade, $threshold, $displaytype = 'texticon', $hint = false)	{

		$result = passorfail($earnedgrade, $threshold);
		$html = '';

		if($displaytype=='basic') $html .= $result; //string as it is; "pass" or "fail"
		if($displaytype=='text') $html .= ucfirst(__($result)); //"Pass" or "Fail"
		if($displaytype=='short') $html .= substr(__($result), 0, 1); //"P" or "F"

		switch($result):
			case 'pass':
				$icon = 'grade_correct.svg'; //set correct icon
			break;
			case 'fail':
				$icon = 'grade_incorrect.svg'; //set incorrect icon
			break;
		endswitch;

		if($displaytype=='texticon') {
			//If set as Text+Icon lets concat the Icon to Normal case Text strings. I.e. to "Pass" or "Fail"
			$html .= ucfirst(__($result)) . ' ' . $this->Html->image(Router::url('/training/img/' . $icon, true), array('alt'=>__($result), 'class'=>"questioncorrectnessicon"));
		}

		if( $hint )	{ //If hint is set it to say, for example, Min [2] [questions] required to pass
			$html .= ' (<em>' . sprintf(__('Min %s %s required to pass'), number_format($threshold,0), $this->_quizSettings['passfailcriteria']) . '</em>)';
		}

		return $html;
	}

	/**
	 *Get pass/fail result for a quizAttempt
	 * @param $quizAttempt array Having 'QuizAttempt' and 'Quiz' indexes set with corresponding data
	 */
	function getresult( $quizAttempt, $displaytype = 'texticon', $hint = false)	{
		//debug($quizAttempt);
		//pr($this->_quizSettings);
		if(!isset($quizAttempt['QuizAttempt']) || $quizAttempt['QuizAttempt']['state']=='inprogress')  return false;
		
		if($quizAttempt['QuizAttempt']['result']== NULL){
			if($this->_quizSettings['passfailcriteria']=='grade')	{
				$earnedgrade = grade($quizAttempt['QuizAttempt']['sumgrades'], $quizAttempt['Quiz']['sumgrades'], $quizAttempt['Quiz']['grade']);
				$threshold = $quizAttempt['Quiz']['minpassgrade'];
			}	else	if($this->_quizSettings['passfailcriteria']=='question')	{
				$earnedgrade = $quizAttempt['QuizAttempt']['correctquestions'];
				$threshold = $quizAttempt['Quiz']['minpassquestions'];
			}

			return $this->passorfail($earnedgrade, $threshold, $displaytype, $hint);
		}else{
		$threshold = $this->_quizSettings['passfailcriteria']=='question' ? $quizAttempt['Quiz']['minpassquestions'] : $quizAttempt['Quiz']['minpassgrade'];
		
		$html = '';
		$result = $quizAttempt['QuizAttempt']['result'];
		if($displaytype=='basic') $html .= $result; //string as it is; "pass" or "fail"
		if($displaytype=='text') $html .= ucfirst(__($result)); //"Pass" or "Fail"
		if($displaytype=='short') $html .= substr(__($result), 0, 1); //"P" or "F"

		switch($result):
			case 'pass':
				$icon = 'grade_correct.svg'; //set correct icon
			break;
			case 'fail':
				$icon = 'grade_incorrect.svg'; //set incorrect icon
			break;
		endswitch;

		if($displaytype=='texticon') {
			//If set as Text+Icon lets concat the Icon to Normal case Text strings. I.e. to "Pass" or "Fail"
			$html .= ucfirst(__($result)) . ' ' . $this->Html->image(Router::url('/training/img/' . $icon, true), array('alt'=>__($result), 'class'=>"questioncorrectnessicon"));
		}

		if( $hint )	{ //If hint is set it to say, for example, Min [2] [questions] required to pass
			$html .= ' (<em>' . sprintf(__('Min %s %s required to pass'), number_format($threshold,0), $this->_quizSettings['passfailcriteria']) . '</em>)';
		}

		return $html;
		}
	}

	function float($num)	{
		return number_format($num, 2);
	}

	function editMarkAllowed($qtype)	{
		switch($qtype):
				case 'truefalse':
				case 'multiplechoice-one';
				case 'multiplechoice-multi';
					return false;
				break;
				default:
					return true;
				break;
		endswitch;
	}

	/**
	 * Create pass or fail (P/F) result links
	 * @param $quizAttempt array Passed by reference so we can use it in loop
	 */
	function createresultlinks($quizAttempt)	{
		//if( !$quizAttempt ) return;
		global $course_id;
		//debug($quizAttempt);
		//$attempt = array_shift( $quizAttempt );
		echo $this->createresultlink($quizAttempt, $course_id);
	}

	/**
	 * Create pass or fail (P/F) result links
	 * @param $quizAttempt array Passed by reference so we can use it in loop
	 */
	function createresultlink($attempt, $course_id)	{
		$query = array('course_id'=>$course_id);
		if($this->action == 'admin_view' || $this->action == 'admin_enrol')  $query['redirect'] = $this->action;

		$admin = @$this->request->params['admin'];
		if($attempt['QuizAttempt']['state']=='inprogress')  return false;
		return $this->Html->link(strtoupper($this->getresult($attempt, 'short')), array('plugin'=>'training', 'controller'=>'quizzes', 'action'=>'review', 'admin'=>$admin, $attempt['QuizAttempt']['id'], '?'=>$query), ['escape'=>false]);
	}

	/**
	 * Get clickable list of uploads to a training course
	 * @param $course array
	 */
	function getUploads($course)	{
		if($course['Course']['source_type']=='Document')	{
			if(isset($course['Upload']) && sizeof($course['Upload'])>0)	{
				$html = '';
				foreach($course['Upload'] as $attachment):
					$html .= '<div class="row">';
						$html .= '<div class="col-xs-6">' .  $this->Utility->linklocalfile(h($attachment['filename']),	$this->Html->url('/files/' . $attachment['name']), array('target'=>'_blank'))  . '</div>';
						$html .= '<div class="col-xs-3">' . $this->Utility->linklocalfile(h('Download'),	$this->Html->url('/files/' . $attachment['name']), array('target'=>'_blank'))  . '</div>';
					$html .= '</div>';
				endforeach;
				return $html;
			}
		}
	}

	/**
	 * Get embeded course code for a training course
	 * @param $course array
	 */
	function getEmbeded($course)	{
		if($course['Course']['source_type']=='Video' || $course['Course']['source_type']=='Powerpoint')	{
			if(trim($course['Course']['source_file_embeded'])) return $course['Course']['source_file_embeded']; else return __('No Source found!');
		}
	}



/**
	 *Get pass/fail result for a quizAttempt
	 * @param $quizAttempt array Having 'TestQuizAttempt' and 'Quiz' indexes set with corresponding data
	 */
	function getTestResult( $quizAttempt, $displaytype = 'texticon', $hint = false)	{
		//debug($quizAttempt);
		if($this->_quizSettings['passfailcriteria']=='grade')	{
			$earnedgrade = grade($quizAttempt['TestQuizAttempt']['sumgrades'], $quizAttempt['Quiz']['sumgrades'], $quizAttempt['Quiz']['grade']);
			$threshold = $quizAttempt['Quiz']['minpassgrade'];
		}	else	if($this->_quizSettings['passfailcriteria']=='question')	{
			$earnedgrade = $quizAttempt['TestQuizAttempt']['correctquestions'];
			$threshold = $quizAttempt['Quiz']['minpassquestions'];
		}

		return $this->passorfail($earnedgrade, $threshold, $displaytype, $hint);
	}



}