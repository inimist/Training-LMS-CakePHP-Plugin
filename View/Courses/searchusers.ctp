<?php echo $this->Html->link(__('Select All'), 'javascript:void(0)', array('id'=>'selectAll'));
		?> | <?php echo $this->Html->link(__('Unselect All'), 'javascript:void(0)', array('id'=>'unselectAll'));
		?>
<?php
if(isset($users))	{

	if($users)	{

	//$options = getlist($users);
	//$selected = array(1, 3);

	/*foreach ($users as $id=>$name)	{
		$users[$id] = $this->Html->link($name, array('controller'=>'users', 'action'=>'view', $id), array('escape'=>false));
	}*/

	//debug($selected);
	
		//$i = 0;
		//echo '<ul class="list-unstyled padLeft20">';
		//foreach($users as $user)	{
			//echo '<li class="col-md-4">';
			//$options = array('type'=>'checkbox', 'value'=>$user['User']['id'], 'label'=>false, 'div'=>false, 'id'=>'UserId' . $i);
			//echo $this->Form->input('Course.user_id][', $options);
			//echo '<label for="'.'UserId' . $i.'">' . $user['User']['full_name'] . '</label>';

			echo $this->Form->input('Course.user_id', 
					array('multiple' => 'checkbox', 'options' => $users, 'selected' => $selected, 'label'=>false, 'div'=>'listchkbox', 'class'=>"col-md-3", 'escape'=>false)
			);
			echo '<div class="clearfix"></div>';
			if (USESTARTDATE) {
				echo $this->Form->input('Course.startdate', array('type'=>'text','label'=>__('Starting From'), 'class' => 'form-control', 'div' => 'form-group', 'value'=>date('Y-m-d')));
				echo $this->Form->input('Course.duration', array('label'=>__('Course duration'), 'options'=>$months,'div' => 'form-group', 'class' => 'form-control')); //, 'empty'=>'Unlimited'
				echo $this->Form->hidden('Course.enddate', array('label'=>__('Enrolment Ends')));
			}else {  
				echo $this->Form->hidden('Course.startdate', array('value'=>date('Y-m-d')));
				echo $this->Form->input('Course.enddate', array('type'=>'text', 'label'=>__('Complete By'), 'class' => 'form-control'));
			}
			echo $this->Form->input('Course.leaddays', array('label'=>__('Lead Days'), 'div' => 'form-group', 'class' => 'form-control', 'type'=>'number', 'step'=>1, 'value'=>15));

		echo $this->Form->input('Course.set_reminder', array('type'=>'checkbox', 'div'=>'marginLeft20'));

    echo '<div class="rem-options marginLeft20" style="display:none;">';

    echo $this->Form->input('Course.reminder_date', array('type'=>'text', 'class' => 'form-control'));
		
    echo '<p></p><p>Reminder Options (Prior)</p>';
    echo $this->Form->input('Course.rem_pre_month', array('type'=>'checkbox', 'label'=>__('1 month prior')));
    echo $this->Form->input('Course.rem_pre_week', array('type'=>'checkbox', 'label'=>__('1 week prior')));
    echo $this->Form->input('Course.rem_pre_day', array('type'=>'checkbox', 'label'=>__('1 day prior')));
		echo $this->Form->input('Course.rem_today', array('type'=>'checkbox', 'label'=>__('On the day')));

    echo '<p>Reminder Options (Overdue)</p>';
    echo $this->Form->input('Course.rem_post_daily', array('type'=>'checkbox', 'label'=>__('Daily until complete')));
    echo $this->Form->input('Course.rem_post_weekly', array('type'=>'checkbox', 'label'=>__('Weekly until complete')));

    echo $this->Form->input('Course.set_post_specific_date', array('type'=>'checkbox', 'label'=>__('Specific date')));

    echo $this->Form->input('Course.rem_post_date', array('type'=>'text','label'=>__('Specific date'), 'class' => 'form-control', 'div' => array('id'=>'rem-post-date')));

    echo '</div>';
			//echo '</li>';
			//$i++;
		//}
		//echo '</ul>';
		echo $this->Form->hidden('Course.existing', array('value'=>implode($selected,',')));

		echo $this->Form->end(__('Assign Course'));

	}
} ?><script>
jQuery(function($) {
			var startDate = jQuery('#CoursesEnrollmentStartdate').val();
		//$('#CourseStartdate').datetimepicker({pickTime:false, format: 'YYYY-MM-DD'});
		$('#CourseEnddate').datetimepicker({pickTime:false, format: 'YYYY-MM-DD'}); 
		$('#CourseStartdate').datetimepicker({pickTime:false, format: 'YYYY-MM-DD', minDate: startDate});

		$('.listchkbox input[type=checkbox]').each(function()	{
			//console.log($(this).attr('id'));
			//console.log($(this).is(':checked'));
			if($(this).is(':checked'))	{
				//console.log($(this).parent('.listchkbox'));
				//$(this).parent('.listchkbox').attr('disabled', 'disabled');
			}
		})
		$('#selectAll').click(function(){
			console.log('clicked');
			$('.listchkbox input[type=checkbox]').each(function(){
				console.log('checked');
				$(this).prop('checked', true);
				})
		})
		$('#unselectAll').click(function(){
			console.log('clicked');
			$('.listchkbox input[type=checkbox]').each(function(){
				console.log('checked');
				$(this).prop('checked', false);
				})
		})
	
	// on click reminder set checkbox
	$('#CourseSetReminder').on('click ifClicked', function()	{
		$('div.rem-options').toggle();
    if($(this).is(':checked'))  {
      $('#CourseReminderDate').datetimepicker({pickTime:false});
    }
	})

	if($('#CourseSetReminder').is(':checked'))	{
    $('#CourseReminderDate').datetimepicker({pickTime:false});
		$('div.rem-options').show();
	}
	
	// on click reminder post specific date checkbox
	$('#CourseSetPostSpecificDate').on('click ifClicked', function()	{
		$('#rem-post-date').toggle();
    if($(this).is(':checked'))  {
      $('#CourseRemPostDate').datetimepicker({pickTime:false});
    }
	})

	if($('#CourseSetPostSpecificDate').is(':checked'))	{
    $('#CourseRemPostDate').datetimepicker({pickTime:false});
		$('#rem-post-date').show();
	}

	})
		</script>