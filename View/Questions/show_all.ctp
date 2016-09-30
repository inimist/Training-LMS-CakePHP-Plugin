<h2>Questions with unsure answers are highlighted.</h2>

<table>
		<th>Question</th>
		<th>Your Answers</th>

		
		<?php foreach($data as $result):?>
			<tr>
				<td>
					
					<?php 
						$marked = array();
						if($result['QuestionQuiz']['is_marked']==1):
							$marked = array('class' => 'marked');
						endif;
						
						echo $html->link($result['Question']['text'], array('action'=>'display',$result['QuestionQuiz']['id']), $marked);
					?>
				</td>
				
				
				<td>
					<?php echo $result['Answer']['text'];?>
				</td>
			
			
			</tr>
		
		<?php endforeach; ?>

	
	</table>
	
	<div align="right"><?php echo $html->link('Get Score', array('action'=>'results', true), null, sprintf(__('Are you sure you want to get your quiz result?', true)));?></div>




