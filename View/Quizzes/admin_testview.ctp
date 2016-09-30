<div class="courses view">
<?php //global $_quizSettings;

$this->Training->setQuizSettings($_quizSettings); // pr($_quizSettings); ?>

	<h2><?php echo $quiz['Quiz']['name']; ?></h2>
	<p>Course: <?php echo $this->Html->link($course['Course']['name'], array('controller'=>'courses', 'action'=>'view', 'admin'=>false, $course['Course']['id'])); ?></p>

<div class="col-xs-16 col-md-12">

<?php if($exception): ?>

	<p class="warning"><?php echo $exception; ?>. <a href="#">Contact support</a>.</p>

	<?php /*if($this->Utility->isAdmin()): ?>

	<!-- <p class="info">If you are an administrator got to <?php echo $this->Html->link(__('Enroll users now'), array('controller'=>'courses', 'action'=>'enrol', 'admin'=>true, $course['Course']['id']), array()); ?>.</p> -->

	<?php endif; */ ?>

<?php else: ?>

<p class="text-center">Grading method: <?php echo $quiz['Quiz']['showpassfail'] ? 'Pass/Fail' : 'Highest grade'; ?></p>

<?php
//debug($quiz['Quiz']);
//debug($quizAttemptUsers);
if(isset($quiz['user_count'])):
?>
	<p class="text-center">Users Attempts: <?php echo $quiz['user_count']; ?></p>
<?php
/*
	if($quizAttemptUsers['QuizAttempt']['state']=='inprogress'):

	echo $this->Element('Quiz/continue_attempt');

	elseif($quizAttempt['QuizAttempt']['state']=='finished'):
*/
	
	
?>

		<h3>Summary of your previous attempts for Test: "<?php echo $quiz['Quiz']['name']; ?>"</h3>

		<table class="table table-striped generaltable generalbox quizreviewsummary">
			<tbody>
				<tr>
					<th class="cell" scope="row">User</th>
					<th class="cell" scope="row">Status</th>
					<?php if($_quizSettings['usegrading']): ?>
						<th class="cell" scope="row">Marks / <?php echo float($quiz['Quiz']['sumgrades']); ?></th>
						<th class="cell" scope="row">Grade / <?php echo float($quiz['Quiz']['grade']); ?></th>
					<?php endif; ?>

					<?php if($_quizSettings['usepassfail']): ?>
						<th class="cell" scope="row">Result</th>
					<?php endif; ?>

					<th class="cell" scope="row">Review</th>
					<th class="cell" scope="row">Attempts</th>
					</tr>
			<?php	foreach($quizAttemptUsers as $quizAttempt ){  
				
				//pr($quizAttempt); 
				$earnedgrade = grade($quizAttempt['TestQuizAttempt']['sumgrades'], $quiz['Quiz']['sumgrades'], $quiz['Quiz']['grade']);
				$earnedgrade_formatted = float( $earnedgrade );
					?>
				<tr>
					<td class="cell"><?php echo $this->Html->link($quizAttempt['User']['full_name'], array('controller'=>'user', 'action'=>'view', $quizAttempt['User']['id'])); ?></td>
					<td class="cell"><?php echo ucfirst($quizAttempt['TestQuizAttempt']['state']) ?> <br /><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quizAttempt['TestQuizAttempt']['timefinish']); ?></td>

					<?php if($_quizSettings['usegrading']): ?>
					<td class="cell"><?php echo float($quizAttempt['TestQuizAttempt']['sumgrades']) ?></td>
					<td class="cell"><?php echo $earnedgrade_formatted; ?></td>
					<?php endif; ?>

					<?php if($_quizSettings['usepassfail']): 
						$quizAttempt['Quiz'] = $quiz['Quiz'];
					?>
						<td class="cell"><?php echo $this->Training->getTestResult( $quizAttempt, 'texticon', 1); ?></td>
					<?php endif; ?>


					<td class="cell"><?php echo $this->Html->link(__('Review Answers'), array('controller'=>'quizzes', 'action'=>'review', $quizAttempt['TestQuizAttempt']['id'], '?'=>array('course_id'=>$quizAttempt['Quiz']['course_id'], 'test'=>'true')), array()); ?></td>

					<td class="cell"><?php echo $quizAttempt['TestQuizAttempt']['attempt_count']; ?></td>
				</tr>
				<?php  }  //foreach end ?> 
			</tbody>
		</table>
	
		<?php if($_quizSettings['usegrading']): ?>
			<h3 class="text-center">Highest grade: <?php echo $earnedgrade_formatted; ?> / <?php echo float($quiz['Quiz']['grade']); ?>.</h3>
		<?php endif; ?>

		<?php
		$this->request->data = $quizAttempt;
		//debug($quizAttempt);
		//debug($quiz['Quiz']['id']);
		echo $this->Element('Quiz/test_reattempt_fieldset', array('quizid'=>$quiz['Quiz']['id'], 'action'=>'testattempt'));
		$options  = array('label'=>__('Re-attempt Quiz'));
		//if( !$quizAttempt['TestQuizAttempt']['locked'] )	{
			
			
	
	
		echo $this->Form->end( $options );
		?>

<?php
/*	
endif;

else: 
?>

<?php echo $this->Element('Quiz/attempt_test_now'); ?>

<?php 
*/
endif;
endif;

?>

</div></div>