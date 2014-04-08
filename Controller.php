<?php
namespace L;
use Exception;

class Controller{

	public function __construct(){
		$this->init();
	}

	public function init(){

	}

	public function render($template = NULL, $args = []){
		if($template){
			$template_info = debug_backtrace()[1];
			$folders  = explode('\\', $template_info['class']);
			$folder = strtolower(end($folders));
			$template = APP_PATH.$template_info['function'].'.php';	
		}else{
			
		}
	}
}