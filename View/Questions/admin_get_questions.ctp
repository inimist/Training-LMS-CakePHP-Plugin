<?php
if(isset($questions)){ //debug($questions); ?>

	<table class="table table-striped" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo __('Select'); ?></th>
			<th><?php echo __('Question') ?></th>
		</tr>
	</thead>
	<tbody>

	<?php
		$i=0;
	foreach($questions as $id=>$question){  
		$chkoptions = array('hiddenField' => false, 'value' => $id);
		?>
	<tr class="questions">
		<td><?php echo $this->Form->checkbox('Questions.'.$i.'.question_id', $chkoptions); ?></td>
		<td><?php echo __($question); ?>&nbsp;</td>
	</tr>
	<?php $i++;
		} ?>
	</tbody>
	</table>
<?php //echo $this->Form->input('Control.department_id', array('label'=>__('Department'), 'value'=>$dept_id, 'class'=>'form-control'));
}
else{
	echo "<p style='color:red;'>Course/Question Bank hasn't any question</p>";
}
