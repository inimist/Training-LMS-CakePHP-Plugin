<p>Hi <?php echo $data['User']['full_name']; ?>,</p>
	
<p>You have been enrolled to course <?php echo $this->Html->link($data['Course']['name'], array('plugin'=>'training', 'controller'=>'courses', 'action'=>'view', $data['Course']['id'], 'full_base' => true, 'admin'=>false)); ?> for Training with following user details:</p>
<table>
<tr>
  <th>First Name:</td>
  <td><?php echo $data['User']['first_name']; ?> &nbsp; </td> 
</tr>
<tr>
  <th>Last Name:</td>
  <td><?php echo $data['User']['last_name']; ?></td>
</tr>
<tr>
  <th>Username:</td>
  <td><?php echo $data['User']['username']; ?></td>
</tr>
<tr>
  <th>Email Address:</td>
  <td><?php echo $data['User']['email_address']; ?></td>
</tr>
</table>

<h3>Consider Course Details as below:</h3>

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
<br>
<br>

