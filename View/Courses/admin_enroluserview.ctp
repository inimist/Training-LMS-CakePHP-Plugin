<?php	//echo $this->Html->css('/app/plugin/training/webroot/style.css'); ?>
<div class="courses view">
  <p>Here you can enroll multiple users to multiple courses</p>

  <div class="col-xs-16 col-md-12">
    <div class="table-responsive">
    <?php echo $this->Form->create('CoursesEnrollment', array(
					'url'=>array('controller'=>'courses', 'action'=>'enroluserview', 'admin'=>true, 'plugin'=>'training'),
					'inputDefaults' => array(
						'div' => 'form-group',
						'wrapInput' => false,
						'class' => 'form-control'
					),
					'class' => 'well quiz-form'
				)); ?>
      <table class="table table-bordered white-space-wrap" style="max-height:400px;">
        <tbody>
          <tr>
            <th><?php echo $this->Form->checkbox('usermultiselect', 
					array('name' => 'usermultiselect','hiddenField'=>false,'label'=>false, 'id'=>'usermultiselect', 'escape'=>false)
			);?><?php echo __('Users'); ?></td>
            <th><?php echo $this->Form->checkbox('coursemultiselect', 
					array('name' => 'coursemultiselect','hiddenField'=>false,'label'=>false, 'id'=>'coursemultiselect', 'escape'=>false)
			);?><?php echo __('Courses'); ?></th>
            <th><?php echo __('Other options'); ?></th>
          </tr>
          <tr>
            <td><div  id="userview" nowrap="nowrap" style="overflow:auto;max-height:400px;display: block;"><?php foreach($users as $user)  {
              $checked = false;
              if(isset($userid) && $userid) {
                if($userid==$user['User']['id']) $checked = true;
              }
            
            ?>
            <div class="listitem"><?php echo $this->Form->checkbox('userid', 
					array('name' => 'data[Course][user_id][]','value'=>$user['User']['id'],'hiddenField'=>false,'label'=>false, 'class'=>'userlistchkbox', 'escape'=>false, 'checked'=>$checked)
			);echo $this->Html->link($this->Utility->avatar('vvs', ['source'=>$user]), array('controller'=>'users', 'action'=>'view', $user['User']['id'], 'admin'=>false), array('escape'=>false)); ?><?php echo $this->Utility->linkeduname($user); ?></div><?php
  } ?></div></td>
            <td><div id="courseview" style="overflow:auto;max-height:400px;display: block;">
            <?php foreach($courses as $courseid=>$coursename)  {
            
            $checked = false;
            if(@in_array($courseid, $userenrolledcourses)) $checked = true;
            
            ?>
              <div class="listitem"><?php echo $this->Form->checkbox('courseid', 
					array('name' => 'data[Course][course_id][]','value'=>$courseid,'hiddenField'=>false,'label'=>false, 'class'=>'courselistchkbox', 'escape'=>false, 'checked'=>$checked)
			);echo $this->Html->link($coursename, array('action'=>'view', $courseid));?></div>
            <?php } ?>
            </div></td>
            <td id="fieldsview">
            <?php
            if (USESTARTDATE) {
              echo $this->Form->input('Course.startdate', array('type'=>'text','label'=>__('Starting From'), 'class' => 'form-control', 'div' => 'form-group', 'value'=>date('Y-m-d')));
              echo $this->Form->input('Course.duration', array('label'=>__('Course duration'), 'options'=>$months,'div' => 'form-group', 'class' => 'form-control')); //, 'empty'=>'Unlimited'
              echo $this->Form->hidden('Course.enddate', array('label'=>__('Enrolment Ends')));
            }else {  
              echo $this->Form->hidden('Course.startdate', array('value'=>date('Y-m-d')));
              echo $this->Form->input('Course.enddate', array('type'=>'text', 'label'=>__('Complete By'), 'class' => 'form-control'));
            }
            echo $this->Form->input('Course.leaddays', array('label'=>__('Lead Days'), 'div' => 'form-group', 'class' => 'form-control', 'type'=>'number', 'step'=>1, 'value'=>15));
             
						 echo $this->Form->input('Course.set_reminder', array('type'=>'checkbox', 'div'=>'form-group marginLeft20', 'class' => 'form-control'));

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

            ?>

            <?php echo $this->Form->submit(__('Enroll Users'), array('class'=>"btn btn-primary", 'div'=>false));  ?> <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </td>
          </tr>
        </tbody>
      </table>
      <?php echo $this->Form->end();  ?>
    </div>
  </div>
</div>
<script>
jQuery(function($) {

	var startDate = jQuery('#CoursesStartdate').val();

  $('#CourseStartdate').datetimepicker({pickTime:false, format: 'YYYY-MM-DD'});
  $('#CourseEnddate').datetimepicker({pickTime:false, format: 'YYYY-MM-DD', minDate: startDate});

  $('#usermultiselect').on('click', function()	{
    if($(this).is(':checked'))  {
      //$('.userlistchkbox').parent('div').removeClass('checked');
      
      $('.userlistchkbox').prop('checked', true);
    } else  {
      $('.userlistchkbox').prop('checked', false);
      //$('.userlistchkbox').parent('div').addClass('checked');
    }
	})
  $('#coursemultiselect').on('click', function()	{
    if($(this).is(':checked')){
      //$('.courselistchkbox').parent('div').removeClass('checked');
      $('.courselistchkbox').prop('checked', true);
    }else{
      $('.courselistchkbox').prop('checked', false);
      //$('.courselistchkbox').parent('div').addClass('checked');
    }
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

	//twitter bootstrap script
	//get all users
	$('.nav-tabs a.link_Search_Users').on('show.bs.tab', function(){
		//$('#searchUserForm').hide();
		$.ajax({
			type: "POST",
			url: '<?php echo $this->Html->url(array("action"=>"searchusers", "admin"=>false)); ?>',
			data: {'listall':'listall'},
			success: function(data)	{
				$("#searchusers-results").html(data)
				$("#CourseAssigncourseForm").show(); 
			}
		});
	})

	//get users onchange dropdown
	$('#CourseDepartmentId').on('change', function(){
		//$('#searchUserForm').hide();
		var $dept_id = $("#CourseDepartmentId option:selected" ).val();
		$.ajax({
			type: "POST",
			url: '<?php echo $this->Html->url(array("action"=>"searchusers", "admin"=>false)); ?>',
			data: {'department_id':234},
			success: function(data)	{
				$("#searchusers-results").html(data)
				$("#CourseAssigncourseForm").show(); 
			}
		});
	})
	//hide #CourseAssigncourseForm on every tab change
	$('.nav-tabs a').on('hide.bs.tab', function(){
	$('#CourseAssigncourseForm').hide();
	})

	$('#linkSearchUsers').click(function()	{
			$('#searchUserForm').show();
			$('#CourseAssigncourseForm').hide();
		})

	$('.link-remove-user').on('click', function()	{

		if (!confirm("Are you sure to remove this user??")) { return false; }

		$this = $(this);
		var $id = $(this).attr('rel');
		$.ajax({
			type: "POST",
			url: '<?php echo $this->Html->url(array("action"=>"removeuser")); ?>',
			data: {id : $id},
			success: function(data){
				console.log(data);
				if(data=='deleted')	{
					$this.parent('td').parent('tr').remove();
				}	else	{
					alert( data );
					//$('body').append(data);
					//console.log(data);
				}
			},
			error: function()	{
				alert("failure");
			}
		});
	})

	$("#AssignFormWrapper").on('submit', '#CourseSearchusersForm', function()	{
		$.ajax({
			type: "POST",
			url: '<?php echo $this->Html->url(array("action"=>"searchusers", "admin"=>false)); ?>',
			data: $("#CourseSearchusersForm").serialize(),
			success: function(data){
				$("#searchusers-results").html(data)
				$("#CourseAssigncourseForm").show(); 
			},
			error: function(){
				alert("failure");
			}
		});
	});
//
$('#CoursesEnrollmentSelectToggle').on('click ifClicked', function()	{
		if($(this).is(':checked'))  {
				$('.userlistchkbox').parent('div').removeClass('checked');
				$('.userlistchkbox').prop('checked', false);
			}else{
				$('.userlistchkbox').prop('checked', true);
				$('.userlistchkbox').parent('div').addClass('checked');
			}
	});

});
</script>
