<div class="courses view">
<h2>Training Course: <?php echo h($course['Course']['name']); ?></h2>

<?php //debug($course); ?>

<p><i>Tutorial by: <?php echo $this->Utility->linkeduname($course['Creator']); ?></i></p>

	<table class="table table-striped" cellpadding="0" cellspacing="0">
		<tbody>
			<tr style="height:0;display:none;">
					<th style="height:0;display:none;"></th>
			</tr>
			<tr>
				<td><?php echo $course['Course']['description']; ?></td>
			</tr>
			<tr>
					<th><?php echo __('Source') . ' ' .  $course['Course']['source_type']; ?></th>
			</tr>
			<tr>
				<td><?php
				echo $this->Training->getEmbeded( $course );
				echo $this->Training->getUploads( $course );
				//var_dump(is_null(trim($course['Course']['source_file_embeded'])));
				?>
				</td>
			</tr>
			<tr>
					<th><?php echo __('Views'); ?></th>
			</tr>
			<tr>
				<td><?php
				echo $course['Course']['views']; ?></td>
			</tr>
			<tr>
				<td><p class="text-center"><a href="<?php echo $this->Html->url(array('action'=>'view', $course['Course']['id'], 'admin'=>false, '?'=>array('enid'=>$course['CoursesEnrollment']['id']))); ?>">Go to Course</a></p></td>
			</tr>
		</tbody>
	</table>

	
</div>
