		<?php  ?>
		
		<legend><?php echo __('Question Choices'); ?></legend>
		<div id="questionsAnswers">
		<!-- <label for="QuestionTitle">Answers</label> -->

	<?php
			if($this->request->data['QuestionAnswer']){ //if question - answers are already saved, then only answer count should option as options
					if(!isset($this->request->data['Question']['num_answers'])) $num_answers = sizeof($this->request->data['QuestionAnswer']);
					echo $this->Form->hidden('remove_answers'); //input for recording removed answers
				}
		for($i=0;$i<$num_answers;$i++){ 
			echo '<div class="well answers">';
			if(isset($this->request->data['QuestionAnswer'][$i])){
				if(isset($this->request->data['QuestionAnswer'][$i]['id'])){
					echo $this->Form->input('QuestionAnswer.'.$i.'.id', array('class'=>'answer-id'));
					echo '<a href="#" class="remove_answer pull-right">Remove</a>';
				}
			}else{
					echo '<a href="#" class="remove_option pull-right">Remove</a>';
				}
			if(isset($this->request->data['QuestionAnswer'][$i]['fraction'])) 
					$this->request->data['QuestionAnswer'][$i]['correct_anwer'] = $this->request->data['QuestionAnswer'][$i]['fraction'] > 0 ? 1 : 0;
			echo $this->Form->input('QuestionAnswer.'.$i.'.answer', array('type'=>'text','label'=>sprintf(__('Choice %d'), $i+1), 'div'=>'form-group'));
			echo $this->Form->input('QuestionAnswer.'.$i.'.correct_anwer', array('type'=>'checkbox', 'label'=>'check if correct',  'class'=>'correct-answer'));
			echo $this->Form->hidden('QuestionAnswer.'.$i.'.fraction', array('options'=>$fractions, 'label'=>__('Grade'), 'div'=>'form-group'));
			echo '</div>';
		}
		?>
	</div>
	<script>
		var removeEntries =[];
		$(document).ready(function(){
		/*
		var x = <?php echo $i; ?>; //initlal text box count
		$('#addNewEntry').click(function(e){ //on add input button click
			e.preventDefault();
			x++; //text box increment
			$('#addEntry').append('<div class="form-group"><input class="form-control" type="text" name="data[LogColumnsCustomListEntry]['+ x + '][entry_name]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
			});
			*/
		$('#questionsAnswers').on("click",".remove_option", function(e){ //user click on remove text
			 e.preventDefault();  
			// alert('remove clicked'); 
			 $(this).parent('.answers').remove();
			});
			
		$('#questionsAnswers').on("click", ".remove_answer", function(e){ //user click on remove text
			 e.preventDefault();
			 //alert('remove clicked');
			 var removeEntryId = $(this).siblings('input.answer-id').val(); 
				removeEntries.push(removeEntryId);
				$(this).parents('.answers').remove();
				var removables = removeEntries.join(',');
				$('#QuestionRemoveAnswers').val(removables);
				//alert(removables);
			});
			
	/* function validateAnswers(){
				var qType = $('#QuestionQuestionTypeId').val();
				if(qType == 5){
					var rs_count = $(".correct-answer :checked").length;
					if(rs_count >= 1) {
						console.log(rs_count);
						alert("You can't mark more than 1 answer as correct");
						return false;
						}
					}
					return true;
				}
		*/
	$('.correct-answer').on('click ifClicked', function(){
		var qType = $('#QuestionQuestionTypeId').val();
			if(qType == 5){
			$('.correct-answer').prop('checked', false);
			$('.correct-answer').parent('div').removeClass('checked');
				console.log(qType);
			}
		});
	
	$('#editQuestionAnswer').on('click', function(e){
						e.preventDefault();
					$('#editQuestionAnswer').before( '<input type="hidden" name="data[Question][submit]"  value="Save Changes" >' );
					var qType = $('#QuestionQuestionTypeId').val();
					var rs_count = $(".correct-answer:checked").length;
					console.log(rs_count);
					if(qType == 5){
						if(rs_count > 1) {
							console.log(rs_count);
							alert("You can't mark more than 1 answer as correct for Multiple Choices (One Answer)");
						}else if(rs_count == 0){
							console.log(rs_count);
							alert("Mark atleast 1 answer as correct");
						}else{
							$('#QuestionAdminEditForm').submit();
						}
					}
					if(qType == 6){
						if(rs_count == 0){
							console.log(rs_count);
							alert("Mark atleast 1 answer as correct");
						}else{
							$('#editQuestionAnswer').before( '<input type="hidden" name="data[Question][correct_ans_num]"  value="' + rs_count + '" >' );
							$('#QuestionAdminEditForm').submit();
						}
					}
				});

		});
	</script>