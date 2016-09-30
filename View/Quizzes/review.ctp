<?php 
if($this->layout == 'pdf'){
	$basepath = 'http://'.$_SERVER['HTTP_HOST'];
	?>
	
<script type="text/javascript" src="<?php echo $basepath; ?>/theme/coco/libs/jquery/jquery-1.11.3.min.js"></script>
<!-- <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script> -->
<!-- <div>
<?php echo $this->Form->create('Quiz', array('url'=>array('plugin'=>'training', 'controller'=>'quizzes', 'action'=>'review', 'admin'=>true, $quizAttempt['QuizAttempt']['id'], '?'=>array('course_id'=>$course['Course']['id'], 'export'=>'pdf'))));  ?>
		<?php echo $this->Form->hidden('pdf_html', array('value'=>'', 'id'=>'htmlforpdf')); ?>
		<div style="display:flex">
		<?php echo $this->Form->submit(__('HTML to PDF'),array('class'=>'pull-right btn btn-default', 'name'=>'export_pdf', 'id'=>'exportdata')); ?>
		<?php echo $this->Form->end(); ?>
</div> -->
<?php
 } 
?>


<div class="courses view">
<?php if($this->layout == 'pdf'){ ?>
<style>
	/* Matching pairs question styles */
div.row {
    display: block;
}

.match-pair-left-box-pdf {
	    display: inline-block !important;
	}

* {
    outline: none !important;
		box-sizing: border-box;
}

.col-md-3 , .col-xs-4 {
    width: 25%;
}

.pull-right {
    float: right!important;
}

.matching-pairs{
 margin: 10px 25px;
}
p.leftpair{
background:#fcaa9c;
border:1px solid #ccc;
padding:5px;
}

p.rightpair{
background:#faebd7;
border:1px solid #ccc;
padding:5px;
}

p.leftpair-selected{
border:2px solid #1bab5f !important;
}

p.pre-match{
border:2px solid #103eba !important;
}

p.rightpair-selected{
border:2px solid #1bab5f !important;
}

span.line{
 /* transform-origin: 0 100%; */
	position: absolute; /* allows to position it anywhere */
  height: 2px; /* Line width of 3 */
  background: #000; /* Black fill */
	z-index: 1; /* make sure this is above your other elements */
	transform-origin: top left;
}	

.matching-question{
	    position: relative;
}

</style>
<?php
 } 
?>

	<h2>Course: <?php echo $course['Course']['name']; ?></h2>

	<?php if($this->layout != 'pdf' && $this->Utility->isAdmin()) echo $this->Html->link(__('Export to PDF'), array('plugin'=>'training', 'controller'=>'quizzes', 'action'=>'review', 'admin'=>true, $quizAttempt['QuizAttempt']['id'], '?'=>array('course_id'=>$course['Course']['id'], 'export'=>'pdf')), array('class'=>'btn btn-default', 'id'=>'more-occurrences', 'target'=>'_blank'));   ?>
<div class="col-xs-16 col-md-12">

<?php 
//debug($quizAttempt);
$this->Training->setQuizSettings($_quizSettings);

if($quizAttempt): 

	$earnedgrade = grade($quizAttempt['QuizAttempt']['sumgrades'], $quizAttempt['Quiz']['sumgrades'], $quizAttempt['Quiz']['grade']);
	$earnedgrade_formatted = float( $earnedgrade );
?>

		<h3>Test: "<?php echo $quizAttempt['Quiz']['name']; ?>" - Preview</h3>

		<table class="table table-striped generaltable generalbox quizreviewsummary">
			<tbody>
				<tr>
					<th class="cell" scope="row">User</th>
					<td class="cell"><?php echo $this->Utility->linkeduname($quizAttempt['User']); ?></td>
				</tr>
				<tr>
					<th class="cell" scope="row">State</th>
					<td class="cell"><?php echo ucfirst($quizAttempt['QuizAttempt']['state']); ?></td>
				</tr>
				<tr>
					<th class="cell" scope="row">Started on</th>
					<td class="cell"><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quizAttempt['QuizAttempt']['timestart']); ?></td>
				</tr>
				<tr>
					<th class="cell" scope="row">Completed on</th>
					<td class="cell"><?php echo $this->Time->format(__SYSTEM_DATETIME_FORMAT, $quizAttempt['QuizAttempt']['timefinish']); ?></td>
				</tr>
				<tr>
					<th class="cell" scope="row">Time taken</th>
					<td class="cell"><?php echo humantimediff($quizAttempt['QuizAttempt']['timestart'], $quizAttempt['QuizAttempt']['timefinish']); ?></td>
				</tr>

				<?php if($_quizSettings['passfailcriteria']=='question'): ?>
				<tr>
					<th class="cell" scope="row">Marks</th>
					<td class="cell"><?php echo $quizAttempt['QuizAttempt']['correctquestions']; ?>/<?php echo $quizAttempt['Quiz']['question_count']; ?></td>
				</tr>
				<?php else: ?>
				<tr>
					<th class="cell" scope="row">Marks</th>
					<td class="cell"><?php echo float($quizAttempt['QuizAttempt']['sumgrades']); ?>/<?php echo float($quizAttempt['Quiz']['sumgrades']); ?></td>
				</tr>
				<?php endif; ?>

			<?php if($_quizSettings['usegrading']): ?>
				<tr>
					<th class="cell" scope="row">Grade</th>
					<td class="cell"><b><?php echo $earnedgrade_formatted; ?></b> out of <?php echo float($quizAttempt['Quiz']['grade']); ?> (<b><?php echo pcgrade_formatted($quizAttempt['QuizAttempt']['sumgrades'], $quizAttempt['Quiz']['sumgrades'], $quizAttempt['Quiz']['grade']); ?></b>%)</td>
				</tr>
				<?php endif; ?>
				<?php if($quizAttempt['Quiz']['showpassfail'])	{ ?>
				<tr>
					<th class="cell" scope="row">Result</th>
					<td class="cell"><b><?php echo $this->Training->getresult( $quizAttempt, 'texticon', 1 ); ?></b></td>
				</tr>
				<?php } ?>
				<?php if($course['Course']['signature'])	{ ?>
				<tr>
					<th class="cell" scope="row">Signature:</th>
					<td class="cell"><b><?php if($quizAttempt['QuizAttempt']['signed_by_user']) $this->Utility->get_signature($quizAttempt['QuizAttempt']['user_id'], '100');
						else echo "Not signed"; ?></b></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

<?php
$matchpairtype = false;
foreach($quizAttempt['QuestionAttempt'] as $QuestionAttempt)	{
	if($QuestionAttempt['QuestionType']['qtype']== 'matching-pairs'){
		$matchpairtype = true;
		}
	if($this->layout == 'pdf'){
		echo '<hr style="border: 1px dashed #000 !important; " />';
		}
	echo $this->Training->renderQuestionAttempt($QuestionAttempt);
}


if($this->Utility->isAdmin() && isset($this->request->query['test']))	{ ?>	
<p class="text-center"><?php if($this->layout != 'pdf') echo $this->Html->link(__('Finish Review'), array('controller'=>'quizzes', 'action' => 'testview', $quizAttempt['Quiz']['id'], '?'=>array('course_id'=>$course['Course']['id']))); ?></p>

<?php
}
	else	{
?>

<p class="text-center"><?php
	$url = array('controller'=>'quizzes', 'action' => 'view', $quizAttempt['Quiz']['id'], 'admin'=>false);
		if(isset($this->request->query['redirect'])){
			$url = array('controller'=>'courses', $quizAttempt['Quiz']['course_id'], 'admin' => true);
			$url['action'] = $this->request->query['redirect'] == 'admin_enrol' ? 'enrol' : 'view';
		}
if($this->layout != 'pdf') echo $this->Html->link(__('Finish Review'), $url); ?></p>


<?php } //echo $this->Element('Quiz/sample_form'); ?>

<?php
endif; ?>

</div></div>
<div id="editor"></div>

<!-- Modal -->
<div class="modal fade" id="editMarks" tabindex="-1" role="dialog" aria-labelledby="editMarksLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>
<script>
jQuery(function($)	{
	$('#editMarks').on('show.bs.modal', function (event) {
		//var button = $(event.relatedTarget) // Button that triggered the modal
		//var recipient = button.data('target') // Extract info from data-* attributes
		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
		//var modal = $(this)
		//modal.find('.modal-title').text('Edit marks of ' + recipient)
		//modal.find('.modal-body input').val(recipient)
	})
})
</script>
<?php 
if($matchpairtype){  ?>
<script>
function drawline(obj1, obj2, rightAnswer, parentOffset){
				var width1 = obj1.width();
					var height1 = obj1.height();
					var offset1 = obj1.offset();
					var y1 = Number(offset1.left) + Number(width1) ;
					var x1 = Number(offset1.top) + Number(height1/2) ;
					console.log(offset1);
					console.log(x1);
					console.log(y1);
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
					"<? if($this->layout == 'pdf'){ echo 'var actualLeft = y1.toFixed(2) - offset1.left + 136; var actualTop = x1.toFixed(2) - parentOffset.top;'; ?>";
					console.log('actual postition');
					console.log(actualLeft);
					console.log(actualTop);
					if(rightAnswer){
						lineHtml = "<span class='line' style='left:" + actualLeft + "px; top:" + actualTop + "px; width:" + length.toFixed(2) + "px; transform:" + transform + "; background: #1BAB5F !important;'></span>";
						}else{
						lineHtml = "<span class='line incorrect' style='left:" + actualLeft + "px; top:" + actualTop + "px; width:" + length.toFixed(2) + "px; transform:" + transform + "; background: #FF403C !important;'></span>";
					}
					console.log(lineHtml);
					obj1.after(lineHtml);
				return;
		}


//show previous response if exists.
var correctMatches = new Object();
var responseMatches = new Object();
$('.macthing-question-answers').each(function(){
	var qID = $(this).children('input.mp-question-id').val();
	parentOffset = $(this).children('.matching-question').offset();
	console.log(qID);
	console.log(parentOffset);
	var response = $(this).children('input.mpq-correctanswer').val();
	response = JSON.parse(response);
	console.log(response);
	//get correct answers
	var rightanswer = $(this).children('input.mpq-responsesummary').val();
	rightanswer = JSON.parse(rightanswer);
	
	var correctMatchPairs = new Object();
		for (var cpair in rightanswer) {
		var correctPairs = rightanswer[cpair];
			var cleft = correctPairs[0];
			var cright = correctPairs[1];
			if(cleft != '' && cright != ''){
				var cqObj = new Object();
				var cansObj = new Object();
				$(this).find('p.leftpair').each(function(){
					if($(this).html() == cleft){
						cqObj = $(this);
					}
				});
				$(this).find('p.rightpair').each(function(){
					if($(this).html() == cright){
						cansObj = $(this);
					}
				});
				correctMatchPairs[cqObj.attr('id')] = cansObj.attr('id');
			}
		}
if(response){
	var matchPairs = new Object();
	for (var pair in response) {
		var responsePairs = response[pair];
			console.log(responsePairs);
			var left = responsePairs[0];
			var right = responsePairs[1];
			if(left != '' && right != ''){
				var rightAnswer = false;
				var qObj = new Object();
				var ansObj = new Object();
				$(this).find('p.leftpair').each(function(){
					if($(this).html() == left){
						//$(this).addClass('leftpair-selected');
						qObj = $(this);
					}
				});
				$(this).find('p.rightpair').each(function(){
					if($(this).html() == right){
					  //$(this).addClass('rightpair-selected');
						ansObj = $(this);
					}
				});
				if(correctMatchPairs[qObj.attr('id')] == ansObj.attr('id')){
					rightAnswer = true;
				 }
				matchPairs[qObj.attr('id')] = ansObj.attr('id');
				drawline(qObj, ansObj, rightAnswer, parentOffset);
			}
		}
	}
correctMatches[qID] = correctMatchPairs;
responseMatches[qID] = matchPairs;
});

//on resize window
$(window).resize(function(evt){
		console.log('window resized');
			$('.macthing-question-answers').each(function(){
				var qsID = $(this).children('input.mp-question-id').val();
				var parentOffset = $(this).children('.matching-question').offset();
					matchPairs = responseMatches[qsID];
					correctMatchPairs = correctMatches[qsID];
					if(!matchPairs) return;
					//redraw each line
						for (var qId in matchPairs) {
							rightAnswer = false;
						//console.log(qId);
						//console.log(matchPairs[qId]);
						var ansId = matchPairs[qId];
						if(correctMatchPairs[qId] == ansId){
								rightAnswer = true;
							 }
						var qObj = $('#'+qId);
						var ansObj = $('#'+ ansId);
						qObj.next("span").remove();
						drawline(qObj, ansObj, rightAnswer, parentOffset);
					}
					evt.preventDefault();
					return;
			});
	//console.log($('.courses.view').html());
	});

</script>

<?php } ?>

<?php if($this->layout == 'pdf'){ ?>
<script>
	//window.print();
/* var requiredHtml = $('.courses.view').html();
//console.log(requiredHtml);
//$('#htmlforpdf').val(requiredHtml);
//$('QuizAdminReviewForm').submit();
var doc = new jsPDF();
var specialElementHandlers = {
    '#editor': function (element, renderer) {
        return true;
    }
};

 doc.fromHTML(requiredHtml, 15, 15, {
        'width': 1100,
            'elementHandlers': specialElementHandlers
    });
    doc.save('sample-file.pdf');
*/
</script>

<?php } ?>