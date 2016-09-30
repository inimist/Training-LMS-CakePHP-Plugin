<div class="quizzes add form">
<?php echo $this->Form->create('Quiz', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well quiz-form'
)); ?>
<fieldset>
<p class="pull-right"><?php echo $this->Html->link(__('Edit Quiz Settings'), array('action' => 'settings', $this->request->data['Quiz']['id'])); ?></p>
<?php echo $this->Element( 'Quiz/quiz_fieldset' ); ?>

<?php if($attempts)	{ ?>
	<p class="warning">You cannot add or remove questions because this quiz has been attempted. (Attempts: <?php echo $attempts; ?>)</p>
<?php } ?>

<?php echo $this->Element('Quiz/quiz_questions'); ?>

<?php 
if($attempts) echo $this->Form->hidden('attempts', array('value'=>$attempts));
else echo $this->Form->hidden('attempts', array('value'=> 0)); ?>
<?php echo $this->Form->submit(__(' Save Changes')); ?>
</fieldset>
<?php echo $this->Form->end(); ?>
</div>