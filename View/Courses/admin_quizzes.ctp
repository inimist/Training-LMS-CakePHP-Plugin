<div class="quizzes index">
	<h2><?php echo __('Quizzes'); ?></h2>
	<table class="table table-striped" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
      <th><?php echo $this->Paginator->sort('description'); ?></th>
      <th><?php echo $this->Paginator->sort('timeopen'); ?></th>
      <th><?php echo $this->Paginator->sort('timeclose'); ?></th>
      <th><?php echo $this->Paginator->sort('timelimit'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($quizzes as $quiz): ?>
		<td><?php echo h($quiz['Quiz']['id']); ?>&nbsp;</td>
		<td><?php echo h($quiz['Quiz']['name']); ?>&nbsp;</td>
    <td><?php echo substr( $quiz['Quiz']['description'], 0, 100); ?>&nbsp;</td>
		<td><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quiz['Quiz']['timeopen']); ?>&nbsp;</td>
		<td><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quiz['Quiz']['timeclose']); ?>&nbsp;</td>
		<td><?php echo h($quiz['Quiz']['timelimit']); ?>&nbsp;</td>

		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'reports', $quiz['Quiz']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $quiz['Quiz']['id'])); ?>
			<?php echo $this->Utility->deleteButton($quiz, 'Quiz'); ?>
		</td>
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


