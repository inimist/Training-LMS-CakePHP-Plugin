	<?php
  //prepare some data here

  $action = $this->request->params['action'];

	//debug($course_id);
  ?>
  

		<legend><?php echo sprintf(__('%s Quiz'), ucfirst(str_replace('admin_', '', $action))); ?></legend>

  <?php
    echo $this->Form->input('id');
		echo $this->Form->input('creator_id', array('type'=>'hidden'));
		echo $this->Form->input('course_id', array('type'=>'hidden'));
		//debug($this->request);
		if($this->request->params['action']=='admin_edit')	{
			$this->request->data['Quiz']['grade'] = number_format($this->request->data['Quiz']['grade'], 2);
			if($_quizSettings['usegrading'])	{
				echo $this->Form->input('grade', array('label'=>__('Maximum grade:'), 'div'=>array('class'=>'pull-left')));
				echo '<div class="clearfix">&nbsp;</div><br />';
			}	else	{
				echo $this->Form->hidden('grade');
			}
		}

		echo $this->Form->input('name', array('label'=>__('Quiz (Title)')));
	?>

	<?php
		echo $this->Form->input('description', array('label'=>__('Description')));

		if( $_quizSettings['useopenclose'] )	{
	?>

	<?php
		echo $this->Form->input('timeopen', array('type'=>'text', 'label'=>__('Date/Time Starts')));
	?>

<?php
		echo $this->Form->input('timeclose', array('type'=>'text', 'label'=>__('Date/Time Close')));
		}
?>

<?php
		if( $_quizSettings['usetimelimitation'] )	{
			echo $this->Form->input('timelimit', array('label'=>__('Time Limit to take this Quiz')));
		}
?>

<?php
		if( $_quizSettings['usedefault'] )	{
			echo $this->Form->input('is_default', array('type'=>'checkbox', 'label'=>__('Mark as Default')));
		}
?>

<?php $this->TinyMCE->editor(array(
        'theme' => 'modern', 
        'skin'=>'lightgray', 
        'mode' => "exact", 
        'elements' => 'QuizDescription',
        'plugins' => 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste'
        )); ?>

<script>
jQuery(function($)	{
	$('#QuizTimeopen').datetimepicker({pickTime:true, format: 'YYYY-MM-DD hh:mm'});
	$('#QuizTimeclose').datetimepicker({pickTime:true, format: 'YYYY-MM-DD hh:mm'});
})
</script>
