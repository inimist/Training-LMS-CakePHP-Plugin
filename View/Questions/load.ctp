<div id="topinnerbox">
	<div class="indent">
		<p style="font-size:20px; color:#458ed2; text-align:center; padding-top: 70px; "><em><?php echo ($header_title == null) ? 'Study Buddy' : $header_title; ?></em></p>

		<p style="text-align:center;">
			<em style="font-size: 22px; color:#4d4d4d; font-weight:bold;">
						Welcome back <?php echo $user;?>!
			</em>
		</p>
</div>
<div align="center">
	<?php 
		if($finished == true):?>
			You already finished the exam.<br><br><br><br><br><br>
			<?php echo $html->link($html->image('../css/images/see-result.png'), array('action'=>'show_results', true),array('escape'=>false));
		
		elseif($counter_unanswered_marked == 0 && $remaining_time>0):?>
			You still have <?php echo $time;?> time left and have answered all of the questions. You can review your answers if you wish.
			<br><br><br><br>
			<?php echo $html->link($html->image('../css/images/continue-button.png'), "/questions/review/review:0", array('escape' => false )); 
		
		elseif($finished==false && $remaining_time<=0):?>
			You don't have enough time. Hence, the exam has ended.<br><br>
			<?php echo $html->link($html->image('../css/images/see-result.png'), array('action'=>'show_results', true),array('escape'=>false));
			
		elseif($unanswered>0 || $marked>0):?>
			Answered Questions: <?php echo $answered_unmarked+$answered_marked;?>
				<li>Marked Unsure: <?php echo $answered_marked;?></li>
				<li>Not Marked: <?php echo $answered_unmarked;?></li>
			<br>	
			<h4>Unanswered Questions: <?php echo $unanswered_unmarked+$unanswered_marked;?></h4>
				<li>Marked: <?php echo $unanswered_marked;?></li>
				<li>Not Marked: <?php echo $unanswered_unmarked;?></li><br><br>
				
				<?php if($unanswered>0):
					echo $html->link($html->image('../css/images/continue-button.png'), "/questions/display", array('escape' => false ));
				else:
					echo $html->link($html->image('../css/images/continue-button.png'), "/questions/more_time/true", array('escape' => false ));
				endif;
		
		endif;
		
		

	?>
</div>