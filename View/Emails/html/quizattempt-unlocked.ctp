<p>Dear <?php echo $quizAttempt['User']['full_name']; ?>,</p>

<p>As per your request, your quiz attempt <?php echo $this->Html->link('#'.$quizAttempt['QuizAttempt']['id'], array('full_base' => true, 'admin'=>false, 'controller'=>'quizzes', 'action'=>'view', $quizAttempt['QuizAttempt']['id'], 'plugin'=>'training', '?'=>array('course_id'=>$course['Course']['id']))); ?> of course <?php echo $this->Html->link($course['Course']['name'], array('full_base' => true, 'controller'=>'courses', 'action'=>'view', $course['Course']['id'], 'plugin'=>'training', 'admin'=>false)); ?> has been unlocked.</p>
<p> User's Detail as below: </p>
<table>
<tr>
  <th>First Name:</td>
  <td><?php echo $quizAttempt['User']['first_name']; ?> &nbsp; </td> 
</tr>
<tr>
  <th>Last Name:</td>
  <td><?php echo $quizAttempt['User']['last_name']; ?></td>
</tr>
<tr>
  <th>Username:</td>
  <td><?php echo $quizAttempt['User']['username']; ?></td>
</tr>
<tr>
  <th>Email Address:</td>
  <td><?php echo $quizAttempt['User']['email_address']; ?></td>
</tr>
</table>