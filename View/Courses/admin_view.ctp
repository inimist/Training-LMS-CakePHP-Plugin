<div class="courses view">
<h2 class="pull-left"><?php 
//pr($course);
global $course_id;
$course_id = $course['Course']['id'];
$this->Training->setQuizSettings($_quizSettings);
echo __('Course View & Reports'); ?> <a href="<?php echo $this->Html->url(array('action'=>'view', $course['Course']['id'], 'admin'=>false)); ?>"><?php echo $this->Html->image('/training/img/icon.svg'); ?></a></h2>
<!-- <div class="inline pull-right">
<?php echo $this->Form->create(false , array('class'=>''), array('type' => 'get'));  ?>
<?php echo $this->Form->submit(__('Export'),array('class'=>'pull-right btn btn-default' ,'name'=>'Export', 'id'=>'exportdata')); ?>

<?php echo $this->Form->end(); ?>
</div> -->
<div class="pull-right">
		<?php echo $this->Form->create('Report', array('url'=>array('control'=>'reports', 'action'=>'index', 'admin'=>true, 'plugin'=>false )));  ?>
		<?php echo $this->Form->hidden('report_type', array('value'=>'Training Reports')); ?>
		<?php echo $this->Form->hidden('training_report_type', array('value'=>'Training Name Report')); ?>
		<?php echo $this->Form->hidden('training_report_name_options', array('value'=>$course['Course']['id'])); ?>
		<?php echo $this->Form->hidden('training_report_by_user_group', array('value'=>false)); ?>
		<?php echo $this->Form->hidden('training_report_by_period', array('value'=>false)); ?>
		<div style="display:flex">
		<?php echo $this->Form->submit(__('Export Recent Attempts'),array('class'=>'pull-right btn btn-default marginRight10' ,'name'=>'Export', 'id'=>'exportRecentAttemps')); ?>
		<?php echo $this->Form->submit(__('Export'),array('class'=>'pull-right btn btn-default' ,'name'=>'Export', 'id'=>'exportAttempts')); ?>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>


	<table id="courseStatsTable" class="table table-striped course-index">
		<thead>
			<tr>
				<th ><?php echo __('Title'); ?></th>
				<th ><?php echo __('Module Type'); ?></th>
				<th ><?php echo __('Users Assigned'); ?></th>
				<th ><?php echo __('Total Attended'); ?></th>
				<th ><?php echo __('Pass'); ?></th>
				<th ><?php echo __('Fail'); ?></th>
				<th ><?php echo __('# Quiz available'); ?></th>
				<th ><?php echo __('# Questions in pool'); ?></th>
				<th ><?php echo __('# Questions needed to pass'); ?></th>
				<th ><?php echo __('Signature y/n'); ?></th>
				<th ><?php echo __('Repeat Months'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="cell"><?php echo h($course['Course']['name']); ?> &nbsp;</td>
				<td class="cell"><?php echo $course['Course']['source_type']=='Document' ? __('Downloadable') : __('On-Line');; ?> &nbsp;</td>
				<td class="cell"><?php $user_count = $course['Course']['user_count'] ? $course['Course']['user_count'] : 0;
				echo $this->Html->link(($user_count), array('action' => 'enrol', 'admin'=>true, $course['Course']['id'])); ?></td>
				<td class="cell"><?php echo $course['Course']['completed_count']; ?></td>
				<td class="cell pass-count"> <?php //echo $course['Course']['pass_count']; ?></td>
				<td class="cell fail-count"><?php //echo $course['Course']['fail_count']; ?></td>
				<td class="cell"><?php echo $this->Html->link(h($course['Course']['quiz_count']), array('controller'=>'quizzes', 'action' => 'index', 'admin'=>true, '?'=>array('course_id'=>$course['Course']['id'])));; ?></td>
				<td class="cell"><?php echo $this->Html->link(h($course['Course']['question_count']),  array('controller'=>'questions', 'action' => 'index', 'admin'=>true, '?'=>array('course_id'=>$course['Course']['id']))); ?></td>
				<td class="cell"><?php echo $course['Course']['quizquestion_count']; ?></td>
			<!-- 	<td class="cell"><?php echo $course['Course']['minpassquestion_count']; ?></td> -->
				<td><?php echo h($course['Course']['signature']) ? 'Y': 'N'; ?>&nbsp;</td>
				<td><?php echo $course['Course']['frequency']; ?></td>
			</tr>

		</tbody>
	</table> 
<!-- 	<?php
		echo $this->Form->create('Course', array(
				'inputDefaults' => array(
					'div' => false,
					'wrapInput' => false,
				),
				'class' => 'course-form',
			));
		echo $this->Form->input('limit', array('label'=>'Records per page', 'type'=>'select', 'options'=>$limits, 'class'=>'col-md-1 col-xs-2 right marginBottom10', 'id'=>'changeLimit'));
		echo $this->Form->end(); 
	?> -->
<div class="col-xs-16 col-md-12">
		<h4 ><?php echo __('Enrollments'); ?></h4>
		<?php $this->Utility->pagination_form('CoursesEnrollment'); ?>
		<?php echo $this->Form->create('QuizAttempt', array('url'=>array('controller'=>'quizzes', 'action'=>'switchAllLock', 'admin'=>true, '?'=>array('course_id'=>$course['Course']['id'])))
					); ?>
			<?php echo $this->Form->hidden('QuizAttempt.unlock_ids');  ?>
			<?php echo $this->Form->button(__('Unlock All'), array('type'=>"submit", 'div'=>false, 'class'=>"btn btn-default marginBottom10", 'id'=>'unlockALL')); ?>
			<?php echo $this->Form->button(__('Unlock Only Requested'), array('type'=>"submit", 'div'=>false, 'class'=>"btn btn-default marginBottom10", 'id'=>'requestedUnlockALL')); ?>
		<?php echo $this->Form->end(); ?>

		<!-- <div class="clear"></div> -->
	<div class="table-responsive">
	
		<!-- <h3><?php echo __('Existing Enrollments'); ?></h3> -->

		<table class="table table-bordered col-md-6">
			<tbody>
			<tr> 
				<!-- 	<th>Select</th> -->
					<th>User Code</th>
					<th>Name</th>
					<th>Manager</th>
					<th>User Type</th>
					<th nowrap="nowrap">Start on</th>
					<th nowrap="nowrap">Completed</th>
					<th>Locked</th>
					<th>Signed</th>
					<th>Complete By</th>
					<th>Lead Days</th>
			</tr>
			<?php
			$pass = 0; $fail = 0;
			$locked = array(); $unlocked_requested = array();
			foreach($courses_enrollments as $enrollment){
				//pr($enrollment);
				$attended = false;
				$pass_fail = '';
			?>
				<tr> 
					<!-- 	<td><input type="checkbox" /></td> -->
					<td><?php echo $enrollment['User']['username']; ?></td>
					<td nowrap="nowrap"><?php echo $this->Html->link($this->Utility->avatar30(array('source'=>$enrollment['User'])), array('controller'=>'users', 'action'=>'view', $enrollment['User']['id']), array('escape'=>false)); ?> <?php echo $this->Utility->linkeduname($enrollment['User']); ?></td>
					<td><?php echo @$this->Utility->linkeduname($enrollment['User']['Supervisor']); ?></td>
					<td><?php if(isset($enrollment['User']['Role'])) echo $enrollment['User']['Role']['role_name']; ?></td>

					<td nowrap="nowrap"><?php
					if(count($enrollment['CoursesEnrollment'])>0)	{
					echo '<ol class="olistmarginfix">';
					foreach($enrollment['CoursesEnrollment'] as $module)	{
						echo '<li>';
						//$gh="hello";
						//if (USESTARTDATE) {
							echo $this->Time->format($module['CoursesEnrollment']['startdate']);
					/*	}
						else {
							echo $this->Time->format($module['CoursesEnrollment']['created']);

						} */
						echo '</li>';
					}
					echo '</ol>';
				}?></td>
<!-- 	<td nowrap="nowrap"><?php
					if(count($enrollment['CoursesEnrollment'])>0)	{
					echo '<ol class="olistmarginfix">';
					foreach($enrollment['CoursesEnrollment'] as $module)	{
						echo '<li>';
						echo $this->Time->format($module['CoursesEnrollment']['startdate']);
						echo '</li>';
					}
					echo '</ol>';
}?></td> -->

					<td nowrap="nowrap"><!-- Completed --><?php
			if(count($enrollment['CoursesEnrollment'])>0)	{
					//debug($enrollment);
					//$QAForLinks = $enrollment['QuizAttempt'];
					echo '<ol class="olistmarginfix">';
					foreach($enrollment['CoursesEnrollment'] as $module){
						if($module['CoursesEnrollment']['completed_date'] && isset($module['QuizAttempt'])){
							$attended = true;
							$pass_fail = $this->Training->getresult($module, 'short');
							//$pass_fail = substr($module['QuizAttempt']['result'], 0, 1);
							//debug($module['CoursesEnrollment']['completed_date']);
							echo '<li>';
							echo  $module['QuizAttempt']['state']=='finished' ? $this->Time->format($module['CoursesEnrollment']['completed_date']) : 'Re-attempt In Progress';  //if user re-attempting and its inprogress.
							echo ' ';
							echo $this->Training->createresultlinks( $module );
							echo '</li>';
						}
					}
					echo '</ol>';
				}?></td>
					<td><?php 
					
				if(count($enrollment['CoursesEnrollment'])>0)	{
					foreach($enrollment['CoursesEnrollment'] as $module)	{
						if(!isset($module['QuizAttempt'])) continue;
						if($module['QuizAttempt']['locked'])	{
							$locked[] = $module['QuizAttempt']['id'];
							echo $this->Html->link(__('Locked'), array('controller'=>'quizzes', 'action'=>'switchlock', $module['QuizAttempt']['id'], '?'=>array('course_id'=>$course['Course']['id'])), array("data-toggle"=>"modal", "data-target"=>"#requestUnlockAttempt", 'onclick'=>'return false'));
							if($module['QuizAttempt']['unlock_requested'])	{
								$unlocked_requested[] = $module['QuizAttempt']['id'];
								echo '<span class="glyphicon glyphicon-bell" style="color:orange;"></span>';
							}
						}	else	{
							echo $this->Html->link(__(' -- '), array('controller'=>'quizzes', 'action'=>'switchlock', $module['QuizAttempt']['id'], $course['Course']['id']), array("data-toggle"=>"modal", "data-target"=>"#requestUnlockAttempt", 'onclick'=>'return false'));
						}
						echo '<br />';
					}}
				 ?></td>
				 <td><!-- Signed -->
				 <?php if($course['Course']['signature']){
					if(count($enrollment['CoursesEnrollment'])>0)	{
					foreach($enrollment['CoursesEnrollment'] as $module){
						if(!isset($module['QuizAttempt'])) continue;
						echo $module['QuizAttempt']['signed_by_user']? $this->Utility->get_signature($enrollment['User']['id'], '100') : "No";
						echo '<br />';
						}
					}
				 }else{
					echo "Not required";
				 }
				 ?> </td>
					<td><!-- Complete By --><?php
			if(count($enrollment['CoursesEnrollment'])>0)	{
					foreach($enrollment['CoursesEnrollment'] as $module)	{
						if($module['CoursesEnrollment']['enddate'])	{
							echo $this->Time->format($module['CoursesEnrollment']['enddate']);
							echo '<br />';
						}
					}
				}?></td>
					<td><!-- Lead days --><?php
			if(count($enrollment['CoursesEnrollment'])>0)	{
					foreach($enrollment['CoursesEnrollment'] as $module)	{
							echo $module['CoursesEnrollment']['leaddays'];
							echo '<br />';
					}
				}?></td>
				</tr>

			<?php
			if($attended){
					$pass_fail == 'p' ? $pass++ : $fail++ ;
				}
			}
			?>

			</tbody>
		</table>

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
</div>
</div>

<div class="modal fade" id="requestUnlockAttempt" tabindex="-1" role="dialog" aria-labelledby="requestUnlockAttemptLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>
<script>
jQuery(function($)	{
	$('#courseStatsTable td.pass-count').html("<?php echo $pass; ?>");
	$('#courseStatsTable td.fail-count').html("<?php echo $fail; ?>");

	$('#requestUnlockAttempt').on('show.bs.modal', function (event) {
		//var button = $(event.relatedTarget) // Button that triggered the modal
		//var recipient = button.data('target') // Extract info from data-* attributes
		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
		//var modal = $(this)
		//modal.find('.modal-title').text('Edit marks of ' + recipient)
		//modal.find('.modal-body input').val(recipient)
	})

	var locked = "<?php echo implode(',', $locked) ?>";
	var unlocked_requested = "<?php echo implode(',', $unlocked_requested) ?>";
	$('#unlockALL').on('click', function(e)	{
		e.preventDefault();
		$('#QuizAttemptUnlockIds').val(locked);
		$('#QuizAttemptAdminViewForm').submit();
	});
	$('#requestedUnlockALL').on('click', function(e)	{
		e.preventDefault();
		$('#QuizAttemptUnlockIds').val(unlocked_requested);
		$('#QuizAttemptAdminViewForm').submit();
	});
	
	$('#exportRecentAttemps').on('click', function(e){
		e.preventDefault();
		$('#ReportTrainingReportByPeriod').val('Recent Attempts');
		
		$('#exportAttempts').click();
	})

})
</script>
