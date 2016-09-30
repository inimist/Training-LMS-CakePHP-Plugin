<div class="quizzes add form">
<?php echo $this->Form->create('Quiz', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well quiz-form'
));

echo $useopenclose;
?>
<fieldset>
<?php echo $this->Element('Quiz/quiz_fieldset'); ?>
<?php echo $this->Form->submit(__(' Next >> ')); ?>
</fieldset>
<?php echo $this->Form->end(); ?>
</div>