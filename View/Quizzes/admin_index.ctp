<div class="quizzes index">
	<h2><?php echo $course['Course']['name']; ?></h2>

<?php if($quizzes){ ?>

	<h3><?php echo __('Quizzes'); ?></h3>
    
 <table class="table table-striped table-fixed-header" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
				<!--  <th><?php echo $this->Paginator->sort('id'); ?></th>   -->
			<th class="actions"><?php echo __('Actions'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
      <th><?php echo $this->Paginator->sort('description'); ?></th>
			 <th><?php echo $this->Paginator->sort('question_count'); ?></th>
			 <th><?php echo $this->Paginator->sort('minpassquestions', __('Questions need to pass')); ?></th>
			<?php if($useopenclose): ?>
				<th><?php echo $this->Paginator->sort('timeopen'); ?></th>
				<th><?php echo $this->Paginator->sort('timeclose'); ?></th>
			<?php endif; ?>
			<?php if($usetimelimitation): ?>
				<th><?php echo $this->Paginator->sort('timelimit'); ?></th>
			<?php endif; ?>
			<?php if($usedefault): ?>
				<th><?php echo $this->Paginator->sort('is_default'); ?></th>
			<?php endif; ?>
			<th><?php echo h(__('Review Attempts')); ?></th>
			
	</tr>
	</thead>
	<tbody>
	<?php foreach ($quizzes as $quiz): 
	
	//pr($quiz);  ?>


	<tr class="<?php echo $this->Utility->statuscss('Quiz', $quiz); ?> actions">
	<!--	<td><?php echo h($quiz['Quiz']['id']); ?></td>   --->
		
			<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $quiz['Quiz']['id'], 'admin'=>true, '?'=>array('course_id'=>$quiz['Quiz']['course_id'])),array('class' => 'p_right glyphicon glyphicon-th-list','title'=>'View')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $quiz['Quiz']['id']), array('class' => 'p_right glyphicon glyphicon-edit','title'=>'Edit')); ?>
			<?php echo $this->Utility->deleteButton($quiz, 'Quiz'); ?>
			</td>
			<td><?php echo h($quiz['Quiz']['name']); ?></td>
			<td><?php echo substr( $quiz['Quiz']['description'], 0, 100); ?></td>
			<td><?php  $count_title = $quiz['Quiz']['question_count']? $quiz['Quiz']['question_count'] :0; ?>
			<?php echo $this->Html->link($count_title, '#', array("data-toggle"=>"modal", "data-target"=>"#viewInfo", "data-id"=>$quiz['Quiz']['id'], "data-title"=>$quiz['Quiz']['name'], 'onclick'=>'return false'));
        ?><div style="display:none;" id="desc-title-<?php echo $quiz['Quiz']['id']; ?>"><?php echo "Active Questions"; ?>
									<table class="table" cellpadding="0" cellspacing="0">
												<tbody>
													<?php $i=0; foreach ($quiz['Questions'] as $question): ?>
														<tr>
															<td>
																<?php echo ++$i; ?>.
															</td>
															<td>
																<?php echo $question['Question']['title']; ?>
															<td>
														</tr>
													<?php endforeach; ?>
													<tr>
													<td colspan="2"><?php echo ($quiz['Quiz']['attempts'])? '<p class="warning">You cannot add or remove questions because this quiz has been attempted.</p>' : $this->Html->link(__('Add Question'), array('controller'=>'quizzes', 'action'=>'add_question', $quiz['Quiz']['id'], '?'=>array('course_id' => $quiz['Quiz']['course_id'])), array('class' => 'p_right glyphicon glyphicon-plus', 'title'=>'Add Question')) ?> </td>
													</tr>
												</tbody>
											</table>
							</div></td>
			<td><?php echo $quiz['Quiz']['minpassquestions'] ? $quiz['Quiz']['minpassquestions'] : 0; 
				echo ' '.$this->Html->link(__('Quiz Settings'), array('action' => 'settings', $quiz['Quiz']['id']), array('class' => 'p_right glyphicon glyphicon-pencil', 'title'=>'Edit Settings'));?>
		<?php if($useopenclose): ?>
			<td><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quiz['Quiz']['timeopen']); ?></td>
			<td><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quiz['Quiz']['timeclose']); ?></td>
		<?php endif; ?>
		<?php if($usetimelimitation): ?>
			<td><?php echo h($quiz['Quiz']['timelimit']); ?></td>
		<?php endif; ?>
		<?php if($usedefault): ?>
			<td><?php echo $quiz['Quiz']['is_default'] ? 'Yes' : ''; ?></td>
		<?php endif; ?>
			<td><?php echo $this->Html->link(__('Show Attempts'), array('action'=>'view', $quiz['Quiz']['id'], '?'=>array('course_id'=>$quiz['Quiz']['course_id']))); ?></td>
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
	<?php } 
			else {  ?>
			<div class="courses view">
				<h2><?php echo __('No Quiz found!'); ?></h2>
				<div class="col-xs-16 col-md-12">
					<p>This Course "<strong><?php echo $course['Course']['name'] ?></strong>" does not seem to have any quiz yet!</p><p>If you are a moderator try adding quiz here <?php echo $this->Html->link(__('Add Quiz'), array('action' => 'add', '?'=>array('course_id'=>$course['Course']['id']), 'admin'=>true)); ?>. </p>
				</div>
			</div>

			
	<?php } ?>

</div>
<?php echo $this->Utility->initModelHTML(); ?>
<script type="text/javascript">
		jQuery(function($)	{
	$('#viewInfo').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) // Button that triggered the modal
		var title = button.data('title');
		var id = button.data('id');
		var modal = $(this);
		modal.find('.modal-title').text(title)
    //alert($('#desc-title-'+id).html());
		modal.find('.modal-body').html($('#desc-title-'+id).html());
	})
		
})
</script>