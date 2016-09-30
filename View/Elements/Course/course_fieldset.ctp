	<?php
  //prepare some data here
  $action = $this->request->params['action'];

	//debug($this->request->data);

	//$errors = $this->Form->validationErrors;

	//pr($errors ); die;
  ?>
  



  <fieldset>
		<legend><?php echo sprintf(__('%s Course/Module'), ucfirst($action)); ?></legend>

  <?php
    echo $this->Form->input('id');
		echo $this->Form->input('creator_id', array('type'=>'hidden'));
		echo $this->Form->input('uniqueid', array('label'=>__('UniqueID'), 'value'=>$this->Form->value('id'), 'readonly', 'placeholder'=>__('Automatically Generated')));
	?>

	<?php
		echo $this->Form->input('name', array('label'=>__('Title of Training course/module')));
	?>

  <?php
		echo $this->Form->input('startdate', array('type'=>'text','label'=>__('Start Date')));
	?>

	<?php
		//echo $this->Form->input('due_date', array('type'=>'text', 'label'=>__('Due Date')));
	?>

 <?php
		echo $this->Form->input('description', array('label'=>__('Description')));
	?>

  <?php
		echo $this->Form->input('signature', array('label'=>__('Does this module require a signature?'), 'div' => array('class' => 'form-group dtdd')));
	?>

  <?php
		echo $this->Form->input('repeats', array('label'=>__('Repeat?'), 'div' => array('class' => 'form-group dtdd checkbox-perline')));
	?>

  <?php
    echo '<div id="cnr-options" style="display:none;">'; //changing to NON-Repeating options
		echo $this->Form->input('frequency', array('label'=>__('Repeat every n months')));
    echo '</div>';
	?>

  <?php
		echo $this->Form->input('source_type', array('label'=>__('Learning Source (File)') . ' <i class="glyphicon glyphicon-question-sign" rel="tooltip" title="' . __('Select source of training module') . '"></i>', 'options'=>$source_types, 'empty'=>'--Select--', 'before'=>'', 'after'=>'<div id="SourceType-error" class="starthidden error">Please select a source type</div>'));
    //echo '<div id="SourceType-error" class="error">Please select a source type</div>';
	?>

  <div id="SourceFileArea" class="starthidden well bgwhite">

  <?php
		//echo '<p>' . $this->Html->link(__('Embed Code'), 'javascript:void(0)', array('id'=>'linkEmbedCode')). '</p>';

		echo '<div class="divEmbedCode">';
		
			echo '<div class="divEmbedCodeItems starthidden">';

				echo '<p><li class="marginLeft20"><a href="javascript:void(0)" id="linkLoadMyItems">Select from a list of existing uploads</a></li></p>';

				echo '<div class="divMyItems starthidden">loading..</div>';

			echo '</div>';

		echo $this->Form->input('source_file_embeded', array('label'=>__('Enter embed code here') . ' <i class="glyphicon glyphicon-question-sign" rel="tooltip" title="' . __('To get embed code you must upload a Video to Youtube and get embed code. Similarily you can get an embed code for a PowerPoint file by uploading to Microsof LIVE. You can get embed code for existing Videos or PPTs from there respective websites') . '"></i>'));
		
	echo '</div>';



		/*
	?>

  <p> -OR- </p>

  <?php
		echo '<p>' . $this->Html->link(__('Upload file'), 'javascript:void(0)', array('id'=>'linkUploadFile')). '</p>';
		echo $this->Form->input('source_file', array('type'=>'file', 'label'=>__('Upload source') . ' <i class="glyphicon glyphicon-question-sign" rel="tooltip" title="' . __('Upload a Video or PowerPoint file here and we will upload it to YoutTube or Microsoft LIVE, based on your selection above and get embed code for you') . '"></i>', 'div'=>'form-group divUploadFile starthidden'));
	*/?>

  </div>

	<div  id="UploadFileArea" class="starthidden">
		<?php 
		//echo '<p>' . $this->Html->link(__('Upload file'), 'javascript:void(0)', array('id'=>'linkUploadFile')). '</p>';
		echo $this->Form->input('Course.attachments', array('type'=>'file', 'label'=>__('Upload source') . ' <i class="glyphicon glyphicon-question-sign" rel="tooltip" title="' . __('Upload a Video or PowerPoint file here and we will upload it to YoutTube or Microsoft LIVE, based on your selection above and get embed code for you') . '"></i>', 'div'=>'form-group divUploadFile', 'class'=>'btn btn-default'));
			if(@sizeof($this->request->data['Upload'])>0): ?>
			<table id="attachements" class="table table-striped pad10 info" style="width:50%;">
				<tr><th colspan="2">Attached Files:</th></tr>
				<?php
				foreach($this->request->data['Upload'] as $attachment):
					echo '<tr>';
						echo '<td>' . $attachment['filename'] . '( ' . $attachment['type'] . ' file) ' . '</td>';
						echo '<td>' . $this->Html->link(__('Remove'),	array('action' => 'delupload', $attachment['id']), array('confirm'=>__('Are you sure you remove this attachement? This cannot be undone!!'))) . '</td>'; //, __('Are you sure you remove this attachement? This cannot be undone!!', $attachment['id'])
					echo '</tr>';
				endforeach;
				?>
			</table>
		<?php endif; 
		//echo $this->Form->input('Course.uploads', array('type'=>'file', 'label'=>__('Upload Document') . ' <i class="glyphicon glyphicon-question-sign" rel="tooltip" title="' . __('Upload a Microsoft Word or PDF file here') . '"></i>', 'div'=>'form-group'));
		?>
  </div>

<?php $this->TinyMCE->editor(array(
        'theme' => 'modern', 
        'skin'=>'lightgray', 
        'mode' => "exact", 
        'elements' => 'CourseDescription',
        'plugins' => 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste'
        )); ?>

<?php if(!defined('SITE_DIR')) echo $this->Form->input('master_course', array('type'=>'checkbox', 'label'=>__('Master Course?'))); ?>



<script>
jQuery(function($)	{


	$('a#linkEmbedCode').on('click', function()	{
		$('.divEmbedCode').toggle();
		$('.divUploadFile').hide();
	})

	$('a#linkUploadFile').on('click', function()	{
		$('.divUploadFile').toggle();
		$('.divEmbedCode').hide();
	})

  if($('#CourseSourceType').val() != "") {
		actionSourceTypeFilled();
		if($('#CourseSourceType').val()=='Document')	{
			$('#SourceFileArea').hide();
			$('#UploadFileArea').show();
		}
  }

	if($('#CourseSourceType').val()=='Video')	{
		//$('.divEmbedCodeItems').show();
	}

  function actionSourceTypeFilled() {
		$('#UploadFileArea').hide();
    $('#SourceType-error').hide();
    $('#SourceFileArea').show();
		$('.divEmbedCode').show();
  }

  $('.course-form').on('submit', function()	{
		if($('#CourseSourceType').val()=="")  {
      $('#SourceType-error').show();
      return false;
    }
	})

  $('#CourseSourceType').on('change', function()	{
		//log($(this).val());
    if($(this).val()=="Video" || $(this).val()=="Powerpoint") {
      actionSourceTypeFilled();
    }	else if($(this).val()=="Document")	{
			$('#UploadFileArea').show();
			$('#SourceFileArea').hide();
		}
  })
	$('#CourseRepeats').on('click ifClicked', function()	{
		window.setTimeout( function () {
			if($('#CourseRepeats').is(':checked')) {
				console.log("Checked");
				$('div#cnr-options').show();
			} else  {
				$('div#cnr-options').hide();
				console.log("NotChecked");
			}
		}, 100);
	})

	//console.log($('#ControlsAssigneeSetPostSpecificDate').is(':checked'));
	if($('#CourseRepeats').is(':checked'))	{
		$('div#cnr-options').show();
	}

		$('#linkLoadMyItems').on('click', function()	{
			$.ajax({
				url: '<?php echo $this->Html->url(array("action"=>"listvideos")); ?>',
				beforeSend: function( xhr ) {
					//xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
					$('.divMyItems').show()
				}
			})
			.done(function( data ) {
				$('.divMyItems').html( data )
			});
	})

	$('.divEmbedCode').on('click', '.uploadItem a', function()	{
		//console.log($(this).attr('id'));
		var embedcode = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'+ $(this).attr('id') +'" frameborder="0" allowfullscreen></iframe>';
		$('#CourseSourceFileEmbeded').val(embedcode);
	})

  $("[rel=tooltip]").tooltip({ placement: 'right'});

	$('#CourseStartdate').datetimepicker({pickTime:false, format: 'MM/DD/YYYY'});

	//$(CourseSourceType)
})
</script>

</fieldset>