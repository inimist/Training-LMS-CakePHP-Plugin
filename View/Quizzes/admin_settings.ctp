<div class="quizzes settings form">
<?php echo $this->Form->create('Quiz', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well quiz-form'
)); ?>
<fieldset>
<?php echo $this->Element('Quiz/settings_fieldset'); ?>
<?php echo $this->Form->submit(__(' Save Settings ')); ?>
</fieldset>
<?php echo $this->Form->end(); ?>
</div>