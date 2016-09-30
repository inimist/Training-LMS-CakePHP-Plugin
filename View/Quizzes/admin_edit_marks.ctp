<?php
echo $this->Form->create('QuestionAttempt', array(
	/*'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well course-form'*/
)); ?>
<?php echo $this->Form->hidden('QuestionAttempt.id'); ?>
<?php echo $this->Form->hidden('QuestionAttempt.rightanswer', array('value'=>'marked')); ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="editMarksLabel">Mark "<?php echo $questionAttempt['QuestionAttempt']['questiontext']; ?>"</h4>
</div>
<div class="modal-body">
<?php echo $this->Form->input('QuestionAttempt.' . MANMARKSFLD, array('label'=>__('Mark this answer out of 100%'))); ?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<?php echo $this->Form->submit(__('Save changes'), array('type'=>"submit", 'div'=>false, 'class'=>"btn btn-primary")); ?>
</div>
<?php echo $this->Form->end(); ?>