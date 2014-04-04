<?php
require(dirname(__FILE__).'/base/Controller.php');
use L\Controller as Controller;

function l($string = NULL){
	if(!isset($GLOBALS['Controller'])){
		$GLOBALS['Controller'] = new Controller;
	}
	return $GLOBALS['Controller'];
}