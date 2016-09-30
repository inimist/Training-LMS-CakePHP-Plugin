

	<?php if($score['status']=='failed'):?>
		<h3>Sorry. You <?php echo $score['status'];?> the exam.</h3><br><br>

	<?php elseif ($score['status']=='passed'):?>
		<h3>Congratulations! You <?php echo $score['status'];?> the exam.</h3><br><br>

	<?php endif;?>

	Your Score: <?php echo round($score['percentage'], 2);?>%<br>
	Date Test Completed: <?php echo $date_completed;?><br>
	Username: <?php echo $user_name;?><br>
	Email: <?php echo $user_email;?><br><br>
	
	<?php 
		$this->Session->delete('start_time');
		echo $html->link('Take Quiz Again', array('controller'=>'quizzes', 'action'=>'take_again')); 
	?> 
	&nbsp;
	<?php echo $html->link('Exit Quiz', '/pages/home');  ?>
	