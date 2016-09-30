

<div id="topinvibox1">
				  <div class="indent">
					  <p style="font-size:20px; color:#458ed2; text-align:center; padding-top: 40px; "><em><?php echo ($header_title == null) ? 'Study Buddy' : $header_title; ?></em></p>
					<hr style="color:#dddddd;" />
					  <p style="text-align:center;"><strong><em style="font-size: 14px; color:#4d4d4d;">
						
						<?php echo (empty($taglines[3])) ? 'Click start button and enjoy the quiz! Goodluck!' : $taglines[3]; ?>
					  </em></strong></p>
				  </div>
				  
				  <div style="text-align:center; padding-left:160px; font-size:15px;">
						
					</div>
				  
				  <div style="padding-top:35px;">
					  
				  </div>
				  <div style="text-align:center;">
					
						<?php echo $html->link($html->image('../css/images/start-now-button.png'), "/questions/display", array('escape' => false)); ?>
				 </div>
				  
				  <div id="boxes">
					 
				  </div>
				  </div>
				  <div style="text-align:left;">
						<?php
							$style = ($viewlogo == 'off') ? 'visibility: hidden' : '';
							echo $this->Html->image('../css/images/poweredtext.png', array('alt' => '', 'style' => $style));
						?>
						
				  </div>
