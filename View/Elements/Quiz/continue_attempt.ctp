<?php echo $this->Form->create('QuizAttempt', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'url'=>array('controller'=>'quizzes', 'action'=>'attempt', $quiz['Quiz']['id'], '?'=>array('course_id'=>$course['Course']['id'])),
	'class' => 'well course-form'
)); ?>
<h3>Summary of your previous attempts</h3>

<table class="table table-striped" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo __('Attempt'); ?></th>
			<th><?php echo __('State'); ?></th>
			<th><?php echo __('Review'); ?></th>
	</tr>
	</thead>
	<tbody>
		<td>Preview</td>		
		<td><?php echo $quizAttempt['QuizAttempt']['state'] == 'inprogress'? 'In Progress' : $quizAttempt['QuizAttempt']['state']; ?>&nbsp;</td>
		<td><?php echo @$quiz['Quiz']['state']; ?></td>
		<td></td>
	</tr>
	</tbody>
</table>

<?php echo $this->Form->hidden('quiz_id'); ?>
<?php echo $this->Form->hidden('user_id'); ?>
<?php echo $this->Form->hidden('courses_enrollment_id', array('value'=>$enrollment['CoursesEnrollment']['id'])); ?>
<?php echo $this->Form->hidden('currentpage', array('value'=>'continue_attempt')); ?>
<?php echo $this->Form->end(__('Continue the last attempt')); ?>