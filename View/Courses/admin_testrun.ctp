<?php //pr($course);   exit;?>
<div class="courses view">
	<h2><?php echo __('No Quiz found!'); ?></h2>
	<div class="col-xs-16 col-md-12">
		<p>This Course "<strong><?php echo $course['Course']['name'] ?></strong>" does not seem to have any quiz yet! or no quiz is default</p><p>If you are a moderator try adding quiz or mark any quiz as default here <?php echo $this->Html->link(__('Quizzes'), array('controller'=>'quizzes', 'action' => 'index', '?'=>array('course_id'=>''), 'admin'=>true)); ?>. </p>
	</div>
</div>