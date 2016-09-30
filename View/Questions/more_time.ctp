<h2 style="color:#2482cc;"><?php echo ($header_title == null) ? 'Study Buddy' : $header_title; ?></h2>
            <div class="indent">
				
				<p style="font-size:20px; "><em>You still have <em style="color:#2482cc;"><?php echo $marked; ?></em> marked questions. Do you want to return to those questions?</em></p>
            </div>
              
            <div id="dividerx">
				
				<?php echo $html->link($html->image('../css/images/button-yes.png'), array('action'=>'get_marked'), array('escape'=>false));?>
			</div>
			
			<div id="dividerx">
				
				<?php echo $html->link($html->image('../css/images/button-no.png'), array('action'=>'show_results', true), array('escape'=>false), sprintf(__('Are you sure you want to get your quiz result?', true)));?>
			</div>
			  
			<div id="dividerx">
				<em style="font-size:18px; font-weight:bold; padding-left:70px;">
					Time Left: <?php echo $remaining_time;?>
				</em>
			</div>
			
			<div id="dividerx1">
		
				<?php echo $this->Html->image('../css/images/time-loader.png');?>
			</div>
              
            <div id="boxes">
			</div>