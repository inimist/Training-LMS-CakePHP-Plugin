	<?php
  //prepare some data here

  $action = $this->request->params['action'];

	//debug($this->request->data);
  ?>
  

		<legend><?php echo sprintf(__('%s Question'), ucfirst(substr($action,6))); ?></legend>

  <?php
    echo $this->Form->hidden('id');
		echo $this->Form->input('creator_id', array('type'=>'hidden'));
		echo $this->Form->input('course_id', array('type'=>'hidden'));
	?>

	<?php
		echo $this->Form->input('title', array('label'=>__('Question (Title)')));
	?>

	<?php
		echo $this->Form->input('description', array('label'=>__('Description')));
	?>

  <?php
		//debug($questionTypes);
		echo $this->Form->input('question_type_id', array('label'=>__('Question Type')));
	?>

  <?php
    echo '<div id="cnr-options" style="display:none;">'; //changing to NON-Repeating options
		echo $this->Form->input('frequency', array('label'=>__('Repeat every n months')));
    echo '</div>';

		if(isset($this->request->data['Question']['defaultmark'])) $this->request->data['Question']['defaultmark'] = number_format($this->request->data['Question']['defaultmark'], 1);
		else	$this->request->data['Question']['defaultmark'] = 1;

		if( $_quizSettings['usegrading'] )	{
			echo $this->Form->input('defaultmark', array('label'=>__('Default marks'), 'step'=>'1'));
		}	else	{
			echo $this->Form->hidden('defaultmark');
		}
?>

<?php $this->TinyMCE->editor(array(
        'theme' => 'modern', 
        'skin'=>'lightgray', 
        'mode' => "exact", 
        'elements' => 'QuestionDescription',
        'plugins' => 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste'
        )); ?>
<script>
jQuery(function($)	{

})
</script>

