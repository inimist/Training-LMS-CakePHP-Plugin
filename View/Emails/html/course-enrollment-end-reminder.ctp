Hi <?php echo $enrollment['User']['full_name']; ?>,

<p>Your enrollment to the course ' <?php echo $this->Html->link($enrollment['Course']['name'], array('full_base' => true, 'plugin'=>'training', 'controller'=>'courses', 'action' => 'view', $enrollment['Course']['id'], 'admin'=>false)); ?> has last day to complete Today!!</p>
<p> User's Detail as below: </p>
<table>
<tr>
  <th>First Name:</td>
  <td><?php echo $enrollment['User']['first_name']; ?> &nbsp; </td> 
</tr>
<tr>
  <th>Last Name:</td>
  <td><?php echo $enrollment['User']['last_name']; ?></td>
</tr>
<tr>
  <th>Username:</td>
  <td><?php echo $enrollment['User']['username']; ?></td>
</tr>
<tr>
  <th>Email Address:</td>
  <td><?php echo $enrollment['User']['email_address']; ?></td>
</tr>
</table>

<p>Consider Course Details as below:</p>

<table>
<tr>
  <th>Course name:</td>
  <td><?php echo $enrollment['Course']['name']; ?> &nbsp; <?php echo $this->Html->link('Learn Now', array('full_base' => true, 'plugin'=>'training', 'controller'=>'courses', 'action' => 'learn', $enrollment['Course']['id'], 'admin'=>false)); ?></td> 
</tr>
<tr>
  <th>Course Description:</td>
  <td><?php echo $enrollment['Course']['description']; ?></td>
</tr>
<tr>
  <th>Start Date:</td>
  <td><?php echo $this->Time->format('D d, M Y', $enrollment['CoursesEnrollment']['startdate']); ?></td>
</tr>
<tr>
  <th>End Date:</td>
  <td><?php echo $this->Time->format('D d, M Y', $enrollment['CoursesEnrollment']['enddate']); ?></td>
</tr>
<?php if($enrollment['Course']['repeats']) { ?>
<tr>
  <th>Repeat after every:</td>
  <td><?php echo $enrollment['Course']['frequency']; ?> Months</td>
</tr>
<?php } ?>
<tr>
  <th>Signature Required:</td>
  <td><?php echo $enrollment['Course']['signature']? "Yes" : "No"; ?></td>
</tr>
<tr>
  <th>Source Type:</td>
  <td><?php echo $enrollment['Course']['source_type']=='Document' ? __('Downloadable') : __('On-Line'); ?>&nbsp; </td>
</tr>
<tr>
  <th></td>
  <td>(This is a Reminder for Course Enrollment!)</td>
</tr>
</table>