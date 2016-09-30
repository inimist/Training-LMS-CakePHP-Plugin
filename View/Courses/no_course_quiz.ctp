<?php //pr($course);   //exit;?>
<div class="courses view">
	<h2><?php echo __('No Quiz found!'); ?></h2>
	<div class="col-xs-16 col-md-12">
		<p>This Course "<strong><?php echo $course['Course']['name'] ?></strong>" does not seem to have any quiz yet!</p><p>If you are a moderator try adding quiz here <?php echo $this->Html->link(__('Add Quiz'), array('controller'=>'quizzes', 'action' => 'add', '?'=>array('course_id'=>$course['Course']['id']), 'admin'=>true)); ?>. </p>
	</div>
</div>