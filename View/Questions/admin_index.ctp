<div class="questions index">

	<h2><?php echo $course['Course']['name']; ?></h2>
	
	<h2><?php echo __('Questions'); ?></h2>
	<table class="table table-striped table-fixed-header" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<!--	<th><?php //echo $this->Paginator->sort('id'); ?></th> -->
			<th><?php echo __('#'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
			<th><?php echo $this->Paginator->sort('title'); ?></th>
      <th><?php echo $this->Paginator->sort('description'); ?></th>
      <th><?php echo $this->Paginator->sort('question_type_id'); ?></th>
			<th><?php echo __('Correct Answer'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	//pr($questions);
	$qnum = 1;
	foreach ($questions as $question): ?>
	<tr class="<?php echo $this->Utility->statuscss('Question', $question); ?>">
	<!--	<td><?php echo h($question['Question']['id']); ?>&nbsp;</td>	 -->
		<td> <strong><?php echo $qnum.'.';  $qnum++; ?>&nbsp;</strong></td>
		<td class="actions">
			<!-- <?php //echo $this->Html->link(__('View'), array('action' => 'view', $question['Question']['id'])); ?> -->
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $question['Question']['id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?>
			<?php echo $this->Utility->deleteButton($question, 'Question'); ?>
		</td>
		<td><?php echo h($question['Question']['title']); ?>&nbsp;</td>
		<td><?php echo substr($question['Question']['description'], 0, 50); 
							if(strlen($question['Question']['description'])>50) echo '...'; ?>&nbsp; </td>
		<td><?php echo h($question['QuestionType']['title']); ?>&nbsp;</td>
		<td><?php if(sizeof($question['QuestionAnswer'])>0) {
							$correct_answer = '';
								foreach($question['QuestionAnswer'] as $answer ) {
									$correct_answer .= h($answer['QuestionAnswer']['answer']).', ';
									}
								echo rtrim($correct_answer, ', ');
								}?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="pagination">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>

<!-- <?php echo $this->Element('menu'); ?> -->