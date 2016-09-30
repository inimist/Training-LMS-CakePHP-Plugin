<div class="courses view">
<h2>Training Course: <?php echo h($course['Course']['name']); ?></h2>

<!-- <p>Created by: <?php echo $this->Utility->linkeduname($course['Creator']); ?></p> -->

<h4>Active Questions</h4>

<?php 
						if(isset($course['Quizzes']) && sizeof($course['Quizzes'])>0)	{ ?>
							<table class="table" cellpadding="0" cellspacing="0">
								<?php $i=0; foreach ($course['Quizzes'] as $quiz): ?>
									<thead>
										<tr>
											<th><?php echo __('Test'); ?>: <?php echo $this->Html->link($quiz['Quiz']['name'], array('controller'=>'quizzes', 'action'=>'edit', $quiz['Quiz']['id'], '?'=>array('course_id'=>$quiz['Quiz']['course_id']))); ?></th>
											<th class="text-right"><?php echo $this->Html->link(__('Manage Questions'), array('controller'=>'quizzes', 'action'=>'edit', $quiz['Quiz']['id'], '?'=>array('course_id'=>$quiz['Quiz']['course_id']))); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td colspan="2">
											<?php 
												if(isset($course['Quizzes']) && sizeof($course['Quizzes'])>0)	{ 
											?>
											<table class="table" cellpadding="0" cellspacing="0">
												<tbody>
													<?php $i=0; foreach ($quiz['Questions'] as $question): ?>
														<tr>
															<td>
																<?php echo ++$i; ?>.
															</td>
															<td>
																<?php echo $question['Question']['title']; ?>
															<td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
											<?php } ?>
								<?php endforeach; ?>
								</tbody>
							</table>

					<?php } ?>
</div>
