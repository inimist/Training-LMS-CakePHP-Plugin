	<?php
  //prepare some data here

  $action = $this->request->params['action'];

	//debug($this->request->data);
  ?>
  

		<legend><?php echo sprintf(__('Question Choices'), ucfirst($action)); ?></legend>

  <?php
    echo $this->Form->input('id');
	?>

	<?php
		echo $this->Form->input('title', array('label'=>__('Question (Title)'), 'readonly', 'value'=>$question['Question']['title']));
	?>

	<div class="form-group">
		<label for="QuestionTitle">Question Type</label><br />
		<?php
			//echo $this->Form->hidden('question_type_id', array('label'=>__('Question Type')));
			echo $question['QuestionType']['title'];
		?>
	</div>

	<label for="QuestionTitle">Answers</label>

	<?php
		for($i=0;$i<$num_answers;$i++)	{ 
			echo '<div class="well">';

			if(isset($this->request->data['QuestionAnswer'][$i]))	{
				if(isset($this->request->data['QuestionAnswer'][$i]['id']))	{
					echo $this->Form->input('QuestionAnswer.'.$i.'.id');
				}
			}

			echo $this->Form->input('QuestionAnswer.'.$i.'.answer', array('type'=>'text','label'=>sprintf(__('Choice %d'), $i+1), 'div'=>'form-group'));
			echo $this->Form->input('QuestionAnswer.'.$i.'.fraction', array('options'=>$fractions, 'label'=>__('Grade'), 'div'=>'form-group'));
			echo '</div>';
		}
		?>

<script>
jQuery(function($)	{

})
</script>

