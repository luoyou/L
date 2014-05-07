<?php
namespace{
    defined('APP_PATH') or define('APP_PATH',dirname(__FILE__).'/../app');
    define('APP', rtrim(APP_PATH,'/').'/');

    function l($string = NULL){
        if($string === NULL){
            static $base;
            if(!isset($base)){
                $base = new L\Base;
                $base->route();
            }
            return $base;
        }

        $parse = new L\route\Parse($string);
        return $parse->main();
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