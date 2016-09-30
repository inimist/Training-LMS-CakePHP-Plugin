<div class="courses index">
	<?php $this->Utility->pagination_form('Course'); ?>
		<!-- <span class="clear"> </span> -->
	<div class = "col-md-12 col-xs-16 well">
		<?php 	
		echo $this->Form->create('Course', array(
			'inputDefaults' => array(
				'div' => 'form-group',
				'wrapInput' => false,
				'class' => 'form-control'
			),
			'class' => 'col-md-8 col-xs-8 wel'
			));  ?>
			<fieldset>
			<br>
			<div class="col-md-2 col-xs-2 legend"><p>Filter By:</p></div>
			<div class="col-md-6 col-xs-8">
				<?php echo $this->Form->input('search_keyword', array('label'=>false)); ?>
			</div>
		   <div id="courseListFilters" class="col-md-4 col-xs-6 ">
					<?php echo $this->Form->submit(__('Search'),array('name'=>'data[Course][search]')); ?>
		   </div>
    	</fieldset>
    <?php echo $this->Form->end(); ?>
		<div class= "col-md-4 col-xs-8">
			<?php echo $this->Form->create('Report', array('url'=>array('control'=>'reports', 'action'=>'index', 'admin'=>true, 'plugin'=>false )));  ?>
			<br>
			<?php echo $this->Form->hidden('report_type', array('value'=>'Training Reports')); ?>
			<?php echo $this->Form->hidden('training_report_type', array('value'=>false)); ?>
			<?php echo $this->Form->hidden('training_report_by_user_group', array('value'=>false)); ?>
			<?php echo $this->Form->hidden('training_report_by_period', array('value'=>false)); ?>
			<div class="pull-right">
				<div style="display:flex">
				<?php echo $this->Form->submit(__('Export Recent Attempts'),array('class'=>'pull-left btn btn-default marginRight10' ,'name'=>'Export', 'id'=>'exportRecentdata')); ?>
				<?php echo $this->Form->submit(__('Export'),array('class'=>'btn btn-default' ,'name'=>'Export', 'id'=>'exportdata')); ?>
				</div>
			</div>
		</div>
	</div>
	<h2><?php echo __('Courses'); ?></h2>
	<table width="100%" class="table table-striped course-index " cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th rowspan="2"><?php echo $this->Form->checkbox('allselect', array('name' => 'allselect', 'hiddenField'=>false, 'label'=>false, 'id'=>'selectAllRecords')); ?> </th>
		<!--<th><?php echo __('Select'); ?></th>   -->
			<th rowspan="2"><?php echo __('Action'); ?></th>
			<th rowspan="2"><?php echo $this->Paginator->sort('name', __('Title')); ?></th>
			<!-- <th rowspan="2"><?php echo __('Description'); ?></th>
			<th rowspan="2"><?php echo $this->Paginator->sort('source_type', __('Module Type')); ?></th> -->
			<th colspan ="2"><?php echo __('Launch'); ?></th>
			<!-- <th><?php echo __('Test'); ?></th> -->
			<th rowspan="2"><?php echo $this->Paginator->sort('question_count', __('# Questions in pool')); ?></th>
			<th rowspan="2"><?php echo $this->Paginator->sort('quiz_count', __('# Quizzes')); ?></th>
			<th rowspan="2"><?php echo $this->Paginator->sort('quizquestion_count', __('Questions to ask')); ?></th>
			<th rowspan="2"><?php echo $this->Paginator->sort('minpassquestion_count', __('# Questions needed to pass')); ?></th>
			<th rowspan="2"><?php echo $this->Paginator->sort('user_count', __('# Users Assigned')); ?></th>

	<!-- 		<th rowspan="2"><?php echo $this->Paginator->sort('pastdue_count'); ?></th> -->
			<th rowspan="2"><?php echo $this->Paginator->sort('signature', __('Signature Y/N')); ?></th>
			<th rowspan="2"><?php echo $this->Paginator->sort('repeats', __('Repeat Months')); ?></th>
			<!-- <th rowspan="2"><?php echo $this->Paginator->sort('master_course', __('Master Course')); ?></th> -->
	</tr>
	<tr>
		<th class="padTop0" ><?php echo __('Course'); ?></th>
		<th class="padTop0" ><?php echo __('Quiz'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($courses as $course): ?>
	<tr class="<?php echo $this->Utility->statuscss('Course', $course); ?> actions">
		<td><?php echo $this->Form->checkbox('course_id', array('name' => 'data[Report][course_ids][]', 'value'=>$course['Course']['id'] , 'hiddenField'=>false, 'class'=>'select-course-checkbox', 'label'=>false, 'form'=>'ReportAdminIndexForm')); ?></td>
		<td class="td_ac"><?php echo $this->Html->link(__(''), array('action' => 'view', $course['Course']['id']),array('class'=>'padding-rightt glyphicon glyphicon-th-list','title'=>'View')); ?>
		<?php echo $this->Html->link(__(''), array('action' => 'edit', $course['Course']['id']),array('class'=>'padding-rightt glyphicon glyphicon-edit','title'=>'Edit')); ?>
		<?php echo $this->Utility->deleteButton($course, 'Course'); ?></td>
		<td><?php echo __($course['Course']['name']); ?>&nbsp;</td>
		<!-- <td style="white-space: normal !important;"><?php echo __(substr($course['Course']['description'], 0,70)); ?>&nbsp;<?php if(strlen($course['Course']['description'])>70)
		        		echo $this->Html->link(__('more..'), '#', array("data-toggle"=>"modal", "data-target"=>"#viewInfo", "data-id"=>$course['Course']['id'], "data-title"=>$course['Course']['name'], 'onclick'=>'return false'));
		        		?><div style="display:none;" id="desc-title-<?php echo $course['Course']['id']; ?>"><?php echo nl2br($course['Course']['description']); ?></div></td>
		<td><?php echo $course['Course']['source_type']=='Document' ? __('Downloadable') : __('On-Line'); ?>&nbsp;</td> -->
		<td><?php echo $this->Html->link('Launch Course', array('action'=>'learn', 'admin'=>false, $course['Course']['id']), array('class'=>'glyphicon glyphicon-play-circle', 'title'=>'Launch Course')); ?>&nbsp;</td>
		<td><?php echo $this->Html->link('Launch Quiz', array('action' => 'testrun', $course['Course']['id']), array('class'=>'glyphicon glyphicon-copy', 'title'=>'Launch Quiz')); ?></td>
		<td><?php echo $this->Html->link($course['Course']['question_count']? $course['Course']['question_count'] : 0 , array('controller'=>'questions', 'action' => 'index', 'admin'=>true, '?'=>array('course_id'=>$course['Course']['id']))); ?>&nbsp;</td>
		<td><?php echo $this->Html->link($course['Course']['quiz_count']? $course['Course']['quiz_count'] : 0, array('controller'=>'quizzes', 'action' => 'index', 'admin'=>true, '?'=>array('course_id'=>$course['Course']['id']))); ?>&nbsp;</td>
		<td><?php echo $course['Course']['quizquestion_count'] ? $course['Course']['quizquestion_count'] : 0 ; ?>&nbsp;</td>
		<td><?php echo $course['Course']['minpassquestion_count'] ? $course['Course']['minpassquestion_count'] :0 ; ?>&nbsp;</td>
		<td><?php echo $this->Html->link($course['Course']['user_count'] ? $course['Course']['user_count'] : 0, array('action' => 'enrol', 'admin'=>true, $course['Course']['id'])); ?>&nbsp;</td>
<!-- <td><?php echo $this->Html->link($course['Course']['pastdue_count'], array('action' => 'enrol', 'admin'=>true, $course['Course']['id'])); ?>&nbsp;</td> -->
		<td><?php echo h($course['Course']['signature']) ? 'Y': 'N'; ?>&nbsp;</td>
		<td><?php echo $course['Course']['frequency']; ?></td>
		<!-- <td><?php echo h($course['Course']['master_course']) ? 'Y': 'N'; ?>&nbsp;</td> -->

	</tr>
<?php endforeach; ?>
	</tbody>
	</table>

	<?php echo $this->Form->end(); ?>  <!-- Close Form -->
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
</div>
<?php echo $this->Utility->initModelHTML(); ?>
<script type="text/javascript">
		jQuery(function($)	{
	$('#viewInfo').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) // Button that triggered the modal
		var title = button.data('title');
		var id = button.data('id');
		var modal = $(this);
		modal.find('.modal-title').text(title)
    //alert($('#desc-title-'+id).html());
		modal.find('.modal-body').html($('#desc-title-'+id).html());
	})

	$('#exportRecentdata').on('click', function(e){
		e.preventDefault();
		$('#ReportTrainingReportByPeriod').val('Recent Attempts');
		
		$('#exportdata').click();
	})
	
	 $('#selectAllRecords').on('click ifClicked', function()	{
		if($(this).is(':checked')){
				$('.select-course-checkbox').parent('div').removeClass('checked');
				$('.select-course-checkbox').prop('checked', false);
			}else{
				$('.select-course-checkbox').prop('checked', true);
				$('.select-course-checkbox').parent('div').addClass('checked');
			}
	});

})
</script>

