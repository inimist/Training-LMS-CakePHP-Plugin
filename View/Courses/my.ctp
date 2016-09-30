<div class="courses view">
<h2><?php echo __('My Enrollments'); ?></h2>

<div class="col-xs-16 col-md-12">
	<div class="table-responsive">
		<table class="table table-striped col-md-6">
			<tbody>
			<?php
			$this->Training->setQuizSettings($_quizSettings);
			//pr($mycourses);
			foreach($mycourses as $membership)	{

				//pr($membership);
				//check for if user has attempted course and passed it.
				?>
						<?php foreach($membership['CoursesEnrollment'] as $enrollment):

							if(isset($enrollment['QuizAttempt']) && $this->Training->getresult($enrollment, 'short') =='p') continue; 
							?>
							<tr>
							<td>
							<dl class="dl-horizontal">
								<dt>Course</dt>
								<dd><?php echo $this->Html->link($membership['Course']['name'], array('controller'=>'courses', 'action'=>'view', $membership['Course']['id'], '?'=> array('enid' =>$enrollment['CoursesEnrollment']['id']))); ?>
							
						<?php	//debug($enrollment);
							$backgroundcolor = '';
							if(@$enrollment['CoursesEnrollment']['enddate'] <= date('Y-m-d')) $backgroundcolor = 'background-color: #FFE7E7;';
							if(@$enrollment['QuizAttempt']['unlock_requested']) $backgroundcolor = 'background-color: #E5E5E5;';
						?>
							<dl class="dl-horizontal" style="border-bottom:1px solid #f0f0f0; <?php echo $backgroundcolor; ?>">

								<dt>Starts on</dt>
								<dd><?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $enrollment['CoursesEnrollment']['startdate']); ?></dd>

								<?php if($_quizSettings['usepassfail'] && isset($enrollment['Quiz'])): ?>

								<dt>Min pass questions</dt>
								<dd><?php echo $enrollment['Quiz']['minpassquestions']; ?></dd>

								<?php else: ?>
								<dt>Min pass questions</dt>
								<dd><?php echo $membership['Course']['minpassquestion_count']; ?></dd>

								<?php endif; ?>

								<dt>Assigned on</dt>
								<dd><?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $enrollment['CoursesEnrollment']['created']); ?></dd>

								<?php if($enrollment['CoursesEnrollment']['completed_date'] && isset($enrollment['QuizAttempt']) && $enrollment['QuizAttempt']['state'] == 'finished'): ?>

								<dt>Completed on</dt>
								<dd><?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $enrollment['CoursesEnrollment']['completed_date']); ?> <?php echo $this->Training->createresultlink($enrollment, $membership['Course']['id']); ?></dd>
								<dt>Due Date </dt>
								<dd><?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $enrollment['CoursesEnrollment']['enddate']); ?></dd>

								<?php else: ?>

								<dt>Complete By</dt>
								<dd><?php echo $this->Time->format(__SYSTEM_DATE_FORMAT, $enrollment['CoursesEnrollment']['enddate']); ?></dd>

								<?php endif; ?>
								</dl>
							<?php endforeach; ?>

							</dd>

					</dl>

					</td>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>
</div></div></div>

