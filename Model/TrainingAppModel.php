<?php
/**
 * Copyright 2015 - 2016, Inimist Technologies (http://inimist.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2016, Inimist Technologies (http://inimist.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppModel', 'Model');

/**
 * Training AppModel
 *
 * @package Training
 */
class TrainingAppModel extends AppModel {

	/*The fractions to be used in questions.. */
	public $fractions = array(
	"0.0"=>"None (0)",
	"1.0"=>"100%",
	"0.9"=>"90%", //only need to show 0 grade or 1 grade
	"0.8333333"=>"83.33333%",
	"0.8"=>"80%",
	"0.75"=>"75%",
	"0.7"=>"70%",
	"0.6666667"=>"66.66667%",
	"0.6"=>"60%",
	"0.5"=>"50%",
	"0.4"=>"40%",
	"0.3333333"=>"33.33333%",
	"0.3"=>"30%",
	"0.25"=>"25%",
	"0.2"=>"20%",
	"0.1666667"=>"16.66667%",
	"0.1428571"=>"14.28571%",
	"0.125"=>"12.5%",
	"0.1111111"=>"11.11111%",
	"0.1"=>"10%",
	"0.05"=>"5%" 
	/*"-0.05"=>"-5%",
	"-0.1"=>"-10%",
	"-0.1111111"=>"-11.11111%",
	"-0.125"=>"-12.5%",
	"-0.1428571"=>"-14.28571%",
	"-0.1666667"=>"-16.66667%",
	"-0.2"=>"-20%",
	"-0.25"=>"-25%",
	"-0.3"=>"-30%",
	"-0.3333333"=>"-33.33333%",
	"-0.4"=>"-40%",
	"-0.5"=>"-50%",
	"-0.6"=>"-60%",
	"-0.6666667"=>"-66.66667%",
	"-0.7"=>"-70%",
	"-0.75"=>"-75%",
	"-0.8"=>"-80%",
	"-0.8333333"=>"-83.33333%",
	"-0.9"=>"-90%",
	"-1.0"=>"-100%"*/);

	/*The fractions to be used in questions.. */
	public $penalties = array(
		"1.0"=>"100%",
		"0.5"=>"50%",
		"0.3333333"=>"33.33333%",
		"0.25"=>"25%",
		"0.2"=>"20%",
		"0.1"=>"10%",
		"0.0"=>"0%"
	);

	public function fractionList()	{
		return $this->fractions;
	}

	public function penalityList()	{
		return $this->penalties;
	}
}
