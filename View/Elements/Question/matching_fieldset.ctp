<style>
.match-pairs .form-control{
    /* width: 45% !important; */
}

</style>
<legend><?php echo __('Matching Pairs'); ?></legend>
	<div id="matchpairs">
		<!-- <label for="QuestionTitle">Answers</label> -->

	<?php
			if($this->request->data['QuestionPairMatch']){ //if question - answers are already saved, then only answer count should option as options
					if(!isset($this->request->data['Question']['num_pair'])) $num_answers = sizeof($this->request->data['QuestionPairMatch']);
					echo $this->Form->hidden('remove_pair'); //input for recording removed answers
				}
		for($i=0;$i<$num_answers;$i++){ 
			echo '<div class="well match-pairs row">';
			if(isset($this->request->data['QuestionPairMatch'][$i])){
				if(isset($this->request->data['QuestionPairMatch'][$i]['id'])){
					echo $this->Form->input('QuestionPairMatch.'.$i.'.id', array('class'=>'answer-id'));
					echo '<a href="#" class="remove_answer pull-right">Remove</a>';
					}
				}else{
					echo '<a href="#" class="remove_option pull-right">Remove</a>';
				}
				//debug($this->request->data['QuestionPairMatch'][$i]['display_order']);
			$pair_order = isset($this->request->data['QuestionPairMatch'][$i]['pair_order']) ? $this->request->data['QuestionPairMatch'][$i]['pair_order'] : $i+1; 
			$display_order = isset($this->request->data['QuestionPairMatch'][$i]['display_order']) ? $this->request->data['QuestionPairMatch'][$i]['display_order'] : $i+1 ;
			echo $this->Form->hidden('QuestionPairMatch.'.$i.'.question_id', array('value'=>$this->request->data['Question']['id']));
			echo $this->Form->input('QuestionPairMatch.'.$i.'.pair_order', array('type'=>'text', 'value'=> $pair_order, 'div'=>'form-group col-xs-1 col-md-1', 'style'=>'width:35px;'));
			echo $this->Form->input('QuestionPairMatch.'.$i.'.question_pair_left', array('type'=>'textarea', 'label'=>'Pair Left', 'style'=>'height:100px', 'div'=>'form-group col-xs-6 col-md-4'));
			echo '<div class="col-xs-1 col-md-1" style="height:100px; text-align: center;"> <=> </div>';

			echo $this->Form->input('QuestionPairMatch.'.$i.'.display_order', array('type'=>'text','value'=> $display_order, 'div'=>'form-group col-xs-1 col-md-1', 'style'=>'width:35px;'));
			echo $this->Form->input('QuestionPairMatch.'.$i.'.question_pair_right', array('type'=>'textarea', 'label'=>'Pair Right', 'style'=>'height:100px','div'=>'form-group col-xs-6 col-md-4'));
			echo '</div>';
		}
		?>
</div>
	<!-- <script>
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
		$('#matchpairs').on("click",".remove_option", function(e){ //user click on remove text
			 e.preventDefault();  
			// alert('remove clicked'); 
			 $(this).parent('.answers').remove();
			});
			
		$('#matchpairs').on("click", ".remove_answer", function(e){ //user click on remove text
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
	</script> -->