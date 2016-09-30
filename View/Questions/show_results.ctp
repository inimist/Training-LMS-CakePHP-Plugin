<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<?php echo $javascript->link('graph.js'); ?>
	<link rel="stylesheet" href="style.css" type="text/css" /> 
	<?php echo $this->Html->css('graph1'); ?>
</head>	


	<div id="topinvibox2">
		
		<h2 style="color:#2482cc;"><?php echo ($header_title == null) ? 'Study Buddy' : $header_title; ?></h2>
		<div id="top-right">
			&nbsp;&nbsp;
			<div style="float:right;">
				<?php echo $this->Html->image('../css/images/done-img.png', array('alt' => ''));?>
			</div>
		</div>
		</h2>

		<div class="indent">
			<p style="font-size:24px; text-align:center; color:#575757; margin-top:50px;">
				<?php if($score['status']=='failed'):?>
					<em>Sorry, you <em style="color:#dd0021;">failed</em> the quiz.</em><br><br>
					<strong><em style="font-size:20px;">Your Score: <em style="color:#dd0021;font-size:20px;"><?php echo round($score['percentage'], 2);?>%</em></em></strong>
				<?php elseif ($score['status']=='passed'):?>
					<em>Congratualtions! You <em style="color:#004d12;">passed</em> the exam!</em><br><br>
					<strong><em style="font-size:20px;">Your Score: <em style="color:#004d12;"><?php echo round($score['percentage'], 2);?>%</em></em></strong>
				<?php endif;?>
		
		
			</p>
		</div>
              
		<div style="padding-left: -18px;">
			<div id="box_result_top">
			</div>
			<div id="result">
			
				<p style="text-align:center; color:#575757;">
					
					
					<br>
					<div align="center" style="margin-top:25px;">	
								
						<div id="wrapper_graph">
					<div class="chart">
						<h2 id="graph">Score By Topic<br><em style="font-size:12px;">(%)</em></h2>
						<table id="data-table" border="1" cellpadding="10" cellspacing="0">
							
							<thead>
								<tr>
									<td>&nbsp;</td>
									<?foreach($percent as $counter):?>
									
										<th scope="col"><?php echo $counter['title'];?></th>
									
									<?php	endforeach; ?>
									
								</tr>
							</thead>
							<tbody>
								
								<tr>
									
									<?foreach($percent as $counter):?>
									
									
										<td><?php echo round($counter['score'], 2);?></td>
									
									<?php	endforeach; ?>
								</tr>	
									
									
									
								</tr>
							
							</tbody>
						</table>
					</div>
				</div>
						</div>
					<br>
					<div align="center" style="margin-top:-50px; margin-left: -20px;">
					<p style="color:#575757; font-size:12px; padding-left: 100px;">
						<fieldset id="result" style="width:450px;" >
							<em style="font-weight:bold;">Questions Answered:</em> <em style="color:#2a81ce;"><?php echo $items_answered."/".$total_items;?></em><br><br>
							<em style="font-weight:bold;">Date Test Completed:</em> <?php echo $date_completed;?><br><br>
							<em style="font-weight:bold;">Username:</em> <?php echo $user_name;?><br><br>
							<em style="font-weight:bold;">Email:</em> <?php echo $user_email;?><br><br>
						</fieldset>
						
									
								
						
								
					</p>
					</div>

				</p>
				
			
			</div>
			<div id="box_result_bottom">
			</div>
			
			<div align="center" style="margin-top: 50px; margin-bottom: 50px;">
							
				<?php 
					$this->Session->delete('start_time');
					echo $html->link($html->image('../css/images/try-again-button.png'), "/quizzes/take_again", array('escape'=>false)); 
				?>
				<?php echo $html->link($html->image('../css/images/exit-button.png'), '/quizzes/home', array('escape'=>false));  ?>
			</div>
		</div>
			  

	</div>
	<div style="text-align:left;">
		<?php
			$style = ($viewlogo == 'off') ? 'visibility: hidden' : '';
			echo $this->Html->image('../css/images/poweredtext.png', array('alt' => '', 'style' => $style));
		?>
	</div>