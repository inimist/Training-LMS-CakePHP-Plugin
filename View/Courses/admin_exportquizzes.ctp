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

<h3 style="text-align:center;">Quizzes Report</h3>

	<p style="text-align:center;">
		<strong>Course: </strong> <?php echo $course['Course']['name']; ?><br>
	</p>
	<hr>
	<!-- <p class="course-description"><?php echo $course['Course']['description']; ?> <p> -->
<?php //if($courses['Course'])
if(sizeof($results) > 0){
		$ta_count = $pa_count = $fa_count = $na_count = $ce_count = 0;

	//pr($courses);
	$this->Training->setQuizSettings($_quizSettings);
	foreach($results as $user){
	?>
	<!-- <p><strong>User: </strong> <?php echo $user['User']['full_name']; ?></p> -->
	<p style="text-align:center;">
		<strong>User:</strong> <?php echo $this->Utility->linkeduname($user['User']); ?><br>
		<strong>Full Name:</strong> <?php echo $user['User']['full_name']; ?><br>
		<strong>Department:</strong> <?php echo $user['Department']['name']; ?><br>
	</p>
	<br>
	<?php if(sizeof($user['Quiz'])> 0){
			$quiz_count = 1;
			foreach($user['Quiz'] as $quiz){
				$ce_count++;
				//echo "<strong> #".$quiz_count ." Quiz : </strong>";
				 //echo $quiz['Quiz']['name']; ?>
				<?php
					if(sizeof($quiz['QuizAttempt'])>0){
						echo "<p class='center'> Total Attempts: " .sizeof($quiz['QuizAttempt']). " </p>"; 
						?>
						<?php 
							$attempt_count = 0;
						foreach($quiz['QuizAttempt'] as $quizAttempt){
										$attempt_count++;
										$quizAttempt['Quiz'] = $quiz['Quiz'];
										$earnedgrade = grade($quizAttempt['QuizAttempt']['sumgrades'], $quizAttempt['Quiz']['sumgrades'], $quizAttempt['Quiz']['grade']);
										$earnedgrade_formatted = float( $earnedgrade );
									?>
											<br><strong>Test: "<?php echo $quizAttempt['Quiz']['name']; ?>" - Preview (Attempt:#<?php echo $attempt_count; ?>)</strong>

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
														<td class="cell"><?php echo $quizAttempt['QuizAttempt']['correctquestions']; ?>/<?php echo $quizAttempt['QuizAttempt']['total_questions']; ?></td>
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
									foreach($quizAttempt['QuestionAttempt'] as $QuestionAttempt){
										echo $this->Training->renderQuestionAttempt($QuestionAttempt, $this->layout);
									}
							//$attempt_count++;
							} 	
				?>



						<!-- <p><strong>Summary of your attempts for Test: "<?php echo $quizAttempt['Quiz']['name']; ?>"</strong><p> -->
						<!-- <table class="table table-striped generaltable generalbox quizreviewsummary">
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
						
									<th class="cell" scope="row">Review</th>
								</tr>
											<?php	 foreach($quiz['QuizAttempt'] as $quizAttempt){
								$ta_count++;
								$quizAttempt['Quiz'] = $quiz['Quiz'];
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
														<td class="cell"><?php echo $quizAttempt['QuizAttempt']['correctquestions']; ?>/<?php echo $quizAttempt['Quiz']['question_count']; ?></td>
													<?php else: ?>
														<td class="cell"><?php echo float($quizAttempt['QuizAttempt']['sumgrades']); ?>/<?php echo float($quizAttempt['Quiz']['sumgrades']); ?></td>
											<?php endif; ?>
										<td class="cell"><?php echo $this->Training->getresult( $quizAttempt, 'texticon', 1); ?></td>
									<?php endif; ?>
									<td class="cell"><?php echo $this->Html->link(__('Review Answers'), array('controller'=>'quizzes', 'action'=>'review', $quizAttempt['QuizAttempt']['id'], '?'=>array('course_id'=>$quizAttempt['Quiz']['course_id'])), array()); ?></td>
								</tr>
						<?php 
							$attempt_count++;
							} ?>
						</tbody>
						</table> -->
					<?php 
					 }else{
						 $na_count++; ?>
						<br><strong>Test: "<?php echo $quizAttempt['Quiz']['name']; ?>" - Preview </strong>
					<?php echo '<p><em> Not Attempted yet </em></p>';
					 }
				$quiz_count++;
			}
		}else{
			echo '<h5> Quiz not found for course </h5>';
		}
	echo '<hr class="course-separate">';
	}
}else{
			echo '<h2> No Record Found </h2>';
}
?>
<!-- <h3>Summery: </h3>
<table cellpadding="0" cellspacing="0" class="table table-striped">
	<thead>
	<tr>	<th><?php echo __('#Courses'); ?></th>
			<th><?php echo h(__('Total Enrollments')); ?></th>
			<th><?php echo h(__('Total Attempts')); ?></th>
			<th><?php echo h(__('Passed')); ?></th>
			<th><?php echo h(__('Fail')); ?></th>
			<th><?php echo h(__('Not Attempted')); ?></th>
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
	<td><?php echo $na_count; ?> </td>
	<td><?php echo number_format($pa_count * 100/$ta_count); ?> </td>
	</tr>
	</tbody>
	</table> -->
	</div>
</div>
