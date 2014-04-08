<?php
namespace{
    function l($string = NULL){
        if(!isset($GLOBALS['Base'])){
            $GLOBALS['Base'] = new L\Base;
        }
        return $GLOBALS['Base'];
    }
}

namespace L{
    defined('APP_PATH') or define('APP_PATH',dirname(__FILE__).'/../app');

    function luoAutoLoader($className){
        if(strstr($className,'app') !== false){
            $className = str_replace('app\\', '', $className);
            $file = rtrim(APP_PATH,'/').'/'.$className.'.php';
        }else{
            $className = str_replace(__NAMESPACE__.'\\', '', $className);
            $file = __DIR__.'/'.$className.'.php';
        }
        if (is_readable($file)){
            require($file);
        }
    }
    spl_autoload_register("L\\luoAutoLoader");
}