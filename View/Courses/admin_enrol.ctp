<div class="courses view">
<h2><?php echo __('Manage Enrollments'); ?></h2>

<div class="col-xs-16 col-md-12">
	<div class="table-responsive">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td width="20%"><?php echo __('Manage enrollments for'); ?></td>
					<th><?php echo $this->Html->link(__($course['Course']['name']), array('controller'=>'courses', 'action'=>'view', $course['Course']['id']));; ?></th>
				</tr>
			</tbody>
		</table>
		<?php $this->Utility->pagination_form('CoursesEnrollment'); ?>
		<?php echo $this->Form->create('CoursesEnrollment', array(
					'url'=>array('controller'=>'courses', 'action'=>'removeuser', $course['Course']['id'], 'admin'=>true, 'plugin'=>'training'),
					'inputDefaults' => array(
						'div' => 'form-group',
						'wrapInput' => false,
						'class' => 'form-control'
					),
					'class' => 'well quiz-form'
				)); ?>
			<?php //echo $this->Form->hidden('CoursesEnrollment.selected_ids');  ?>
			<?php echo $this->Form->button(__('Delete Selected'), array('type'=>"submit", 'div'=>false, 'class'=>"btn btn-default marginBottom10", 'id'=>'deleteSelected')); ?>
			<?php echo $this->Form->button(__('Hide Future Enrollments'), array('div'=>false, 'class'=>"btn btn-default marginBottom10", 'id'=>'hideFutureEnrollments')); ?>
			<?php echo $this->Form->button(__('Hide Past Enrollments'), array('div'=>false, 'class'=>"btn btn-default marginBottom10", 'id'=>'hidePastEnrollments')); ?>
			<?php echo $this->Form->button(__('Export Quizzes'), array('type'=>"submit", 'div'=>false, 'class'=>"btn btn-default marginBottom10 pull-right", 'id'=>'exportQuizzes')); ?>
		<!-- <h3><?php echo __('Existing Enrollments'); ?></h3> -->

		<table class="table table-bordered col-md-6">
			<tbody>
			<tr>
					<th><input type="checkbox" id ="CoursesEnrollmentSelectToggle"></th> 
					<th>Action</th>
					<th>User</th>
					<th>Start on</th>
					<th>Complete By</th>
					<th>Completed</th>
					
			</tr>
			<?php
			$i = 0;
			$this->Training->setQuizSettings($_quizSettings);
			foreach($courses_enrollments as $user)	{
				//debug( $user['CoursesEnrollment']);
				$class ="";
				if($user['CoursesEnrollment']['startdate'] > date('Y-m-d')) $class ="future-enrollment";
				if($user['CoursesEnrollment']['enddate'] < date('Y-m-d') && $user['CoursesEnrollment']['result'] == 'pass') $class ="past-enrollment";
			?>
				<tr class = "<?php echo $class; ?>">
				<td><?php echo $this->Form->input('CoursesEnrollment.'.$i.'.id', array('type'=>'checkbox', 'label'=>false, 'value'=>$user['CoursesEnrollment']['id'], 'class'=>'select-checkbox-enrol', 'hiddenField'=>false)); $i++; ?></td>

					<td><?php echo $this->Html->link(__('Edit'), array('controller'=>'courses', 'action'=>'enrollment', $user['CoursesEnrollment']['id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?> | <?php 

						$rem_options = array('class'=>'link-remove-user glyphicon glyphicon-remove', 'rel'=>$user['CoursesEnrollment']['id']);
						if($user['CoursesEnrollment']['completed_date']) $rem_options['disabled'] = 'disabled';

						echo $this->Html->link(__('Remove'), 'javascript:void(0)', $rem_options); ?></td>
					<td><?php echo $this->Html->link($this->Utility->avatar30(array('source'=>$user['User'])), array('controller'=>'users', 'action'=>'view', $user['User']['id']), array('escape'=>false)); ?> <?php echo $this->Utility->linkeduname($user); ?></td>
					
					<td><?php //if(USESTARTDATE) { echo $this->Time->format($user['CoursesEnrollment']['startdate']); } else {  echo $this->Time->format($user['CoursesEnrollment']['created']);  }
					echo $this->Time->format($user['CoursesEnrollment']['startdate']); ?></td>
					<td><?php echo $this->Time->format($user['CoursesEnrollment']['enddate']); ?></td>
					<td><?php 
					if(isset($user['CoursesEnrollment']['QuizAttempt']) && $user['CoursesEnrollment']['QuizAttempt']['state'] == 'finished') { 
							echo $this->Time->format($user['CoursesEnrollment']['completed_date']); ?> &nbsp; <?php  
							echo $this->Training->createresultlink( $user['CoursesEnrollment'], $course['Course']['id'] ); 
							}
					if($user['CoursesEnrollment']['completed_date'] && $user['CoursesEnrollment']['QuizAttempt']['state'] == 'inprogress') echo 'Re-attempt Inprogress'; ?></td>

				</tr>

			<?php
			}
			
			?>

			</tbody>
		</table>
	<?php echo $this->Form->end();  ?>
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

		<h3><?php echo __('New Enrollments'); ?></h3>
		
		<div id="AssignFormWrapper" class="well course-form pull-left col-md-12">


  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home" id="linkSearchUsers">Search</a></li>
    <li><a data-toggle="tab" href="#menu1" id="linkListAllDepartments">List All Departments</a></li>
    <li><a data-toggle="tab" href="#menu2" class="link_Search_Users" id="linkSearchUsers">List All Users</a></li>
  </ul>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
		<p>&nbsp;</p>
				 <?php echo $this->Form->create('Course', array(
				'url'=>array('action'=>'searchusers', "admin"=>false, $course['Course']['id']),
				'action'=>'searchusers',
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => false,
					'class' => 'form-control'
				),
				//'class' => 'well course-form',
				'onsubmit'=>'return false'
				)); ?>

				<?php echo $this->Form->input('s', array('label'=>__('Look for users'), 'placeholder'=>__('Search by first/last name '), 'div'=>'inline')); ?>
				<?php echo $this->Form->end(__('Search Users')); ?>
				<br />
    </div>
    <div id="menu1" class="tab-pane fade">
		<p>&nbsp;</p>
				<?php echo $this->Form->input('Course.department_id', array('label'=>__('List/Select by department'), 'div'=>'inline', 'empty'=>'Select department')); ?>
    </div>
    <div id="menu2" class="tab-pane fade">
      <p>&nbsp;</p>
    </div>
 
  </div>



	<?php
		echo $this->Form->create('Course', array(
		'url'=>array('action'=>'assigncourse', $course['Course']['id']),
		'action'=>'assigncourse',
		'inputDefaults' => array(
			'div' => 'starthidden',
			'wrapInput' => false,
			'class' => 'col-md-4'
		)
	));

	echo $this->Form->hidden('course_id', array('value'=>$course['Course']['id']));

	?>
		<div id="searchusers-results" class="pad20 bgwhite"></div>

	</div>

	<div class="clearfix"></div>

</div>
</div>
</div>
<script>
jQuery(function($) {
	$("#CourseAssigncourseForm").hide(); 
	//twitter bootstrap script
	//get all users
	$('.nav-tabs a.link_Search_Users').on('show.bs.tab', function(){
		//$('#searchUserForm').hide();
		$.ajax({
			type: "POST",
			url: '<?php echo $this->Html->url(array("action"=>"searchusers", "admin"=>false, $course["Course"]["id"])); ?>',
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
		console.log($dept_id);
		$.ajax({
			type: "POST",
			url: '<?php echo $this->Html->url(array("action"=>"searchusers", "admin"=>false, $course["Course"]["id"])); ?>',
			data: {'department_id':$dept_id},
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
			url: '<?php echo $this->Html->url(array("action"=>"removeuser", $course["Course"]["id"])); ?>',
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
			url: '<?php echo $this->Html->url(array("action"=>"searchusers", "admin"=>false, $course["Course"]["id"])); ?>',
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
	console.log('clicked');
		if($(this).is(':checked')){
				$('.select-checkbox-enrol').parent('div').removeClass('checked');
				$('.select-checkbox-enrol').prop('checked', false);
			}else{
				$('.select-checkbox-enrol').prop('checked', true);
				$('.select-checkbox-enrol').parent('div').addClass('checked');
			}
	});

$('#exportQuizzes').on('click', function(evt){
		evt.preventDefault();
		var url = "<?php echo $this->Html->url(array('controller'=>'courses', 'action'=>'exportquizzes', $course['Course']['id'], 'admin'=>true, 'plugin'=>'training')); ?>"; 
		$('#CoursesEnrollmentAdminEnrolForm').prop('action', url);
		$('#CoursesEnrollmentAdminEnrolForm').submit();
	});

	$('#hideFutureEnrollments').on('click', function(evt){ 
		evt.preventDefault(); 
		$('tr.future-enrollment').toggle();
		var btntext = $(this).html();
		if(btntext == 'Hide Future Enrollments') $(this).html('Show Future Enrollments');
		if(btntext == 'Show Future Enrollments') $(this).html('Hide Future Enrollments');

	});
	
	$('#hidePastEnrollments').on('click', function(evt){ 
		evt.preventDefault(); 
		$('tr.past-enrollment').toggle();
		var btntext = $(this).html();
		if(btntext == 'Hide Past Enrollments') $(this).html('Show Past Enrollments');
		if(btntext == 'Show Past Enrollments') $(this).html('Hide Past Enrollments');

	});

});
</script>
