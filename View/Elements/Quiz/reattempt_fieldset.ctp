<?php
echo $this->Form->create('QuizAttempt', array(
			'inputDefaults' => array(
				'div' => 'form-group',
				'wrapInput' => false,
				'class' => 'form-control'
			),
			'class' => 'well quizattempt-form text-center',
			'url' => array('controller'=>'quizzes', 'action'=>$action, $quizid)
		)); ?>
		<?php echo $this->Form->hidden('id'); ?>
		<?php echo $this->Form->hidden('quiz_id'); ?>
		<?php echo $this->Form->hidden('user_id'); ?>
		<?php echo $this->Form->hidden('courses_enrollment_id'); ?>