<?php
namespace L;
use Exception;

class Base{

    public $config = [];

    public function __construct(){
        $this->getConfig();
        $this->init();
    }

    public function init(){

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
            echo 'Caught exception: ',$e->getMessage(),", at ",$e->getFile()," line ",$e->getLine(),"\n";
        }
    }

    public function initAction($controller, $action){
        $file = APP.'controller/'.$controller.'Controller.php';
        if(!file_exists($file)){
            throw new Exception($controller.".php file is not exist");
        }
        $className = 'app\\controller\\'.$controller.'Controller';
        $controller = new $className;
        $action = 'action'.$action;
        $controller->$action();
    }

    public function getConfig(){
        $defaultConfig = require(dirname(__FILE__) . '/config.php');
        $userConfig    = require(APP.'config.php');
        try{
            $this->config  = $this->multi_array_merge($defaultConfig,$userConfig);
        }catch(Exception $e){
            echo 'Caught exception: Config ',  $e->getMessage(), "\n";
        }
    }

    public function __get($name){
        if(isset($this->config[$name])){
            return $this->config[$name];
        }
        switch ($name) {
            case 'domain':
                return $this->getDomain();
                break;

            case 'url':
                return $this->getUrl();
                break;
        }
    }

    /*
    public function __call($name,$arguments){
        echo $name.' function is not exist!';
    }
    */

    public function multi_array_merge($arr,$arr1){
        if(is_array($arr1)){
            foreach ($arr1 as $k => $v) {
                if(is_array($v)){
                    isset($arr[$k]) or $arr[$k] = [];
                    $arr[$k] = $this->multi_array_merge($arr[$k],$v);
                }else{
                    if(isset($v)){
                        $arr[$k] = $v;
                    }
                }
            }
        }else{
            throw new Exception('must be array!');
        }

        return $arr;
    }

    public function getUrl(){
        return $this->domain.$_SERVER['REQUEST_URI'];
    }

    public function getDomain(){
        return $_SERVER['HTTP_HOST'];
    }
}