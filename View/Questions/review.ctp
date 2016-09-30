

	
	
	
	<div id="review_header">
					<div id="left_review_header">
						
					</div>
					<div id="right_review_header">
						<h2><?php echo ($header_title == null) ? 'Study Buddy' : $header_title; ?></h2>
						<div id="left_topreview">
							<h3>REVIEW QUESTIONS</h3>
						</div>
						<div id="top-right">
							<em style="color:#4d4d4d; font-size: 12px;">Time Left:</em>
							<em style="color:#000; font-size: 14px;"><?php echo $remaining_time;?></em>
							&nbsp;&nbsp;
						</div>
					</div>
				</div>
				
				<div id="reviewbody">
					<div id="reviewbody_top">	
						<?php if($counter_unanswered_marked == 0):?>
							<br><br><br>
							<div align="center"><em style="color:red; font-size: 17px; font-style:italic;">You have answered all of the questions.</em></div>
						<?php endif;?>
						
					</div>
					<div id="reviewbody_content">
						<?php
							
							foreach($data as $id=>$num):
							
								$item = $id + 1;
								if($item%25==1):?>
									<div id="box_content">
										<TABLE id="review">
								<?php endif;?>
							
								
									
										<?php
											if($num['QuestionQuiz']['is_marked'] == 1){?>
												<TR><td class="ex1">
													<?php echo $html->link($item, "/questions/display/{$num['QuestionQuiz']['id']}/1/{$question_quiz_id}/review:{$review}");?>
												</td>
												<td style="color:#4d4d4d; font-size: 12px;">Marked</td>
												</TR>
											<?php }	
											else if($num['QuestionQuiz']['answered'] != 0){ ?>
												<TR><td class="ex2">
													<?php echo $html->link($item, "/questions/display/{$num['QuestionQuiz']['id']}/1/{$question_quiz_id}/review:{$review}");?>
												</td>
												<td style="color:#4d4d4d; font-size: 12px;">Answered</td>
												</TR>
											<?php }	
											else{?>
												<TR><td class="ex">
													<strong id="unmarked_unanswered"><?php echo $item; ?></strong>
													<?php //echo $html->link($item, "#");?>
												</td>
												<td style="color:#4d4d4d; font-size: 13px;">Not Answered</td>
												</TR>
											<?php }
										?>
										
									
									
								
						<?php
								if($item%25==0 || $item==$count_data):?>
										</TABLE>
									</div>
								
								<?php endif;
							endforeach;

						?>
						
						
						
					
					</div>
				</div>
	
	<br><br>
	<div style="text-align:center;">
		<?php
			if($counter_unanswered_marked == 0):
				echo $html->link($html->image('../css/images/see-result.png'), array('action'=>'show_results', true), array('escape'=>false), sprintf(__('Are you sure you want to get your quiz result?', true)));
			else:
				echo $html->link($html->image('../css/images/continue-button.png'), "/questions/display/{$question_quiz_id}/{$from_review}/review:{$review}", array('escape'=>false));?> &nbsp;&nbsp;&nbsp;<?php
				echo $html->link($html->image('../css/images/back-button.png'), "/quizzes/pause/{$question_quiz_id}/{$from_review}/review:{$review}", array('escape'=>false));
			endif;
		?>
	</div>
				  
	<div id="boxes">
					 
	</div>


<div style="text-align:left;">
						
	<?php
		$style = ($viewlogo == 'off') ? 'visibility: hidden' : '';
		echo $this->Html->image('../css/images/poweredtext.png', array('alt' => '', 'style' => $style));
	?>
					
	
</div>