<div class="courses view">
<h2>Training Course: <?php  echo h($course['Course']['name']); ?></h2>

<!-- <p>Created by: <?php echo $this->Utility->linkeduname($course['Creator']); ?></p> -->

<p><a href="<?php echo $this->Html->url(array('action'=>'learn', $course['Course']['id'], '?'=>array('enid'=>$course['CoursesEnrollment']['id']))); ?>"><?php echo $this->Html->image('/training/img/icon.svg'); ?> Learn</a></p>

<!-- <h4>Available Tests</h4> -->

<?php 
//debug($course);
$this->Training->setQuizSettings( $_quizSettings );

						//debug($course['Quizzes']);
						if(isset($course['Quizzes']) && sizeof($course['Quizzes'])>0)	{ ?>
						
							<table class="table table-striped" cellpadding="0" cellspacing="0">
								<thead>
								<tr>
										<th><?php echo __('Quiz'); ?></th>
										<th><?php echo __('description'); ?></th>
										<th><?php echo __('Questions'); ?></th>

										<?php if($_quizSetting['useopenclose']): ?>
											<th><?php echo __('Time Open'); ?></th>
											<th><?php echo __('Time Close'); ?></th>
										<?php endif; ?>

										<th><?php echo __('Completed On'); ?></th>

										<th><?php echo __('Result'); ?></th>

										<th><?php echo __('Attempt'); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php $i=0; $hasfailedQuiz = false;
										foreach ($course['Quizzes'] as $quiz):
										if($quiz['QuizAttempt']['archived']) continue;
										if(!$quiz['Quiz']['is_default'] && !($quiz['QuizAttempt']['unlock_requested'] || $quiz['QuizAttempt']['unlocked'] || $quiz['QuizAttempt']['locked'] || $quiz['QuizAttempt']['state'] == 'inprogress') ) continue;

										//User can only view default Quiz or Quiz with unlock request or unlocked.

										//if user has any attempt to a quiz which is not passed yet
										 if(!$quiz['Quiz']['is_default'] && ($quiz['QuizAttempt']['result'] == 'fail' || $quiz['QuizAttempt']['state'] == 'inprogress'))
												$hasfailedQuiz = true; 

										$quizview_url = array('controller'=>'quizzes', 'action'=>'view', $quiz['Quiz']['id'], '?'=>array('course_id'=>$quiz['Quiz']['course_id']));
										if($quiz['QuizAttempt']['courses_enrollment_id'])	{
												$quizview_url['?']['enid'] = $quiz['QuizAttempt']['courses_enrollment_id'];
										}

									?>
									<td class="<?php echo $quiz['Quiz']['is_default']? 'default-quiz' : ''  ?>"> <strong><?php 
										echo __($quiz['Quiz']['name']);
										//echo $this->Html->link($quiz['Quiz']['name'], $quizview_url, $quizview_url_options); ?> </strong></td>		
									<td><?php echo substr( $quiz['Quiz']['description'], 0, 100); ?>&nbsp;</td>
									<td><?php echo @$quiz['Quiz']['question_count']; ?></td>
									<?php if($_quizSetting['useopenclose']): ?>
									<td><?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $quiz['Quiz']['timeopen']); ?></td>
									<td><?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $quiz['Quiz']['timeclose']); ?></td>
									<?php endif; ?>
									<td><?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $quiz['QuizAttempt']['timefinish']); ?></td>

									<td><?php 
										$pass_fail = '';
									if(isset($quiz['QuizAttempt']) && (int)	$quiz['QuizAttempt']['id'])	{
										echo $this->Training->getresult( $quiz, 'text', 1 );
										$pass_fail = $this->Training->getresult($quiz, 'basic');
									}	else	echo '<em>Not attempted</em>'; ?></td>

									<td class="<?php echo $quiz['Quiz']['is_default']? 'default-quiz' : ''  ?>"><?php 
								if($quiz['QuizAttempt']['locked'])	{
									echo '<span class="glyphicon glyphicon-lock warning"> LOCKED</span><br />';

									echo $this->Html->link(__('Review'), $quizview_url); 
									if($this->Training->getresult( $quiz, 'short' )!='p'){
									echo ' | ';

									echo $this->Html->link(__('Request Unlock'), array('controller'=>'quizzes', 'action'=>'requestunlock', $quiz['QuizAttempt']['id'], '?'=>array('course_id'=>$quiz['Quiz']['course_id'])), array("data-toggle"=>"modal", "data-target"=>"#requestUnlockAttempt", 'onclick'=>'return false')); 
										}
									if($quiz['QuizAttempt']['unlock_requested'])	{
										echo ' (<span class="info">Unlock Requested</span>) ';
									}


								}	else if(isset($quiz['Quiz']['no_default_no_attempt']))	{ 
									echo '<span class="info">No default no attempt</span>*';
								}else{
									if($quiz['QuizAttempt']['unlocked'] || $pass_fail =='fail' )	{
										echo ($hasfailedQuiz && $quiz['Quiz']['is_default']) ? 'Can\'t Attempt <i class="glyphicon glyphicon-info-sign" rel="tooltip" title="You can\'t attempt quiz until you have passed the previous quiz " id="blah"></i>' : $this->Html->link(__('Re-attempt'), $quizview_url); 
									}	else	{
										echo ($hasfailedQuiz && $quiz['Quiz']['is_default']) ? 'Can\'t Attempt <i class="glyphicon glyphicon-info-sign" rel="tooltip" title="You can\'t attempt quiz until you have passed the previous quiz " id="blah"></i>' : $this->Html->link(__('Attempt'), $quizview_url);
									}
								}
							?></td>
								</tr>
							<?php endforeach;
							?>
								</tbody>
						</table>

					<?php } ?>
					<h4>Comments</h4>
						<div id="post-comments">
							<?php $this->CommentWidget->options(array('allowAnonymousComment' => false));?>
							<?php echo $this->CommentWidget->display();?>
						</div>
</div>
<div class="modal fade" id="requestUnlockAttempt" tabindex="-1" role="dialog" aria-labelledby="requestUnlockAttemptLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>
<script>
jQuery(function($)	{
	$('#requestUnlockAttempt').on('show.bs.modal', function (event) {
		//var button = $(event.relatedTarget) // Button that triggered the modal
		//var recipient = button.data('target') // Extract info from data-* attributes
		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
		//var modal = $(this)
		//modal.find('.modal-title').text('Edit marks of ' + recipient)
		//modal.find('.modal-body input').val(recipient)
	})
	/*
	var hasPendingQuiz = "<?php echo $hasfailedQuiz? 'true' : 'false'; ?>";

	if(hasPendingQuiz){
		$('.default-quiz a').on('click', function(evt){
			console.log('attempt clicked');
			evt.preventDefault();
			alert('You can\'t attempt quiz until you have passed the previous quiz.');
			
			});
	}
	*/
})
</script>
