<div class="questions add form">
<?php echo $this->Form->create('Question', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well question-form'
)); ?>
<fieldset>
<?php echo $this->Element('Question/answers_fieldset'); ?>
<?php echo $this->Form->submit(__('Add 3 More Choices'), array('name'=>'data[Question][submit]', 'div'=>'pull-left')); ?>
<?php echo $this->Form->submit(__('Save Choices'), array('name'=>'data[Question][submit]', 'div'=>'pull-left marginLeft10')); ?>
<?php echo $this->Form->hidden('num_answers', array('value'=>$num_answers)); ?>
</fieldset>
<?php echo $this->Form->end(); ?>
</div>
<!-- <?php echo $this->Element('menu'); ?> -->