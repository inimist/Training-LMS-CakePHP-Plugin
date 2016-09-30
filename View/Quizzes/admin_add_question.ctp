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

$added = already_added($quiz['QuizSlot']);

//debug($quiz['QuizSlot']);
	
echo $this->Form->hidden('id', array('value'=>$quiz['Quiz']['id']));

?>
<fieldset>
	<table class="table table-striped" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo __('Select'); ?></th>
			<!-- <th><?php echo $this->Paginator->sort('slot'); ?></th> -->
			<th><?php echo $this->Paginator->sort('name'); ?></th>
      <th><?php echo $this->Paginator->sort('description'); ?></th>
			<th><?php echo $this->Paginator->sort('question_type_id'); ?></th>
			<th><?php echo __('Correct Answer'); ?></th>
			<!-- <th class="actions"><?php echo __('Actions'); ?></th> -->
	</tr>
	</thead>
	<tbody>
	<?php $i=0; 
	
	//debug($questions);
		
	foreach ($questions as $question): 
	
	$already = in_array($question['Question']['id'], $added);
	
	$chkoptions = array('hiddenField' => false, 'value' => $question['Question']['id']);
	$cssclass = '';$rowdisabled='';
	if($already)	{
		//continue;
		$chkoptions['readonly'] = 'readonly';
		$chkoptions['checked'] = 'checked';
		$chkoptions['disabled'] = 'disabled';
		$cssclass = 'disabled';
		$rowdisabled = 'disabled="disabled"';
	}

	$slot = get_slot($question['Question']['id'], $quiz['QuizSlot']);
	$slot = $slot ? $slot : $next_slot++;

	$page = get_page($question['Question']['id'], $quiz['QuizSlot']);
	$page = $page ? $page : $next_page++;

	?><tr class="<?php echo $cssclass ?>" <?php echo $rowdisabled ?>>
		<td><?php echo $this->Form->checkbox('QuizSlot.'.$i.'.question_id', $chkoptions); ?></td>
		<td><?php echo h($question['Question']['title']); ?>&nbsp;</td>
    <td><?php echo substr( $question['Question']['description'], 0, 100); ?>
		
		
		<?php echo $this->Form->hidden('QuizSlot.'.$i.'.slot', array('value'=>$slot, 'size'=>1)); ?>
		<?php echo $this->Form->hidden('QuizSlot.'.$i.'.quiz_id', array('value'=>$quiz['Quiz']['id'])); ?>
		<?php echo $this->Form->hidden('QuizSlot.'.$i.'.page', array('value'=>$page)); ?>
		<?php echo $this->Form->hidden('QuizSlot.'.$i.'.maxmarks', array('value'=>$question['Question']['defaultmark'])); ?>
		
		</td>
		<td><?php echo h($question['QuestionType']['title']); ?>&nbsp;</td>
		<td><?php if(sizeof($question['QuestionAnswer'])>0) {
								foreach($question['QuestionAnswer'] as $answer ) {
									echo h($answer['QuestionAnswer']['answer']);
									if(sizeof($question['QuestionAnswer'])>1) echo " , ";
									}
								} ?>&nbsp;
			</td>
		<!-- <td class="actions">
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'removequestion', $question['Question']['id'], '?'=>array('course_id' => $question['Question']['course_id'])), array('class'=>'color-fff'), __('Are you sure you want to delete # %s?', $question['Question']['id'])); ?>
		</td> -->
	</tr>
<?php $i++; endforeach; ?>
	</tbody>
	</table>

	<?php if(!$questions):	?>
		<p>No questions found. <?php echo $this->Html->link(__('Add Question'), array('controller'=>'questions', 'action'=>'add', '?'=>['course_id'=>$course['Course']['id']])); ?> to question bank.</p>
	<?php endif;	?>

	<p>
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
	</div>

<div class="clearfix"></div>
<br />
<div class="textcenter">
<?php echo $this->Form->submit(__(' Save Changes')); ?>
</div>
<br />
</fieldset>
<?php echo $this->Form->end(); ?>

</div>
