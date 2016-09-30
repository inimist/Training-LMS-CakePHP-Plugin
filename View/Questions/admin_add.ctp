<div class="questions add form">
<p>&nbsp; </p>
 <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home" id="linkSearchUsers">Add New</a></li>
    <li><a data-toggle="tab" href="#menu1" id="linkListAllDepartments">Copy from other courses</a></li>
    <!-- <li><a data-toggle="tab" href="#menu2" class="link_Search_Users" id="linkSearchUsers">List All Users</a></li> -->
  </ul>

<div class="tab-content">
    <div id="home" class="tab-pane fade in active">
			<?php echo $this->Form->create('Question', array(
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => false,
					'class' => 'form-control'
				),
				'class' => 'well question-form'
			)); ?>
			<fieldset>
			<?php echo $this->Element('Question/question_fieldset'); ?>
			<?php echo $this->Form->submit(__(' Next >> ')); ?>
			</fieldset>
			<?php echo $this->Form->end(); ?>
		</div>
		<div id="menu1" class="tab-pane fade">
		 <?php echo $this->Form->create('Question', array('url'=>array('controller'=>'questions', 'action'=>'copy_questions', 'admin'=>true, 'plugin'=>'training'),
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => false,
					'class' => 'form-control'
				),
				'class' => 'well question-form'
				)); ?>
			<fieldset>
			<?php echo $this->Form->input('course_id', array('label'=>'Select Course/Question Bank', 'style'=>'max-width:50%;', 'id'=>'selectedCourse')); ?>
			<?php //echo $this->Form->hidden('question_ids', array('value'=>'')); ?>
			<div id="showQuestions">  </div>

			<?php echo $this->Form->submit(__('Copy Questions')); ?>
			</fieldset>
			<?php echo $this->Form->end(); ?>

		</div>
   </div> <!-- tabs content end -->
</div>
<!-- <?php echo $this->Element('menu'); ?> -->
<script>
jQuery( function($){

	// Get Department according to User
	function getCourseQuestions(){
			var course_id = $('#selectedCourse').val();
				console.log(course_id);
			$.ajax({
				type: "POST",
				url: '<?php echo $this->Html->url(array("controller"=>"questions", "action"=>"get_questions", "admin"=>true, "plugin"=>"training")); ?>',
				data: {'course_id': course_id },
				success: function(data)	{
					$("#showQuestions").html(data);
				}
			});
		}

		getCourseQuestions();

	$('#selectedCourse').on('change', function(){
			getCourseQuestions();
		console.log('value changed');
		});

})
</script>