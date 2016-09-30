<div class="courses view">

	<h2>Reattempt Test</h2>
	<p>Course: <?php echo $course['Course']['name']; ?></p>

<div class="col-xs-16 col-md-12">

<p class="warning">Are you absolutely sure to reattempt this test?? All the previous attempts will be removed from your dashboard and you may not be able to review them anymore.</p>

<?php echo $this->Element('Quiz/test_reattempt_fieldset', array('quizid'=>$this->request->data['TestQuizAttempt']['quiz_id'], 'action'=>'testattempt')); ?>
<?php echo $this->Form->hidden('reattempt_yes'); ?>
<?php echo $this->Form->end(__('Yes, Re-attempt Test Now')); ?>

</div></div>