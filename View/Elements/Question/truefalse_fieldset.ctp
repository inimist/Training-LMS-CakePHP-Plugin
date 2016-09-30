	<?php
			/*$i = 0;
			if(isset($this->request->data['QuestionAnswer'][$i]))	{
				if(isset($this->request->data['QuestionAnswer'][$i]['id']))	{
					echo $this->Form->input('QuestionAnswer.'.$i.'.id');
				}
			}

			//echo $this->Form->input('QuestionAnswer.'.$i.'.title', array('type'=>'text','label'=>sprintf(__('Choice %d'), $i+1), 'div'=>'form-group'));
			echo $this->Form->hidden('QuestionAnswer.'.$i.'.fraction', array('value'=>1));
			echo $this->Form->input('QuestionAnswer.'.$i.'.correct_answer', array('options'=>array('true'=>'True', 'false'=>'False'),'label'=>__('Correct Answer'), 'div'=>'form-group'));*/


			foreach($this->request->data['QuestionAnswer'] as $i => $answer)	{
				echo $this->Form->hidden('QuestionAnswer.'.$i.'.id');
				echo $this->Form->hidden('QuestionAnswer.'.$i.'.fraction');
				//debug($answer['fraction']);
				if($answer['fraction']==1) {$this->request->data['Question']['correct_answer'] = ($i==0 ? 1 :0);}
			}
			echo $this->Form->input('correct_answer', array('id'=>'correctanswer', 'options'=>array(1=>'True', 0=>'False'),'label'=>__('Correct Answer'), 'div'=>'form-group'));
		?>
<script>
jQuery(function($)	{
	$('#correctanswer').on('change', function()	{
		var $i = $(this).val();
		if($i==0)	{
			$('#QuestionAnswer0Fraction').val(0);
			$('#QuestionAnswer1Fraction').val(1);
		}	else {
			$('#QuestionAnswer0Fraction').val(1);
			$('#QuestionAnswer1Fraction').val(0);
		}
	})
})
</script>