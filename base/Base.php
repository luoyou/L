<?php
namespace L;
class Base{

	public $config = [];
	
	public function __construct(){
		$this->route();
		$this->init();
	}

	public function __get($name){
		if(isset($this->config[$name])){
			return $this->config[$name];
		}else{
			$method = 'get'.$name;
			return $this->$method();
		}
	}

	public function __call($name,$arguments){
		echo $name.' function is not exist!';
	}

	public function init(){
		$defaultConfig = require(dirname(__FILE__).'/config.php');
		$userConfig    = 0;
	}

	public function getUrl(){
		return $this->domain.$_SERVER['REQUEST_URI'];
	}

	public function getDomain(){
		return $_SERVER['HTTP_HOST'];
	}

	public function route(){
		$param = $_SERVER['PATH_INFO'];
		$params = explode('/', $param);
		$controller = array_shift(array);
	}

}