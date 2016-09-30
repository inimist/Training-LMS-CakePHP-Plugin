<?php
//pr($questions);

?>
<div class="questions index">
<?php echo $this->Form->create(false , array('class'=>''), array('type' => 'get'));  ?>
<?php echo $this->Form->submit(__('Export Stats'),array('class'=>'pull-right btn btn-default' ,'name'=>'Export')); ?>
<?php echo $this->Form->end(); ?>
	<h2><?php echo __('Questions Stats'); ?></h2>
	<table class="table table-striped" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
	
	<th><?php echo __('#'); ?></th>
	<th><?php echo __('Title'); ?></th>	
	<th><?php echo __('TotalAttempt'); ?></th>	
	<th><?php echo __('Correct'); ?></th>	
	<th><?php echo __('Incorrect'); ?></th>	
	<th><?php echo __('% Passed'); ?></th>

	</tr>
	</thead>
	<tbody>
	<?php $i=0;
	foreach ($questions as $question): 
	$i++;
	$q=$question['correct'];  $f=$question['incorrect'];?>
	 
	<tr class="<?php echo $this->Utility->statuscss('Question', $question); ?>">
			
		<td><?php echo $i; ?>&nbsp;</td>	
			<td><?php echo h($question['title']); ?>&nbsp;</td>	
		<td><?php echo  $c = $q + $f ;   ?>&nbsp;</td>	
		<td><?php echo $question['correct'] ?>&nbsp;</td>	
		<td><?php echo $question['incorrect'] ?>&nbsp;</td>
		<td><?php echo (int) (($q/$c)*100) ?>% &nbsp;</td>	

	</tr>
<?php endforeach; ?>
	</tbody>
	</table>

</div>
