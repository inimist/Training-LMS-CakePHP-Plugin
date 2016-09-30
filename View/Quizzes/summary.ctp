<div class="quizzes add form">
	<h2>Course: <?php echo $course['Course']['name']; ?></h2>

	<h3>Summary of Attempt for Test: "<?php echo $quizAttempt['Quiz']['name']; ?>"</h3>

	<p>&nbsp;</p>
<?php
//debug($quizAttempt);
if($questionAttempt)	{
?>
<table class="table table-striped" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th>#</th>
			<th><?php echo __('Question'); ?></th>
      <th><?php echo __('Status'); ?></th>
			<th><?php echo __('Review'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php $i=0; foreach ($questionAttempt as $question):
		//debug($question);
		?>
		<td><?php echo $this->Html->link(++$i, array('controller'=>'quizzes', 'action'=>'attempt', $quizAttempt['Quiz']['id'], '?'=>array('course_id'=>$quizAttempt['Quiz']['course_id'], 'page'=>$question['QuestionAttempt']['slot'])), array('class'=>'qalink', 'onclick'=>'javascript:return false;')); ?></td> 
		<td><?php echo $question['Question']['title']; ?></td>
		<td><?php echo 'Answer saved'; ?>&nbsp;</td>
		<td><?php echo $this->Html->link(__('Re-Attempt'), array('controller'=>'quizzes', 'action'=>'attempt', $quizAttempt['Quiz']['id'], '?'=>array('course_id'=>$quizAttempt['Quiz']['course_id'], 'page'=>$question['QuestionAttempt']['slot'])), array('class'=>'qalink', 'onclick'=>'javascript:return false;')); ?></td>    
	</tr>
<?php endforeach;
		
		//echo $checksum;
		
		?>
	</tbody>
	</table>

<div class="text-center">

<?php 

$action  = $this->Html->url(array('controller'=>'quizzes', 'action'=>'attempt', $quizAttempt['Quiz']['id'], '?'=>array('course_id'=>$course['Course']['id'])), true);

echo $this->Form->create('QuizAttempt', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well course-form',
	'url' => $action

)); ?>
<?php echo $this->Form->hidden('quiz_id'); ?>
<?php echo $this->Form->hidden('user_id'); ?>
<?php echo $this->Form->hidden('courses_enrollment_id', array('value'=>$quizAttempt['QuizAttempt']['courses_enrollment_id'])); ?>
<?php echo $this->Form->hidden('currentpage', array('value'=>'continue_attempt')); ?>
<?php echo $this->Form->hidden('page'); ?>
<?php echo $this->Form->end(__('Return to attempt')); ?>

<?php 
	
$action  = $this->Html->url(array('controller'=>'quizzes', 'action'=>'attempt', $quizAttempt['Quiz']['id'], '?'=>array('course_id'=>$course['Course']['id'])), true);

echo $this->Form->create('QuizAttempt', array(
	'id'=>'QuizAttemptSubmitForm',
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control',
	),
	'url' => $action,
	'class' => 'well course-form'
)); ?>
<?php echo $this->Form->hidden('id'); ?>
<?php echo $this->Form->hidden('mark_as_finised'); ?>
<?php echo $this->Form->hidden('courses_enrollment_id', array('value'=>$quizAttempt['QuizAttempt']['courses_enrollment_id'])); ?>
<?php if($course['Course']['signature']){
				echo '<div id="signature-box" style="display:none; border: 1px solid #cccccc; width:250px; margin:0 auto;">';
				$userID = $this->Session->read('Auth.User.id');
				//debug($userID);
				$this->Utility->get_signature($userID, '200');
				echo '</div>';
				$sign_exists = $this->Utility->isSignExists($userID);
				echo $this->Form->hidden('signed_by_user', array('value'=>$sign_exists, 'disabled'=>'disabled'));
				echo $this->Form->input('signature_check', array('label'=>'I\'ve read training material and attempted Quiz', 'type'=>'checkbox', 'required'=>true));
		} 
		else { 
			echo $this->Form->hidden('signed_by_user', array('value'=>false));
		} ?>
<?php echo $this->Form->end(__('Submit all and finish')); ?>

</div>

<?php
}
?>

</div>

<script>
jQuery(function($)	{
		$('.qalink').on('click', function()	{
				$('#QuizAttemptSummaryForm').attr('action', $(this).attr('href'));
				$('#QuizAttemptSummaryForm').submit();
		})
		$('#removeCurrentSign').hide();

	$('#QuizAttemptSignatureCheck').on('click ifClicked', function(){
		$('#signature-box').slideToggle();
		if($('#QuizAttemptSignatureCheck').is(':checked')){
				$('#QuizAttemptSignedByUser').attr('disabled', 'disabled');
				
			}else{
				$('#QuizAttemptSignedByUser').removeAttr('disabled');
		 }
  	});
})
</script>