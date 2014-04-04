<?php
namespace L;
require('Base.php');
use L\Base as Base;

class Controller extends Base{

	public $controller,$action;

	public function init(){
		parent::init();
		$this->route();
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

	public function getUrl(){
		return $this->domain.$_SERVER['REQUEST_URI'];
	}

	public function getDomain(){
		return $_SERVER['HTTP_HOST'];
	}

	public function route(){
		$controller = $this->route['defaultController'];
		$action  	= $this->route['defaultAction'];
		if(isset($_SERVER['PATH_INFO'])){
			$param = $_SERVER['PATH_INFO'];
			$param = ltrim($param,'/');
			$params = explode('/', $param);
			$controller = array_shift($params);
			$action	= array_shift($params);
			foreach ($params as $k => $v) {
				if($k%2 !== 1 && isset($params[$k+1])){
					$_GET[$v] = $params[$k+1];
				}
			}
		}

		$this->controller = $controller;
		$this->action 	  = $action;
		try{
			$this->initAction();			
		}catch(\Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}

	public function initAction(){
		$file = rtrim(APP_PATH,'/').'/controller/'.$this->controller.'.php';
		if(!file_exists($file)){
			throw new \Exception($this->controller."php file is not exist");
		}
		require($file);
		$className = $this->controller;
		$controller = new $className; 

	}

}