<?php
namespace L;
use Exception;

class Base{

    use Process; 

	public function __construct(){
		$this->init();
	}

	public function init(){
        $this->getConfig();
        $this->route();
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

        try{
            $this->initAction($controller, $action);
        }catch(Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function initAction($controller, $action){
        $file = rtrim(APP_PATH,'/').'/controller/'.$controller.'.php';
        if(!file_exists($file)){
            throw new Exception($this->controller.".php file is not exist");
        }
        $className = 'app\\controller\\'.$controller;
        $controller = new $className;
        $controller->$action();
    }
}