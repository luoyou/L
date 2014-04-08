<?php
namespace L;

trait Process{

	public $config = [];

	public function getConfig(){
		$defaultConfig = require(dirname(__FILE__) . '/config.php');
		$userConfig    = require(rtrim(APP_PATH,'/').'/config.php');
		try{
			$this->config  = $this->multi_array_merge($defaultConfig,$userConfig);
		}catch(Exception $e){
			echo 'Caught exception: Config ',  $e->getMessage(), "\n";
		}

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

    public function multi_array_merge($arr,$arr1){
		if(is_array($arr1)){
			foreach ($arr1 as $k => $v) {
				if(is_array($v)){
					$arr[$k] = $this->multi_array_merge($arr[$k],$v);
				}else{
					if(!empty($v)){
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