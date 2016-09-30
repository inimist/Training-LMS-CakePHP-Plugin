<?php echo $this->Html->link($this->Session->read('Auth.User.full_name'), array('full_base' => true, 'controller'=>'users', 'action'=>'view', $this->Session->read('Auth.User.id'), 'plugin'=>false)); ?> has requested unlocking of quiz attempt  <?php echo $this->Html->link('#'.$quizAttempt['id'], array('full_base' => true, 'controller'=>'quizzes', 'action'=>'review', $quizAttempt['id'], 'plugin'=>'training', '?'=>array('course_id'=>$course['Course']['id']))); ?> of course <?php echo $this->Html->link($course['Course']['name'], array('full_base' => true, 'controller'=>'courses', 'action'=>'view', $course['Course']['id'], 'plugin'=>'training', 'admin'=>true)); ?>

<p> User's Detail as below: </p>
<table>
<tr>
  <th>First Name:</td>
  <td><?php echo $this->Session->read('Auth.User.first_name'); ?> &nbsp; </td> 
</tr>
<tr>
  <th>Last Name:</td>
  <td><?php echo $this->Session->read('Auth.User.last_name'); ?></td>
</tr>
<tr>
  <th>Username:</td>
  <td><?php echo $this->Session->read('Auth.User.username'); ?></td>
</tr>
<tr>
  <th>Email Address:</td>
  <td><?php echo $this->Session->read('Auth.User.email_address'); ?></td>
</tr>
</table>