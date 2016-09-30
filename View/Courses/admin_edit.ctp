<div class="courses edit form">
<?php echo $this->Form->create('Course', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well course-form',
	'type'=>'file'
)); ?>
<?php echo $this->Element('Course/course_fieldset'); ?>
<?php echo $this->Form->input('clear_course_log', array('type'=>'checkbox', 'label'=>'Clear Course Learn Log')); ?>
<?php echo $this->Form->end(__('Update Course')); ?>
</div>
