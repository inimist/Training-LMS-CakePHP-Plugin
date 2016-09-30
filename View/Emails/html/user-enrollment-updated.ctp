<p>Hi <?php echo $data['User']['full_name']; ?></p>
<table>
<tr>
  <th>Username:</td>
  <td><?php echo $data['User']['username']; ?></td>
</tr>
<tr>
  <th>Email Address:</td>
  <td><?php echo $data['User']['email_address']; ?></td>
</tr>
</table>
	
<p>Your Enrollment details has been updated as below: </p>

<table>
<tr>
  <th>Course name:</td>
  <td><?php echo $data['Course']['name']; ?> &nbsp; <?php echo $this->Html->link('Learn Now', array('full_base' => true, 'plugin'=>'training', 'controller'=>'courses', 'action' => 'learn', $data['Course']['id'], 'admin'=>false)); ?></td> 
</tr>
<tr>
  <th>Course Description:</td>
  <td><?php echo $data['Course']['description']; ?></td>
</tr>
<tr>
  <th>Start Date:</td>
  <td><?php echo $this->Time->format('D d, M Y', $data['CoursesEnrollment']['startdate']); ?></td>
</tr>
<tr>
  <th>End Date:</td>
  <td><?php echo $this->Time->format('D d, M Y', $data['CoursesEnrollment']['enddate']); ?></td>
</tr>
<?php if($data['Course']['repeats']) { ?>
<tr>
  <th>Repeat after every:</td>
  <td><?php echo $data['Course']['frequency']; ?> Months</td>
</tr>
<?php } ?>
<tr>
  <th>Signature Required:</td>
  <td><?php echo $data['Course']['signature']? "Yes" : "No"; ?></td>
</tr>
<tr>
  <th>Source Type:</td>
  <td><?php echo $data['Course']['source_type']=='Document' ? __('Downloadable') : __('On-Line'); ?>&nbsp; </td>
</tr>
</table>

Thanks,<br>
The Controlist.
