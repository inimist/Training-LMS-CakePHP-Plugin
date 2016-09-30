<?php
App::uses('Component', 'Controller');
/*
* just to calculate results for training in course controller
*/
class TrainingComponent extends Component {

/**
 * @var $_quizSettings
 */
 
	public $_quizSettings;
	
	/**
 * setQuizSettings Get Quiz settings
 *
 * @param $question mixed
 * @return void
 */

	function setQuizSettings ( $quizSettings )	{
		$this->_quizSettings = $quizSettings;
	}

	/**
	 * passorfail - get pass or fail result
	 * @param $earnedgrade - grade earned by user
	 * @param $threshold - threshold for passing a quiz
	 */
	function passorfail($earnedgrade, $threshold, $displaytype = 'short', $hint = false)	{

		$result = passorfail($earnedgrade, $threshold);
		$html = '';
		if($displaytype=='basic') $html .= $result; //string as it is; "pass" or "fail"
		if($displaytype=='short') $html .= substr(__($result), 0, 1); //"P" or "F"

		return $html;
	}

	/**
	 *Get pass/fail result for a quizAttempt
	 * @param $quizAttempt array Having 'QuizAttempt' and 'Quiz' indexes set with corresponding data
	 */
	function getresult( $quizAttempt, $displaytype = 'short', $hint = false)	{
		//debug($quizAttempt);
		//pr($this->_quizSettings);
		if(!isset($quizAttempt['QuizAttempt']) || $quizAttempt['QuizAttempt']['state']=='inprogress')  return;
		if($this->_quizSettings['passfailcriteria']=='grade')	{
			$earnedgrade = grade($quizAttempt['QuizAttempt']['sumgrades'], $quizAttempt['Quiz']['sumgrades'], $quizAttempt['Quiz']['grade']);
			$threshold = $quizAttempt['Quiz']['minpassgrade'];
		}	else	if($this->_quizSettings['passfailcriteria']=='question')	{
			$earnedgrade = $quizAttempt['QuizAttempt']['correctquestions'];
			$threshold = $quizAttempt['Quiz']['minpassquestions'];
		}

		return $this->passorfail($earnedgrade, $threshold, $displaytype, $hint);
	}

	function float($num)	{
		return number_format($num, 2);
	}

/*
* get_signature method - render signature for a user
* @param $id int
* return void
*/
 function get_signature($id=null, $width=null){
		if(!$id) $id = $this->Session->read('Auth.User.id');
		if(!$width) $width = '250';
		$files_dir = WWW_ROOT.'files'.DS.'sign';
		if(defined('SITE_DIR')){
			$files_dir = ROOT.DS.SITE_DIR.DS.'Locale'.DS.'View'.DS.'webroot'.DS. 'files'.DS.'sign';
		 }
		$filename = 'signature-'.$id.'.jpg';
		$file_sign = $files_dir.DS.$filename;
		//debug($file_sign);
			if(file_exists($file_sign)) {   
				if(defined('SITE_DIR')){ ?>
					<img src="<?php echo $this->Html->url('/theme/default/files/sign/'.$filename, true); ?>" alt="signature" title="signature" class="img" style="vertical-align: text-top; width:<?php echo $width; ?>px;" />
				<?php } 
						else{ ?>
					<img src="<?php echo $this->Html->url('/files/sign/'.$filename, true); ?>" alt="signature" title="signature" class="img" style="    vertical-align: text-top; width:<?php echo $width; ?>px;" />
				<?php } 
						if($this->isAdmin() && $this->request->params['controller'] == 'users'){
						?>
						<a id="removeCurrentSign" class="remove_image glyphicon glyphicon-remove" title="Remove"> </a>
		<?php 
						}			
				} 
			else{
				if($this->isAdmin()) echo "<p style='color:#d9534f;'> No Signature file exists, Upload new ". $this->Html->link(__('Here'), array('plugin'=>false, 'controller'=>'users', 'action'=>'uploadsignature', $id, 'admin'=>true))."  </p>";
				else echo "<p style='color:#d9534f;'> No Signature file exists </p>";
			}
	}

/*
* isSignExists method - check if signature exists for a user
* @param $id int
* return boolean 
*/
function isSignExists($id=null){
		if(!$id) return false;
		$files_dir = WWW_ROOT.'files'.DS.'sign';
		if(defined('SITE_DIR')){
			$files_dir = ROOT.DS.SITE_DIR.DS.'Locale'.DS.'View'.DS.'webroot'.DS. 'files'.DS.'sign';
		 }
		$filename = 'signature-'.$id.'.jpg';
		$file_sign = $files_dir.DS.$filename;
		//debug($file_sign);
		if(file_exists($file_sign)) return true;
		else return false;
	}

/*
* getSignPath method - check if signature exists for a user
* @param $id int
* return boolean 
*/
function getSignPath($id=null){
	if(!$id) $id = $this->Session->read('Auth.User.id');
	$filename = 'signature-'.$id.'.jpg';
	if(defined('SITE_DIR')){ 
					return ROOT.DS.SITE_DIR.DS.'Locale'.DS.'View'.DS.'webroot'.DS. 'files'.DS.'sign'.DS.$filename; 
				} else{ 
			return WWW_ROOT.'files'.DS.'sign'.DS.$filename;
		} 
	}

}