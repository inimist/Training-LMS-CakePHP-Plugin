<div class="courses view">
	<h2><?php echo __('No question found!'); ?></h2>
	<div class="col-xs-16 col-md-12">
		<p>This quiz "<strong><?php echo $quiz['Quiz']['name'] ?></strong>" does not seem to have any questions yet!</p><p>If you are a moderator try adding some questions here <?php echo $this->Html->link(__('Add Question'), array('action' => 'add_question', $quiz['Quiz']['id'], 'admin'=>true)); ?>. </p>
	</div>
</div>