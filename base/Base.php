<?php
namespace L;
use Exception;

defined('APP_PATH') or define('APP_PATH',dirname(__FILE__).'/../../app');

class Base{
	
	public $config = [];

	public function __construct(){
		$this->init();
	}

	public function init(){
		$defaultConfig = require(dirname(__FILE__).'/config.php');
		$userConfig    = require(rtrim(APP_PATH,'/').'/config.php');
		try{
			$this->config  = $this->multi_array_merge($defaultConfig,$userConfig);
		}catch(Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}

	public function multi_array_merge($config,$userConfig){
		if(is_array($userConfig)){
			foreach ($userConfig as $k => $v) {
				if(is_array($v)){
					$config[$k] = $this->multi_array_merge($config[$k],$v);
				}else{
					if(!empty($v)){
						$config[$k] = $v;
					}
				}
			}
		}else{
			throw new Exception('Config must be array!');
		}

		return $config;
	}
}