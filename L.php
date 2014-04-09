<?php
namespace{
    defined('APP_PATH') or define('APP_PATH',dirname(__FILE__).'/../app');
    define('APP', rtrim(APP_PATH,'/').'/');

    function l($string = NULL){
        if($string === NULL){
            if(!isset($GLOBALS['Base'])){
                $GLOBALS['Base'] = new L\Base;
            }
            return $GLOBALS['Base'];
        }

        $parse = new L\Parse($string);
        return $parse->main();;
    }
}

namespace L{
    function luoAutoLoader($className){
        if(strstr($className,'app') !== false){
            $className = str_replace('app\\', '', $className);
            $file = APP.$className.'.php';
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