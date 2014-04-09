<?php
namespace L;

class Parse {

    public $string;
    public $name;

    public function __construct($string){
        $this->string = $string;
    }

    public function main(){
        $object = $this->parseSymbol();
        if(is_object($object)){
            return $object;
        }

        $object = $this->parseString();
        if(is_object($object)){
            return $object;
        }

        return $this->parseWord();
    }

    public function parseSymbol(){
        $symbol = substr($this->string, 0, 1);
        if(isset(l()->method['symbol'][$symbol])){
            $this->name = str_replace($symbol, '', $this->string);
            $method = l()->method['symbol'][$symbol];
            return $this->$method();
        }else{
            return false;
        }
    }

    public function parseString(){
        $info = explode($this->string, ':');
        return false;
    }

    public function parseWord(){
        return false;
    }

    public function model(){
        $className = 'app\\model\\'.$this->name;
        return new $className;
    }
} 