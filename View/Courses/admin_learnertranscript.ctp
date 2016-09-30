<div class="courses view">

	<?php if($this->layout == 'pdf'){
	?>
	<style>
	*{
	font-size:10px;
		}
		</style>
	<?php } ?>

<style>

	table{
		width:100%
	}
	table, th, td {
	border: 1px solid;
	border-spacing: 0px !important;
	}
	.table{
		width:100%
	}
	.table, .table th, .table td {
	border: 1px solid;
	}

.course-title{
  font-weight: 600;
  font-size: 16px;
	}
.course-separate{
	border: 1px dashed #000;
	}
	.attempt-default {
    background-color: #DDFBBF;
	}
.fail{
	background-color:#fcab9c !important;
	}
	p {
	margin:0 0 0 5px;
	padding:0;
	}
.center{
	text-align:center;
}


</style>

<p></p>
	<?php if($this->layout != 'pdf' && $this->Utility->isAdmin()) echo $this->Html->link(__('Export to PDF'), array('plugin'=>'training', 'controller'=>'courses', 'action'=>'learnertranscript', 'admin'=>true, $user['User']['id'], '?'=>array('export'=>'pdf')), array('class'=>'btn btn-default pull-right', 'id'=>'more-occurrences', 'target'=>'_blank'));   ?>
<div class="col-xs-16 col-md-12">
	<p style="text-align:center;">
		<strong>User:</strong> <?php echo $this->Utility->linkeduname($user['User']); ?><br>
		<strong>Full Name:</strong> <?php echo $user['User']['full_name']; ?><br>
		<strong>Department:</strong> <?php echo $user['Department']['name']; ?><br>
	</p>
<?php //if($courses['Course'])
if(sizeof($courses) > 0){
		$ta_count = $pa_count = $fa_count = $na_count = $ce_count = 0;

	//pr($courses);
	$this->Training->setQuizSettings($_quizSettings);
	foreach($courses as $course){
	?>

	<h4 class="course-title">Course Name: <?php echo $course['Course']['name']; ?></h4>
	<p class="course-description"><?php echo $course['Course']['description']; ?> <p>

	<p><strong>Enrollments: </strong></p>
	<?php if(sizeof($course['CoursesEnrollment'])> 0){
			$enrol_count = 1;
			foreach($course['CoursesEnrollment'] as $enrollment){
				$ce_count++;
				echo "<strong> #".$enrol_count ."</strong>";
				?>
				Start Date: <?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $enrollment['startdate']); ?><br>
				Due Date: <?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $enrollment['enddate']); ?><br>
				<?php
					if(sizeof($enrollment['QuizAttempt'])>0){
						$attempt_count = 1;
								/*
						foreach($enrollment['QuizAttempt'] as $quizAttempt){
										$earnedgrade = grade($quizAttempt['QuizAttempt']['sumgrades'], $quizAttempt['Quiz']['sumgrades'], $quizAttempt['Quiz']['grade']);
										$earnedgrade_formatted = float( $earnedgrade );
									?>

											<br><strong>Test: "<?php echo $quizAttempt['Quiz']['name']; ?>" - Preview</strong>

											<table class="table table-striped generaltable generalbox quizreviewsummary">
												<tbody>
													<!-- <tr>
														<th class="cell" scope="row">User</th>
														<td class="cell"><?php echo $this->Utility->linkeduname($quizAttempt['User']); ?></td>
													</tr> -->
													<tr>
														<th class="cell" scope="row">State</th>
														<td class="cell"><?php echo ucfirst($quizAttempt['QuizAttempt']['state']); ?></td>
													</tr>
													<tr>
														<th class="cell" scope="row">Started on</th>
														<td class="cell"><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quizAttempt['QuizAttempt']['timestart']); ?></td>
													</tr>
													<tr>
														<th class="cell" scope="row">Completed on</th>
														<td class="cell"><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quizAttempt['QuizAttempt']['timefinish']); ?></td>
													</tr>
													<tr>
														<th class="cell" scope="row">Time taken</th>
														<td class="cell"><?php echo humantimediff($quizAttempt['QuizAttempt']['timestart'], $quizAttempt['QuizAttempt']['timefinish']); ?></td>
													</tr>

													<?php if($_quizSettings['passfailcriteria']=='question'): ?>
													<tr>
														<th class="cell" scope="row">Marks</th>
														<td class="cell"><?php echo $quizAttempt['QuizAttempt']['correctquestions']; ?>/<?php echo $quizAttempt['Quiz']['question_count']; ?></td>
													</tr>
													<?php else: ?>
													<tr>
														<th class="cell" scope="row">Marks</th>
														<td class="cell"><?php echo float($quizAttempt['QuizAttempt']['sumgrades']); ?>/<?php echo float($quizAttempt['Quiz']['sumgrades']); ?></td>
													</tr>
													<?php endif; ?>

												<?php if($_quizSettings['usegrading']): ?>
													<tr>
														<th class="cell" scope="row">Grade</th>
														<td class="cell"><b><?php echo $earnedgrade_formatted; ?></b> out of <?php echo float($quizAttempt['Quiz']['grade']); ?> (<b><?php echo pcgrade_formatted($quizAttempt['QuizAttempt']['sumgrades'], $quizAttempt['Quiz']['sumgrades'], $quizAttempt['Quiz']['grade']); ?></b>%)</td>
													</tr>
													<?php endif; ?>
													<?php if($quizAttempt['Quiz']['showpassfail'])	{ ?>
													<tr>
														<th class="cell" scope="row">Result</th>
														<td class="cell"><b><?php echo $this->Training->getresult( $quizAttempt, 'texticon', 1 ); ?></b></td>
													</tr>
													<?php } ?>
												</tbody>
											</table>

									<?php
									foreach($quizAttempt['QuestionAttempt'] as $QuestionAttempt)	{
										echo $this->Training->renderQuestionAttempt($QuestionAttempt , $this->layout);
									}
							$attempt_count++;
							} 	*/
				
						echo "<p class='center'> Total Attempts: " .sizeof($enrollment['QuizAttempt']). " </p>"; 
						?>
						<!-- <p><strong>Summary of your attempts for Test: "<?php echo $quizAttempt['Quiz']['name']; ?>"</strong><p> -->
						<table class="table table-striped generaltable generalbox quizreviewsummary">
							<tbody>
								<tr>
									<th class="cell" scope="row">State</th>
									<th class="cell" scope="row">Started on</th>
									<th class="cell" scope="row">Completed on</th>
									<?php if($_quizSettings['usegrading']): ?>
										<th class="cell" scope="row">Marks / <?php echo float($quiz['Quiz']['sumgrades']); ?></th>
										<th class="cell" scope="row">Grade / <?php echo float($quiz['Quiz']['grade']); ?></th>
									<?php endif; ?>

									<?php if($_quizSettings['usepassfail']): ?>
										<th class="cell" scope="row">Marks</th>
										<th class="cell" scope="row">Result</th>
									<?php endif; ?>

									<!-- <th class="cell" scope="row">Review</th> -->
								</tr>
					<?php	 foreach($enrollment['QuizAttempt'] as $quizAttempt){
							//debug($quizAttempt); 
								$ta_count++;
								//pr($quizAttempt); 
								$earnedgrade = grade($quizAttempt['QuizAttempt']['sumgrades'], $quizAttempt['Quiz']['sumgrades'], $quizAttempt['Quiz']['grade']);
								$earnedgrade_formatted = float( $earnedgrade );
								$class="";
								if($this->Training->getresult( $quizAttempt, 'basic') == 'pass') $pa_count++;
								if($this->Training->getresult( $quizAttempt, 'basic') == 'fail'){ $fa_count++; $class="fail"; }
									//debug($this->Training->getresult( $quizAttempt, 'basic'));
									?>
								<tr class="attempt-default <?php  echo $class; ?>">
									<td class="cell"><?php echo ucfirst($quizAttempt['QuizAttempt']['state']) ?> </td>
									<td class="cell"><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quizAttempt['QuizAttempt']['timestart']);  ?></td>
									<td class="cell"><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quizAttempt['QuizAttempt']['timefinish']); ?></td>
									
									<?php if($_quizSettings['usegrading']): ?>
									<td class="cell"><?php echo float($quizAttempt['QuizAttempt']['sumgrades']); ?></td>
									<td class="cell"><?php echo $earnedgrade_formatted; ?></td>
									<?php endif; ?>

									<?php if($_quizSettings['usepassfail']): 
										//$quizAttempt['Quiz'] = $quiz['Quiz'];
									?>
										<?php if($_quizSettings['passfailcriteria']=='question'): ?>
														<td class="cell"><?php echo $quizAttempt['QuizAttempt']['correctquestions']; ?>/<?php echo $quizAttempt['QuizAttempt']['total_questions'] != null ? $quizAttempt['QuizAttempt']['total_questions'] : $quizAttempt['Quiz']['question_count']; ?></td>
													<?php else: ?>
														<td class="cell"><?php echo float($quizAttempt['QuizAttempt']['sumgrades']); ?>/<?php echo float($quizAttempt['Quiz']['sumgrades']); ?></td>
											<?php endif; ?>
										<td class="cell"><?php echo $this->Training->getresult( $quizAttempt, 'texticon', 1); ?></td>
									<?php endif; ?>
									<!-- <td class="cell"><?php echo $this->Html->link(__('Review Answers'), array('controller'=>'quizzes', 'action'=>'review', $quizAttempt['QuizAttempt']['id'], '?'=>array('course_id'=>$quizAttempt['Quiz']['course_id'])), array()); ?></td> -->
								</tr>
						<?php 
							$attempt_count++;
							} ?>
						</tbody>
						</table>
					<?php 
						

					 }else{
						 $na_count++;
						echo '<p><em> Not Attempted yet </em></p>';
					 }
				$enrol_count++;
			}
		}else{
			echo '<h5> No Enrollment Found </h5>';
		}
	echo '<hr class="course-separate">';
	}
}else{
			echo '<h2> No Record Found </h2>';
}
if(sizeof($courses) > 0){
?>
<h3>Summery: </h3>
<table cellpadding="0" cellspacing="0" class="table table-striped">
	<thead>
	<tr>	<th><?php echo __('#Courses'); ?></th>
			<th><?php echo h(__('Total Enrollments')); ?></th>
			<th><?php echo h(__('Total Attempts')); ?></th>
			<th><?php echo h(__('Passed')); ?></th>
			<th><?php echo h(__('Fail')); ?></th>
			<!-- <th><?php echo h(__('Not Attempted')); ?></th> -->
			<th><?php echo h(__('Pass %')); ?></th>
		</tr>
	</thead>
	<tbody>
	<tr>
	<td><?php echo sizeof($courses); ?></td>
	<td><?php echo $ce_count; ?> </td>
	<td><?php echo $ta_count; ?> </td>
	<td><?php echo $pa_count; ?></td>
	<td><?php echo $fa_count; ?> </td>
	<!-- <td><?php echo $na_count; ?> </td> -->
	<td><?php echo number_format($pa_count * 100/$ta_count); ?> </td>
	</tr>
	</tbody>
	</table>
	<?php } ?>
	</div>
</div>
