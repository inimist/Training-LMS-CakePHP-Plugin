<div class="questions add form">
<?php 

if($question_has_attempts) {
  echo '<div class="error">You may not edit this question. It has attempts. Please contact support for more information.</div>';
}
echo $this->Form->create('Question', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well question-form'
));
?>
<fieldset>
	<?php echo $this->Element('Question/question_fieldset'); ?>

	<?php 

	if($is_multiple_choice)	{

		if( $_quizSettings['usegrading'] )	{
			//echo $this->Form->input('penalty', array('label' => __('Penalty for each incorrect try')));
		}

		echo '<div class="well">';
		echo $this->Element('Question/multiple_fieldset'); ?>
		<?php echo $this->Form->submit(__('Add 3 More Choices'), array('name'=>'data[Question][submit]', 'div'=>'pull-left')); ?>
		<?php //echo $this->Form->submit(__('Save Choices'), array('name'=>'data[Question][submit]', 'div'=>'pull-left marginLeft10')); ?>
		<?php echo $this->Form->hidden('num_answers', array('value'=>$num_answers));
		echo '</div>';
		echo '<div class="clearfix"></div>';
	}

	if($is_matching_pairs){
		echo '<div class="well">';
		echo $this->Element('Question/matching_fieldset');
		echo '</div>';
		echo '<div class="clearfix"></div>';
	}

	if($is_truefalse)	{
		//echo '<div class="well">';
		echo $this->Element('Question/truefalse_fieldset');
		//echo '</div>';
		//echo '<div class="clearfix"></div>';
	}

	?>

	<?php echo $this->Form->submit(__('Save Changes'), array('name'=>'data[Question][submit]', 'div'=>'pull-left submit', 'id'=>'editQuestionAnswer')); ?>
	<?php //echo $this->Form->submit(__('Save & Go to Choices'), array('name'=>'data[Question][submit]', 'div'=>'pull-left marginLeft10')); ?>
</fieldset>
<?php echo $this->Form->end(); ?>
</div>

<!-- <script>
jQuery(function($)	{

})
</script> -->


<!-- <?php echo $this->Element('menu'); ?> -->