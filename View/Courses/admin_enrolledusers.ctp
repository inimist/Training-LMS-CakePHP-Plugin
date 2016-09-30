<div class="courses view">
<h2><?php echo __('Enrolled Users & Reports'); ?></h2>

<div class="col-xs-16 col-md-12">
	<div class="table-responsive">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td width="20%"><?php echo __('Enrolled Users'); ?></td>
					<th><?php echo $this->Html->link(h($course['Course']['user_count']), array('controller'=>'courses', 'action'=>'enrolledusers', $course['Course']['id']));; ?></th>
				</tr>
			</tbody>
		</table>

		<!-- <h3><?php echo __('Existing Enrollments'); ?></h3> -->

		<table class="table table-bordered col-md-6">
			<tbody>
			<tr>
					<th>#</th>
					<th>User Picture</th>
					<th>User full name</th>
					<th>Enrolled on</th>
					<th>Last access to course</th>
					<th>Action</th>
			</tr>
			<?php
			foreach($course['User'] as $membership)	{
			//debug($membership);
			?>
				<tr>
					<td><input type="checkbox" /></td>
					<td><?php echo $this->Html->link($this->Html->image("/img/avatar.png", array("alt"=>"Picture of Arvind Kumar", "title"=>"Picture of " . $membership['full_name'], "class"=>"userpicture defaultuserpic", "width"=>"35", "height"=>"35")), array('controller'=>'users', 'action'=>'view', $membership['id']), array('escape'=>false)); ?></td>
					<td><?php echo $this->Utility->linkeduname($membership); ?></td>
					<td><?php echo $this->Time->format($membership['CoursesEnrollment']['created']); ?></td>
					<td><?php echo is_null($membership['CoursesEnrollment']['last_access_date']) ? 'Never' : $this->Time->format(__SYSTEM_DATETIME_FORMAT, $membership['CoursesEnrollment']['last_access_date']); ?></td>
					<td><?php echo $this->Html->link(__('Edit'), array('controller'=>'courses', 'action'=>'enrollment', $membership['CoursesEnrollment']['id'])); ?> | <?php echo $this->Html->link(__('Remove'), 'javascript:void(0)', array('class'=>'link-remove-user', 'rel'=>$membership['CoursesEnrollment']['id'])); ?></td>
				</tr>

			<?php
			}

			?>

			</tbody>
		</table>

		<h3><?php echo __('New Enrollments'); ?></h3>
		
		<div id="AssignFormWrapper" class="well course-form pull-left col-md-12">

<p>
		<?php echo $this->Html->link(__('Search'), 'javascript:void(0)', array('id'=>'linkSearchUsers')); 
		?> | <?php echo $this->Html->link(__('List All'), 'javascript:void(0)', array('id'=>'linkListAllUsers'));
		?>
		</p>

		<div id="searchUserForm">

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

		<?php echo $this->Form->end(__('Assign Course')); ?>
	

	</div>

	<div class="clearfix"></div>

</div>
</div>
</div>

<script>
jQuery(function($) {
	$("#CourseAssigncourseForm").hide(); 
	//twitter bootstrap script

	$('#linkListAllUsers').click(function()	{

		$('#searchUserForm').hide();

		$.ajax({
			type: "POST",
			url: '<?php echo $this->Html->url(array("action"=>"searchusers", "admin"=>false, $course["Course"]["id"])); ?>',
			data: {'listall':'listall'},
			success: function(data){
				$("#searchusers-results").html(data)
				$("#CourseAssigncourseForm").show(); 
			},
			error: function(){
				alert("failure");
			}
		});
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
				if(data=='deleted')	{
					$this.parent('td').parent('tr').remove();
				}	else	{
					alert('something went wrong');
					console.log(data);
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
});
</script>