		<legend><?php echo __('Quiz Settings'); ?></legend>

  <?php
    echo $this->Form->input('id');
		echo $this->Form->input('name', array('label'=>__('Quiz (Title)')));

		if($_quizSettings['usepassfail'])	{
			echo $this->Form->input('showpassfail', array('label'=>__('Show result as Pass/Fail'), 'options'=>array('No', 'Yes')));

			if(!$_quizSettings['usegrading'])	{
				echo $this->Form->hidden('showpassfail', ['value'=>'1']);
			}
		}

		echo '<div id="cnr-options" style="display:none;">'; //changing to NON-Repeating options

		if($_quizSettings['usegrading'])	{
			echo $this->Form->input('minpassgrade', array('label'=>__('Min grade required to pass this Test:')));
		}

		echo '</div>';

		echo $this->Form->input('minpassquestions', array('label'=>__('Minimum number of questions required to pass this Test:')));

		if($_quizSettings['usegrading'])	{

			echo '<div id="cnrg-options" style="display:none;">'; //changing to NON-Repeating options
				$this->request->data['Quiz']['grade'] = number_format($this->request->data['Quiz']['grade'], 2);
				echo $this->Form->input('grade', array('label'=>__('Maximum grade:')));
			echo '</div>';

		}
	?>

	<?php
		echo $this->Form->input('description', array('label'=>__('Description')));
	?>

	<?php if($_quizSettings['useopenclose']): ?>

	<?php
		echo $this->Form->input('timeopen', array('type'=>'text', 'label'=>__('Date/Time Starts')));
	?>

<?php
		echo $this->Form->input('timeclose', array('type'=>'text', 'label'=>__('Date/Time Close')));
?>

<?php endif; ?>
<?php $this->TinyMCE->editor(array(
        'theme' => 'modern', 
        'skin'=>'lightgray', 
        'mode' => "exact", 
        'elements' => 'QuizDescription',
        'plugins' => 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste'
        )); ?>

<script>
jQuery(function($)	{

	//console.log($('#ControlsAssigneeSetPostSpecificDate').is(':checked'));
	if($('#QuizShowpassfail').val() == 1)	{
		$('#cnr-options').show();
	}	else	{
		$('#cnrg-options').show();
	}

	$('#QuizShowpassfail').on('change', function()	{
		//console.log($(this).val());
		if($(this).val() == 1)	{
			$('#cnr-options').show();
		}	else	{
			$('#cnr-options').hide();
			$('#cnrg-options').show();
		}
	});

	<?php if(!$_quizSettings['usegrading'])	{ ?>
		$('#QuizShowpassfail').val(1);
		$('#QuizShowpassfail').attr({'disabled':'disabled'});
	<?php } ?>


	$('#QuizTimeopen').datetimepicker({pickTime:true, format: 'YYYY-MM-DD hh:mm'});
	$('#QuizTimeclose').datetimepicker({pickTime:true, format: 'YYYY-MM-DD hh:mm'});
})
</script>
