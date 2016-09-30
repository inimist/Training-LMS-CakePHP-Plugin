<?php 
echo $this->Form->create('QuizAttempt', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well course-form text-center',
	'url'=> array('controller'=>'quizzes', 'action'=>'attempt', $quizAttempt['Quiz']['id'])
)); 
//debug($course);
?>
<?php echo $this->Form->hidden('id'); ?>
<?php echo $this->Form->hidden('quiz_id'); ?>
<?php echo $this->Form->hidden('user_id'); ?>
<?php echo $this->Form->hidden('courses_enrollment_id', array('value'=>$enrollment['CoursesEnrollment']['id'])); ?>

<p>You are about to start a test "<strong><?php echo $quiz['Quiz']['name'] ?></strong>" for training course <?php echo $this->Html->link($course['Course']['name'], array('controller'=>'courses','action' => 'view', $course['Course']['id'])); ?>. </p>

<!-- <p>Click the "<?php echo __('Start Test') ?>" button to start this test.</p> -->

<?php echo $this->Form->hidden('currentpage', array('value'=>0)); ?>

<?php
//debug($enrollment);
$inleaddays = floor(((strtotime($enrollment['CoursesEnrollment']['enddate']) - time()) / 86400) - $enrollment['CoursesEnrollment']['leaddays']);
//if(strtotime($enrollment['CoursesEnrollment']['enddate']) - time()$enrollment['CoursesEnrollment']['leaddays']);
$options = array(
	'label' => __('Attempt test now')
);
if($inleaddays>0)	{ ?>
	<!-- <p class="warning">There are still <strong><?php echo $inleaddays; ?></strong> days left to take this quiz.</p> -->  
<?php //	$options['disabled']='disabled';  //User can attempt test anytime even after lead-time.
	//debug($options);
}

?>

<?php echo $this->Form->end( $options ); ?>