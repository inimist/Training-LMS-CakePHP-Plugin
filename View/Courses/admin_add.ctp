<div class="courses add form">
<?php echo $this->Form->create('Course', array(
	'type' => 'file',
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well course-form',
	'type'=>'file'
)); ?>
<?php echo $this->Element('Course/course_fieldset'); ?>
<?php echo $this->Form->end(__('Add Course')); ?>
</div>
