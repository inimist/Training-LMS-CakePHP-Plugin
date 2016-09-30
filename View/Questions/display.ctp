<html>
	<head>
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript">
			var i = <?php echo $elapse;?>;
			var remaining_time = <?php echo $remaining_time;?>;
			var beforeDate, beforeTime, afterDate, afterTime, diff, totalTime;
			var time_hr, time_min, time_sec = '';
			
			function showIt() {
				i++;
				remaining_time--;
				if(remaining_time <= 0){
					window.location.replace("<?php echo $this->Html->url('/questions/times_up/1');?>");
				
				}
				if (i%5==0){
					$.ajax({
						type: "POST",
						url: "<?php echo $this->Html->url('/quizzes/time');?>", /*********/
						
						beforeSend: function (){
							beforeDate = new Date();
							beforeTime = beforeDate.getTime();
						},
						success: function(result){
							afterDate = new Date();
							afterTime = afterDate.getTime();
							diff = (afterTime-beforeTime)/1000;
							totalTime = Math.round(parseFloat(result) + parseFloat(diff));
							i = totalTime;
							
						}
					});

				}
				
				time_hr = Math.floor(remaining_time/3600); /*********/
				time_min = Math.floor((remaining_time%3600)/60); /*********/
				time_sec = remaining_time%60; /*********/
				$("#time1").html(time_hr+':'+time_min+':'+time_sec); /*********/
				setTimeout(showIt, 1000);
			}
			
			function mark_question(qq_id)
			{
				var mark;
				
				if(document.getElementById('mark').checked == 1)
				{	
					mark = 1;
				}
				else{
					mark = 0;
				}
				
				$.ajax({
					type: "POST",
					url: "<?php echo $this->Html->url('/questions/mark/');?>"+qq_id+"/"+mark
				
				
				
				});
				
				
				
				
			}
			
			function pause(qq_id, review, from_review, last_paused){
			
				var pause=confirm("Do you really want to pause the quiz?");
				
				if(pause){
					window.location.replace("<?php echo $this->Html->url('/quizzes/pause/');?>"+qq_id+"/"+from_review+"/review:"+review+"/"+last_paused);
				}
				
				
			
			}
			
			function disableHandler (form, inputName, limit) {
				var inputs = form.elements[inputName];
				var counter = 0;
				if(limit==0){
					for (var i = 0; i < inputs.length; i++) {
						var currentInput = inputs[i];
						if (inputs[i].checked==false) {
							currentInput.disabled = true;
						}
					}
				}
				for (var i = 0; i < inputs.length; i++) {
					var input = inputs[i];
						
					input.onclick = function (evt) {
						if (this.checked) {
							counter = counter + 1;
						}
						else{
							counter = counter - 1;
						}
						
						if(counter==limit){
							disableInputs(inputs);
						}
						else{
							enableInputs(inputs);
						}
						
						
						return true;
					};
				}
			}

			function disableInputs (inputs) {
				for (var i = 0; i < inputs.length; i++) {
					var currentInput = inputs[i];
					if (inputs[i].checked==false) {
						currentInput.disabled = true;
					}
				}
			}

			function enableInputs (inputs) {
				for (var i = 0; i < inputs.length; i++) {
					var currentInput = inputs[i];
				
						currentInput.disabled = false;
					
				}
			}
			
			$(document).ready(function(){

				$("ul.subnav").parent().append("<span></span>"); //Only shows drop down trigger when js is enabled (Adds empty span tag after ul.subnav*)

				$("ul.topnav li span").click(function() { //When trigger is clicked...

					//Following events are applied to the subnav itself (moving subnav up and down)
					$(this).parent().find("ul.subnav").slideDown('fast').show(); //Drop down the subnav on click

					$(this).parent().hover(function() {
					}, function(){
						$(this).parent().find("ul.subnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
					});

					//Following events are applied to the trigger (Hover events for the trigger)
					}).hover(function() {
						$(this).addClass("subhover"); //On hover over, add class "subhover"
					}, function(){	//On Hover Out
						$(this).removeClass("subhover"); //On hover out, remove class "subhover"
				});

			});

			
		
			
			
		</script>
		
	</head>
	
	
	<body onload="showIt()">
		
			<div id="top-left">
				
					<h2 style="color:#2482cc; clear:both;margin-bottom:30px; font-weight:bold;"><?php echo ($header_title == null) ? 'Study Buddy' : $header_title; ?></h2>
			
			</div>
			<div id="after_title">
				<div id="top-left" style="margin-bottom:-10px;">
				
					<em style="color:#4d4d4d; font-size: 12px;">Time Left</em>
					<span id="time1"></span><br><br>
					
				
				</div>
				<div id="top-right">
					<ul class="topnav">
						<li>
							<ul class="subnav">
								<?
									foreach($data as $id=>$num):
										$item = $id + 1;
										
										if($num['QuestionQuiz']['is_marked'] == 1):?>
											<strong id="dropdown"><?php echo $item." Marked";?></strong><br>	
										<?php elseif($num['QuestionQuiz']['answered'] != 0):?>
											<strong id="dropdown"><?php echo $item." Answered";?></strong><br>
										<?php else:?>
											<strong id="dropdown"><?php echo $item." Not Answered";?></strong><br>
										<?php endif;
										
									endforeach;
								?>
							</ul>
						</li>

					</ul>
				
				</div>
				<div id="top-left" style="margin-top:20px;">
					<strong id="items"><?php echo $number; ?> / <?php echo $total_items; ?></strong>
				</div>
				<div id="top-right" style="margin-right:-100px; margin-top:15px;">
					<?php
							//<a href="#"><img src="images/pause-play-button.png" width="33" height="29" alt="" /></a>
							$question_quiz_id = $question['QuestionQuiz']['id'];
							echo $this->Form->button($html->image('../css/images/pause-play-button.png'), array('onclick' => 'pause('.$question_quiz_id.', '.$review.', '.$from_review.', '.$last_paused.')'));
							
					?>	
				</div>
		
        </div>
		<div class="indent" style="font-size:18px; color:#4d4d4d; ">
			
			
			<br><br>
            <?php echo $question['Question']['text'];
			if($counter_answers>1):?>
				<em style="color:red;">[<?php echo $counter_answers;?> Answers]</em>	
			<?php endif;?>
        </div>
             <br>
        <div id="dividerx-choice">
			<em style="font-size: 14px; color:#4d4d4d; font-weight:bold;">
				<?php
				
					echo $form->create('Question', array('name'=>'display_form', 'action'=>'submit'));
					if($type=='radio'){
						echo $this->Form->radio('Submit.answer', $choices, $attributes);
					}
					else{
						echo $form->input('Submit.answer', array('label'=>'','multiple' => 'checkbox', 'options' => $choices, 'selected' => $selected));
					}
					
					echo $this->Form->hidden('Submit.type', array('value'=>$type));
					echo $this->Form->hidden('Submit.from_review', array('value'=>$from_review));
					echo $this->Form->hidden('Submit.review', array('value'=>$review));
					echo $this->Form->hidden('Submit.last_paused', array('value'=>$last_paused));
					echo $this->Form->hidden('Submit.question_quiz_id', array('value'=>$question['QuestionQuiz']['id']));
					
				
				?>
			</em>
		</div>
			  
		<div>
			
			<div id="dividerx-prev"><a href="#"></a>
				<?php
					if($from_review == 1):
						echo $html->link($html->image('../css/images/cancel-button.png'), "/questions/review/$last_paused/0/review:{$review}", array('escape'=>false));
					
					else:
						if($number != 1):
						
							echo $html->link($html->image('../css/images/prev.png'), "/questions/previous/{$question_quiz_id}/{$from_review}/review:{$review}", array('escape' => false ));
						endif;
					endif;
				?>
			
			</div>
			<div id="dividerx-next">
				<?php
					if($from_review == 1):
						echo $form->end('../css/images/submit-button.png'); 
					
					else:
						echo $form->end('../css/images/next.png'); 
					endif;
				
				?>
			</div>
			<?php if($type=='check' ):?>
				
					<script type="text/javascript">
						disableHandler(document.forms.display_form, 'data[Submit][answer][]', <?php echo $limit; ?>);
					</script>
				
			<?php endif;?>
		</div>
		
		<div id="dividerx1"><? echo $form->input('mark',array('label'=>'Mark this item', 'type'=>'checkbox', 'checked'=>$marked, 'onclick' => 'mark_question('.$question_quiz_id.')'));?></div>
		<div id="dividerx1"><?php echo $this->Html->image('../css/images/time-loader.png', array('alt' => ''));?></div>
			                
        <div id="boxes">
                 
        </div>
	</body>
</html>