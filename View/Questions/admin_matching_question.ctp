<style>
p.question{
background:#fcaa9c;
border:1px solid #000;
padding:5px;
}

p.answer{
background:#faebd7;
border:1px solid #000;
padding:5px;
}

p.question-selected{
border:2px solid #1bab5f !important;
}

p.pre-match{
border:2px solid #103eba !important;
}

p.answer-select{
border:2px solid #1bab5f !important;
}

span.line{
 /* transform-origin: 0 100%; */
	position: absolute; /* allows to position it anywhere */
  height: 2px; /* Line width of 3 */
  background: #000; /* Black fill */
	z-index: 1000; /* make sure this is above your other elements */
	transform-origin: top left;
}

/* span.line{
	  position: absolute; allows to position it anywhere
    width: 3px; your chosen line width
    background-color: #06a; line color
    z-index: 1000; make sure this is above your other elements
    -webkit-transform-origin: top left;
    -moz-transform-origin: top left;
    -o-transform-origin: top left;
    -ms-transform-origin: top left;
    transform-origin: top left;
	} */
</style>
<h3>Matching Question Type </h3>
<div id="matchingQuestion" class="matching-question review col-xs-18 col-md-12">
	<div class="row">
			<div class="col-xs-4 col-md-3 padRight0">
			<p class="question" id="question1">Question 1</p>
			<p class="question" id="question2">Question 2</p>
			<p class="question" id="question3">Question 3</p>
			<p class="question" id="question4">Question 4</p>
			</div>
			<!-- <canvas id="matchCanvas" class="col-xs-8 col-md-6" ></canvas> -->
			<div class="col-xs-4 col-md-3 pull-right">
			<p class="answer" id="answer1">Answer 1</p>
			<p class="answer" id="answer2">Answer 2</p>
			<p class="answer" id="answer3">Answer 3</p>
			<p class="answer" id="answer4">Answer 4</p>
			</div>
	</div>

</div>
<script>
jQuery(function($){
	var parentOffset = $('#matchingQuestion').offset();
	var matchPairs = [];

	$('.question').on('click', function(e){
				obj1 = $(this);
				obj1.addClass('pre-match');
				if((obj1).hasClass('question-selected')) return unpair(obj1.attr('id')); //if its alread selected then return.

			$('.answer').on('click', function(e1){
					if(!obj1) return alert('Select one option from left side');
					if((obj1).hasClass('question-selected')) return;
					obj1.addClass('question-selected');
					var obj2 = $(this);
				if((obj2).hasClass('answer-select')) return;
					obj2.addClass('answer-select');
					drawline(obj1, obj2);
					matchPairs[obj1.attr('id')] = obj2.attr('id');
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
		pair.removeClass('question-selected');
		pair.removeClass('pre-match');
		var ansId = matchPairs[qId];
		//console.log(ansId);
		$('#'+ansId).removeClass('answer-select');
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
	
	});


</script>