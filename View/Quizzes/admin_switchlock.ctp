<?php
//debug($this->request->data);
echo $this->Form->create('QuizAttempt', array(
	/*'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well course-form'*/
)); ?>
<?php echo $this->Form->hidden('QuizAttempt.id'); 

$action = $this->request->data['QuizAttempt']['locked'] ? 'Unlock' : 'Lock';

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="requestUnlockAttemptLabel">Switch Lock?</h4>
</div>
<div class="modal-body">
<?php echo $this->Form->hidden('QuizAttempt.locked', array('value'=>$this->request->data['QuizAttempt']['locked'] ? 0 : 1)); ?>
<p>Switch Lock of a Quiz Attepmt of <strong><?php echo $this->request->data['User']['full_name']; ?></strong>.</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<?php echo $this->Form->submit(__($action . ' Now'), array('type'=>"submit", 'div'=>false, 'class'=>"btn btn-primary")); ?>
</div>
<?php echo $this->Form->end(); ?>