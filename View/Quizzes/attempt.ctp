<div class="courses view">
<h2><?php echo __('Take Test'); ?>  for course "<?php echo __($course['Course']['name']); ?>"</h2>

<div class="col-xs-16 col-md-12">

<?php 
$matchpairtype = false;
//debug($quizAttempt);
if(isset($continue_last_attempt)):
	echo $this->Element('Quiz/continue_attempt');
else:

echo $this->Form->create('QuizAttempt', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well course-form'
)); ?>
<?php echo $this->Form->hidden('id'); ?>
<?php echo $this->Form->hidden('quiz_id'); ?>
<?php echo $this->Form->hidden('user_id'); ?>


<?php

if(isset($quizAttempt)): ?>
<?php echo $this->Form->hidden('courses_enrollment_id', array('value'=>$quizAttempt['QuizAttempt']['courses_enrollment_id'])); ?>
<?php 
//debug($quizAttempt);
//debug($questions);
if($questions){
	$i=0;
	foreach($questions as $question){
		if($question['QuestionType']['qtype'] == 'matching-pairs'){
			$matchpairtype = true;
			$mpq_id = $question['Question']['id'] ;
			$mpq_response = json_encode(unserialize(@$this->request->data['QuestionAttempt'][$mpq_id]['responsesummary']));
			}
		$this->Training->renderQuestion($question, $i++);
	}
}
?>
<?php echo $this->Form->hidden('currentpage'); ?>
<?php echo $this->Form->hidden('attempt'); ?>
<?php echo $this->Form->end(__('Next >>'), array('id'=>'questionAttemptSubmit')); ?>
<?php
else: ?>

<p>You are about to start a test "<strong><?php echo $quiz['Quiz']['name'] ?></strong>" for training course <?php echo $this->Html->link($course['Course']['name'], array('action' => 'view', $course['Course']['id'])); ?>. </p>

<p>Click the "<?php echo __('Start Test') ?>" button to start this test.</p>

<?php echo $this->Form->hidden('currentpage', array('value'=>0)); ?>
<?php echo $this->Form->end(__('Start Test')); ?>

<?php 

endif;

endif; ?>

</div></div>
<?php 
//debug($matchpairtype);
if($matchpairtype){  ?>
<script>
jQuery(function($){
	var parentOffset = $('#matchingQuestion').offset();
	var matchPairs = [];

	$('.leftpair').on('click', function(e){
				obj1 = $(this);
				obj1.addClass('pre-match');
				if((obj1).hasClass('leftpair-selected')) return unpair(obj1.attr('id')); //if its alread selected then return.

			$('.rightpair').on('click', function(e1){
					if(!obj1) return alert('Select one option from left side');
					if((obj1).hasClass('leftpair-selected')) return;
					if(!(obj1).hasClass('pre-match')) return;
					obj1.addClass('leftpair-selected');
					var obj2 = $(this);
				if((obj2).hasClass('rightpair-selected')) return;
					obj2.addClass('rightpair-selected');
					drawline(obj1, obj2);
					matchPairs[obj1.attr('id')] = obj2.attr('id');
					obj1.removeClass('pre-match');
					console.log(matchPairs);
					//e1.preventDefault();
					//return;
				})
		e.preventDefault();
		return;
	})

	function drawline(obj1, obj2){
				var width1 = obj1.width();
					var height1 = obj1.height();
					var offset1 = obj1.offset();
					var y1 = Number(offset1.left) + Number(width1) ;
					var x1 = Number(offset1.top) + Number(height1/2) ;
					//console.log(offset1);
					//console.log(x1);
					//console.log(y1);
					var height2 = obj2.height();
					var offset2 = obj2.offset();
					var y2 = Number(offset2.left);
					var x2 = Number(offset2.top) + Number(height2/2);
					console.log(offset2);
					var length = Math.sqrt((x1-x2)*(x1-x2) + (y1-y2)*(y1-y2));
					var angle  = Math.atan2(y2 - y1, x2 - x1) * 180 / Math.PI ;
					//console.log(angle);
					angle = -(Number(angle) - 90);
					var transform = 'rotate('+angle.toFixed(2)+'deg)';
					var lineHtml ='';
					var actualLeft = y1.toFixed(2) - offset1.left + 25; 
					var actualTop = x1.toFixed(2) - parentOffset.top;
					lineHtml = "<span class='line' style='left:" + actualLeft + "px; top:" + actualTop + "px; width:" + length.toFixed(2) + "px; transform:" + transform + ";'></span>";
					console.log(lineHtml);
					obj1.after(lineHtml);
				return;
		}

	function unpair(qId){
		console.log('unpair');
		var pair = $('#'+qId);
		pair.next("span").remove();
		pair.removeClass('leftpair-selected');
		pair.removeClass('pre-match');
		var ansId = matchPairs[qId];
		//console.log(ansId);
		$('#'+ansId).removeClass('rightpair-selected');
		delete matchPairs[qId];
		//console.log(matchPairs);
		}

	$(window).resize(function(evt){
		console.log('window resized');
		if(!matchPairs) return;
		//redraw each line
			for (var qId in matchPairs) {
			//console.log(qId);
			//console.log(matchPairs[qId]);
			var ansId = matchPairs[qId]
			var qObj = $('#'+qId);
			var ansObj = $('#'+ ansId);
			qObj.next("span").remove();
			drawline(qObj, ansObj);
		}
		evt.preventDefault();
		return;
	});

//Save Pair matches before save.
$('input[value="Next >>"]').on('click', function(e){
	e.preventDefault();
	if(matchPairs){
				for (var qId in matchPairs) {
				var pairanswer = $('#'+ qId).siblings('.'+ qId + '-answer');
				var ansId = matchPairs[qId];
				var rightpair = $('#'+ ansId).html();
				pairanswer.val(rightpair);
				}
			}
	$('#QuizAttemptAttemptForm').submit()
		}); 

//show previous response if exists
if($('#matchingPairResponse').val() != null){
	var matchPairs = new Object();
	var response = '<?php  echo $mpq_response; ?>';
	response = JSON.parse(response)
	console.log(response);
	for (var pair in response) {
		var responsePairs = response[pair];
			console.log(responsePairs);
			var left = responsePairs[0];
			var right = responsePairs[1];
			if(left != '' && right != ''){
				var qObj = new Object();
				var ansObj = new Object();
				$('p.leftpair').each(function(){
					if($(this).html() == left){
						$(this).addClass('leftpair-selected');
						qObj = $(this);
						//$('#'+ qObj.attr('id')).addClass('leftpair-selected');
					}
				});
				$('p.rightpair').each(function(){
					if($(this).html() == right){
					  $(this).addClass('rightpair-selected');
						ansObj = $(this);
						//$('#'+ ansObj.attr('id')).addClass('leftpair-selected');
					}
				});
				//var qObj = $('#'+qId);
				//var ansObj = $('#'+ ansId);
				drawline(qObj, ansObj);
				matchPairs[qObj.attr('id')] = ansObj.attr('id');
			}
		}
	}
	
});



</script>

<?php 
	}
?>

