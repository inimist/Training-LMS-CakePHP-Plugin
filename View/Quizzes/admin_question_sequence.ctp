<div class="quizzes index">

	<h2><?php echo $course['Course']['name']; ?></h2>

	<h3><?php echo __('Add Questions'); ?></h3>
	<p>To quiz: <?php echo $quiz['Quiz']['name']; ?>

	<?php echo $this->Form->create('Quiz', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well quiz-form'
));

//global $next_slot;

function next_slot($slots, &$next_slot)	{
	foreach($slots as $slot)	{
		if($next_slot < $slot['slot'])	{
			$next_slot = (int)$slot['slot'];
		}
	}
	$next_slot++;
}

next_slot($quiz['QuizSlot'], $next_slot);

function next_page($slots, &$next_page)	{
	foreach($slots as $slot)	{
		if($next_page < $slot['page'])	{
			$next_page = (int)$slot['page'];
		}
	}
	$next_page++;
}

next_page($quiz['QuizSlot'], $next_page);
//debug($next_slot);

function already_added($slots)	{
	$added = array();
	foreach($slots as $slot)	{
		//debug($slot);
		$added[] = $slot['question_id'];
	}
	return $added;
}

function get_slot($qid, $slots)	{
	foreach($slots as $slot)	{
		if($qid == $slot['question_id'])	{
			return $slot['slot'];
			break;
		}
	}
	return null;
}

function get_page($qid, $slots)	{
	foreach($slots as $slot)	{
		if($qid == $slot['question_id'])	{
			return $slot['page'];
			break;
		}
	}
	return null;
}
//get estimate of total number of pages will be in quiz.
$num_pages = sizeof($quiz['QuizSlot'])/1; //'1' is number of questions per page
$num_slots_per_page = 1;
$added = already_added($quiz['QuizSlot']);

//debug($quiz['QuizSlot']);
	
echo $this->Form->hidden('id', array('value'=>$quiz['Quiz']['id']));

?>
<fieldset>
 <strong>Drag n Drop question to set sequence: </strong>
 <table class="table table-striped" cellpadding="0" cellspacing="0">
	<?php
		//debug($questions);
		//Outer loop for Pages
		$slot_num = 0;
		for($i=0; $i<$num_pages; $i++){
		//echo '<ol id="pagelist">';
		$curr_page = $i+1;
		$slot_count = $i * $num_slots_per_page + 1;
		?> 
		 <tr>
				<th><?php echo 'Page - '.$curr_page; ?> </th>
		</tr>
		<tr>
			<td class="drag-drop-section" >
			<div class= "question" >
				<ul class= "dropable" type="none" ondrop="drop(event)" ondragover="allowDrop(event)">
				<?php
				echo $this->Form->hidden('page_number.'.$curr_page, array('value' =>$curr_page, 'class'=>'page-number'));
				//Inner loop for Questions section/ number of slots per page
				foreach($quiz['QuizSlot'] as $slot){
					if($slot['page']==$curr_page){
							foreach ($questions as $question){
								if($question['Question']['id'] == $slot['question_id']){
								echo '<li id="question-'.$question['Question']['id'].'" draggable="true" ondragstart="drag(event)" class="draggable" >';
									//variable value field i.e. need to change according to position
								 echo $this->Form->hidden('QuizSlot.'.$slot_num.'.page', array('value'=>$curr_page, 'class'=>'input_page_number'));
								 echo $this->Form->hidden('QuizSlot.'.$slot_num.'.slot', array('value'=>$slot_count, 'class'=>'input_slot_number'));
								 //fixed value field i.e. no affect of drag n drop
								 echo $this->Form->hidden('QuizSlot.'.$slot_num.'.quiz_id', array('value'=>$quiz['Quiz']['id']));
								 echo $this->Form->hidden('QuizSlot.'.$slot_num.'.question_id', array('value'=>$question['Question']['id']));
								 echo $this->Form->hidden('QuizSlot.'.$slot_num.'.maxmarks', array('value'=>$question['Question']['defaultmark']));
								 echo $this->Form->hidden('QuizSlot.'.$slot_num.'.id', array('value'=>$slot['id']));
								echo h($question['Question']['title']); 
								echo '</li>';
								$slot_count++;
								$slot_num++;
								}
							}
					}
				}?>
				</ul>
			</div>
			</td>
		</tr>
	<?php } ?>

	</table>

	<?php if(!$questions):	?>
		<p>No questions found. <?php echo $this->Html->link(__('Add Question'), array('controller'=>'questions', 'action'=>'add', '?'=>['course_id'=>$course['Course']['id']])); ?> to question bank.</p>
	<?php endif;	?>

	<!-- <p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="pagination">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div> -->

<div class="clearfix"></div>
<br />
<div class="textcenter">
<?php echo $this->Form->submit(__(' Save Changes'), array('id'=>'submitSlotChanges')); ?>
</div>
<br />
</fieldset>
<?php echo $this->Form->end(); ?>

</div>

<script>


function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
		console.log(data);
    ev.target.appendChild(document.getElementById(data));
}
jQuery(function($){
			var slots_per_page = "<?php echo $num_slots_per_page; ?>";
	$('.dropable').on('drop', function() {
			//alert('dropped!!');
			var maxSlotreached = false;
		$('.dropable').each(function(){
       var pagenum = $(this).children('.page-number').val();
       $(this).find('.input_page_number').each(function(){
				 $(this).val(pagenum);
			 });
			 var slotnum = (pagenum-1)*slots_per_page + 1;
			//console.log(slotnum);
			$(this).find('.input_slot_number').each(function(){ //set slot number to each question for a page.
					 $(this).val(slotnum);
						slotnum++;
				 });
			 //reset slots 
			 var slot = 0;
			 $(this).find('.input_slot_number').each(function(){
				 slot++;
				 //$(this).val(slot);
			 });
			 //console.log(slot);
			 if(slot > slots_per_page ){
				 $(this).css("border", "2px dashed #db6767");
				 maxSlotreached = true;
				}else{
					$(this).removeAttr('style');
				}
			});
		if(maxSlotreached){
				 $('#submitSlotChanges').attr('disabled', 'disabled');
				}else{
					$('#submitSlotChanges').removeAttr('disabled');
				}
	});

 });
</script>