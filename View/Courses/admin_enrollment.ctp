<div class="courses edit form">

<h2><?php echo $enrollment['User']['full_name']; ?> : <?php echo $this->Html->link($enrollment['Course']['name'], array('controller'=>'courses', 'action'=>'view', $course['Course']['id'])); ?></h2>

<?php echo $this->Form->create('CoursesEnrollment', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well course-form'
)); ?>
<?php echo $this->Form->input('id'); ?>
<?php echo $this->Form->hidden('course_id');  //debug(USESTARTDATE); ?>
<?php if(USESTARTDATE) { echo $this->Form->input('startdate', array('type'=>'text','label'=>__('Starting From'))); ?>
<?php  echo $this->Form->input('duration', array('label'=>__('Course duration'), 'options'=>$months, 'class' => 'form-control', 'empty'=>'Unlimited'));
}else{
		echo $this->Form->hidden('startdate', array('type'=>'text'));
}
?>
<?php echo $this->Form->input('enddate', array('type'=>'text','label'=>__('Complete By'))); ?>
<?php echo $this->Form->input('leaddays', array('label'=>__('Lead Days')));  ?>
<?php if(USESTARTDATE) {  ?>
<?php echo $this->Form->input('autocalcenddate', array('type'=>'checkbox', 'label'=>__('Automatically calculate <b>Complete by</b> date?')));  } ?>
<?php
		echo $this->Form->input('set_reminder');

    echo '<div class="rem-options marginLeft20" style="display:none;">';

    echo $this->Form->input('reminder_date', array('type'=>'text'));
		
    echo '<p></p><p>Reminder Options (Prior)</p>';
    echo $this->Form->input('rem_pre_month', array('type'=>'checkbox', 'label'=>__('1 month prior')));
    echo $this->Form->input('rem_pre_week', array('type'=>'checkbox', 'label'=>__('1 week prior')));
    echo $this->Form->input('rem_pre_day', array('type'=>'checkbox', 'label'=>__('1 day prior')));
		echo $this->Form->input('rem_today', array('type'=>'checkbox', 'label'=>__('On the day')));

    echo '<p>Reminder Options (Overdue)</p>';
    echo $this->Form->input('rem_post_daily', array('type'=>'checkbox', 'label'=>__('Daily until complete')));
    echo $this->Form->input('rem_post_weekly', array('type'=>'checkbox', 'label'=>__('Weekly until complete')));

    echo $this->Form->input('set_post_specific_date', array('type'=>'checkbox', 'label'=>__('Specific date')));

    echo $this->Form->input('rem_post_date', array('type'=>'text','label'=>__('Specific date'), 'div' => array('id'=>'rem-post-date')));

    echo '</div><p></p>';

?>
<!-- <dl>
		<dt>Complete By</dt>
		<dd><?php //echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $enrollment['CoursesEnrollment']['enddate']); 
		//echo $this->Form->input('enddate', array('type'=>'text','label'=>__('Starting From')));		
		?></dd>
</dl> -->

<dl>
		<dt>Assigned on</dt>
		<dd><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $enrollment['CoursesEnrollment']['created']); ?></dd>
</dl>

<?php echo $this->Form->end(__('Save Changes')); ?>
</div>


<script>
jQuery(function($) {
	var startDate = jQuery('#CoursesEnrollmentStartdate').val();
		$('#CoursesEnrollmentStartdate').datetimepicker({pickTime:false, format: 'YYYY-MM-DD'});//, minDate: '<?php echo $enrollment['CoursesEnrollment']['startdate']; ?>'
		$('#CoursesEnrollmentEnddate').datetimepicker({pickTime:false, format: 'YYYY-MM-DD', minDate: startDate});//, minDate: '<?php echo $enrollment['CoursesEnrollment']['enddate']; ?>'
		$('#CoursesEnrollmentReminderDate').datetimepicker({pickTime:false, format: 'YYYY-MM-DD'});
		$('#CoursesEnrollmentRemPostDate').datetimepicker({pickTime:false, format: 'YYYY-MM-DD'});

		// on click reminder set checkbox
		$('#CoursesEnrollmentSetReminder').on('click ifClicked', function()	{
			$('div.rem-options').toggle();
			if($(this).is(':checked'))  {
				//$('#CoursesEnrollmentReminderDate').datetimepicker({pickTime:false});
			}
		});

		if($('#CoursesEnrollmentSetReminder').is(':checked'))	{
			//$('#CoursesEnrollmentReminderDate').datetimepicker({pickTime:false});
			$('div.rem-options').show();
		}
		
		// on click reminder post specific date checkbox
		$('#CoursesEnrollmentSetPostSpecificDate').on('click ifClicked', function()	{
			$('#rem-post-date').toggle();
			if($(this).is(':checked'))  {
				//$('#CoursesEnrollmentRemPostDate').datetimepicker({pickTime:false});
			}
		});

		if($('#CoursesEnrollmentSetPostSpecificDate').is(':checked'))	{
			//$('#CoursesEnrollmentRemPostDate').datetimepicker({pickTime:false});
			$('#rem-post-date').show();
		}

	});

		</script>