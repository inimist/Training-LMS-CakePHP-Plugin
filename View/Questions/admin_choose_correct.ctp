<div id="topinvibox1">
	<div class="indent">
		<p style="font-size:20px; color:#458ed2; text-align:center; padding-top: 40px; "><em>Google Analytics Study Buddy</em></p>
	</div>
				  
	<div style=" font-size:15px;">
		<strong id="admin_display"><?php echo $question;?></strong>

		<br><br><br>
						
		<?php 
			echo $form->create('Question',array('action'=>'choose_correct'));
			echo $form->input('Choice.id', array('label'=>'','multiple' => 'checkbox', 'options' => $choices));
		?>
		
		<div align="center">
			<?php
				echo $form->end('../css/images/submit-button.png');
			?>
						
		</div>
	</div>
				  
	<div style="padding-top:35px;">
					  
	</div>
				
				  
	<div id="boxes">
					 
	</div>
	
</div>

<div style="text-align:left;">
						
	<?php echo $this->Html->image('../css/images/poweredtext.png', array('alt' => ''));?>
					
</div>