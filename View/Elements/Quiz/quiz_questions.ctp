<?php
$this->Training->setQuizSettings($_quizSettings);
if($this->request->data['QuizSlot'])	{
		$checksum = 0;
?>

<table class="table table-striped" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo __('name'); ?></th>
      <th><?php echo __('description'); ?></th>
			<th><?php echo __('Question Type'); ?></th>
			<th><?php echo __('Slot'); ?></th>
			<th style="<?php if($_quizSettings['passfailcriteria']!='grade') echo 'display:none;' ?>"><?php echo __('Maxmarks'); ?></th>
			<th><?php echo __('Delete'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php 
	
		//debug($this->request->data['QuizSlot']);

		$i=0; foreach ($this->request->data['QuizSlot'] as $question): 
		$checksum += $question['question_id'] / $question['slot'];
		$this->request->data['QuizSlot'][$i]['maxmarks'] = number_format($this->request->data['QuizSlot'][$i]['maxmarks'], 2);
		?>
	<tr>
		<td><?php echo $this->Form->hidden('Quiz.questions.', array('value' => $question['Question']['id'])); ?><?php echo h($question['Question']['title']); ?></td>		
    <td><?php echo substr( $question['Question']['description'], 0, 100); ?>&nbsp;</td>
		<td><?php echo $questionTypes[$question['Question']['question_type_id']]; ?></td>
		<td style="<?php if($_quizSettings['passfailcriteria']!='grade') echo 'display:none;' ?>"><?php echo $this->Form->input('QuizSlot.' . $i . '.slot', array('size'=>1, 'label'=>false)); ?></td>
		<td><?php echo $this->Form->input('QuizSlot.' . $i . '.maxmarks', array('size'=>1, 'label'=>false)); ?></td>
		<td class="actions"><?php echo $this->Form->checkbox('QuizSlot.' . $i . '.delete'); ?><?php echo $this->Form->input('QuizSlot.' . $i . '.id'); ?><?php echo $this->Form->hidden('QuizSlot.' . $i . '.question_id'); ?></td>
	</tr>
<?php 
			++$i;
		
		endforeach;
		
		//echo $checksum;
		
		?>
	</tbody>
	</table>


<?php
	echo $this->Form->hidden('Quiz.checksum', array('value' => $checksum)); 
}
?>

<?php if(!$attempts)	{ ?>


<p><?php echo $this->Html->link(__('Add Question'), array('controller'=>'quizzes', 'action'=>'add_question', $this->request->data['Quiz']['id'], '?'=>array('course_id' => $this->request->data['Quiz']['course_id']))) ?> | <?php echo $this->Html->link(__('Edit Questions Sequence'), array('controller'=>'quizzes', 'action'=>'question_sequence', $this->request->data['Quiz']['id'], '?'=>array('course_id' => $this->request->data['Quiz']['course_id']))) ?></p>

<?php } ?>