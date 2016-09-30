
	<div class="indent">
		<p style="font-size:20px; color:#458ed2; text-align:center; padding-top: 40px; "><em>Google Analytics Study Buddy</em></p>
	</div>
	

	<fieldset>
			<legend>Select Correct Answer</legend>
				<?php
					echo $form->create('Question',array('action'=>'edit_correct_answers'));
					echo $form->input('New.correct', array('label'=>'','multiple' => 'checkbox', 'options' => $choices, 'selected'=>$selected));
					echo $this->Form->hidden('Question.id', array('value'=>$question_id));
				?>
				
				<div align="center">
			<?php
				echo $form->end('../css/images/submit-button.png');
			?>
						
		</div>
	</fieldset>
	
<div style="text-align:left;">
						
	<?php echo $this->Html->image('../css/images/poweredtext.png', array('alt' => ''));?>
					
</div>
	
	
		
	
	
	
