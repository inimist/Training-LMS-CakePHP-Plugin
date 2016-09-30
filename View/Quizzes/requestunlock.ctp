<?php
//debug($course);
echo $this->Form->create('QuizAttempt', array(
	/*'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well course-form'*/
)); ?>
<?php echo $this->Form->hidden('QuizAttempt.id'); ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="requestUnlockAttemptLabel">Request Unlock?</h4>
</div>
<div class="modal-body">
<?php echo $this->Form->hidden('QuizAttempt.request_unlock', array('value'=>1)); ?>
<p>Request unlocking your previous attempt? If, unlocked you may be able to update your answers of re-attempt the quiz.</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<?php echo $this->Form->submit(__('Request Now'), array('type'=>"submit", 'div'=>false, 'class'=>"btn btn-primary")); ?>
</div>
<?php echo $this->Form->end(); ?>