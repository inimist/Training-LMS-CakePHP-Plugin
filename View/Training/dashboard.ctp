<div class="training dashboard index">

	<h2><?php echo __('Training Dashboard'); ?></h2>
  
  <p>&nbsp;</p>
	
  <div class="row">
    <div class="col-xs-6 col-md-3">
      <a href="<?php echo $this->Html->url(array('controller'=>'courses', 'action'=>'index', 'admin'=>true, 'plugin'=>'training')) ;?>" class="thumbnail">
        <?php echo $this->Html->image('/training/img/icon_courses.png', array('width'=>60, 'height'=>60)) ;?>
      </a>
    </div><!-- 
    <div class="col-xs-6 col-md-3">
      <a href="<?php echo $this->Html->url(array('controller'=>'courses', 'action'=>'index','admin'=>true,  'plugin'=>'training')) ;?>" class="thumbnail">
        <?php echo $this->Html->image('/training/img/icon_questions.png', array('width'=>60, 'height'=>60)) ;?>
      </a>
    </div>
    <div class="col-xs-6 col-md-3">
      <a href="<?php echo $this->Html->url(array('controller'=>'courses','admin'=>true,  'action'=>'index', 'plugin'=>'training')) ;?>" class="thumbnail">
        <?php echo $this->Html->image('/training/img/icon_quizzes.png', array('width'=>60, 'height'=>60)) ;?>
      </a>
    </div> -->
  </div>
</div>
