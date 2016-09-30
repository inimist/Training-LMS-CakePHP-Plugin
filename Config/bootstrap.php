<?php
/**
 * simple float number format
 * @param $num - number to be formatted
 */
function float($num, $len=2)	{
	return number_format($num, $len);
}

/**
 * calc grade
 * @param $marks_o - marks obtained
 * @param $maxmarks - maxmarks for this quiz
 * @param $quizgrade - criteria for grading quiz. i.e. 10
 */
function grade( $marks_o, $maxmarks, $quizgrade)	{
	//debug(func_get_args());
	//$percent_o = ($marks_o / $maxmarks) * 100;
	return ($marks_o / $maxmarks) * $quizgrade;
}

/**
 * grade_formatted grade
 * @param $marks_o - marks obtained
 * @param $maxmarks - maxmarks for this quiz
 * @param $quizgrade - criteria for grading quiz. i.e. 10
 */
function grade_formatted( $marks_o, $maxmarks, $quizgrade)	{
	//debug(func_get_args());
	//$percent_o = ($marks_o / $maxmarks) * 100;
	return float(grade( $marks_o, $maxmarks, $quizgrade));
}

/**
 * pcgrade - percent grade
 * @param $marks_o - marks obtained
 * @param $maxmarks - maxmarks for this quiz
 * @param $quizgrade - criteria for grading quiz. i.e. 10
 */
function pcgrade( $marks_o, $maxmarks, $quizgrade )	{
	//debug(func_get_args());
	return $marks_o / $maxmarks * 100;
	//return $marks_o / $maxmarks * $quizgrade;
}
/**
 * pcgrade - percent grade
 * @param $marks_o - marks obtained
 * @param $maxmarks - maxmarks for this quiz
 * @param $quizgrade - criteria for grading quiz. i.e. 10
 */
function pcgrade_formatted( $marks_o, $maxmarks, $quizgrade )	{
	//debug(func_get_args());
	return float(pcgrade( $marks_o, $maxmarks, $quizgrade ));
	//return $marks_o / $maxmarks * $quizgrade;
}
/**
 * humantime - convert a time string to human readable time difference
 * @param $oldtime - strtotime() of date/time string
 * @param $newtime - strtotime() of date/time string
 */
function humantime ($oldtime, $newtime = null, $returnarray = false)	{
	if(!$newtime) $newtime = time();
	$time = $newtime - $oldtime; // to get the time since that moment
	$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
	);
	$htarray = array();
	foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			$htarray[$text] = $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
			$time = $time - ( $unit * $numberOfUnits );
	}
	if($returnarray) return $htarray;
	return implode(' ', $htarray);
}
/**
 * humantimediff - convert a time string to human readable time difference
 * @param $old_dt - strtotime() of date/time string
 * @param $new_dt - strtotime() of date/time string
 */
function humantimediff($old_dt, $new_dt)	{
	$new_dt = $new_dt ? strtotime($new_dt) : null;
	return humantime(strtotime($old_dt), $new_dt);
}
/**
 * passorfail - convert a time string to human readable time difference
 * @param $earnedgrade - grade earned by user
 * @param $threshold - threshold for passing a quiz
 */
function passorfail($earnedgrade, $threshold)	{
	if($earnedgrade >= $threshold) return 'pass';
	else return 'fail';
}

define('MANMARKSFLD', 'minfraction'); //manual marks field for text answer type questions
define('USESTARTDATE', false); //This is customization constant for the current website
define('QUIZ_LOCK_OFFSET', 1); //This is customization constant for the current website