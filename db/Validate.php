<?php
namespace L\db;

class Validate {

    public $rules;
    public $model;

    public function __construct(){

    }

    public function execute(){
        foreach ($this->rules as $rule) {
            $this->parseRule($rule);
        }
    }

    public function parseRule($rule){
        $field_str = array_shift($rule);
        $fields    = explode(',', $field_str);
        foreach ($fields as $field) {
            $field = trim($field);
            foreach ($rule as $k => $v) {
                if(method_exists($this, $k)){
                    $this->$k($field, $v);
                }else{
                    var_dump($k);
                }
            }
        }
    }

    public function match($field, $arguments){
        $regular = $arguments['regular'];
        $errormessage = $arguments['errorMessage'];
        if(!preg_match($regular, $this->model->$field)){
            $this->errormessage($field, $errormessage);
        }
    }

    public function type($field, $type){
        switch ($type) {
            case 'numeric':
                if(!is_numeric($this->model->$field)){
                    $this->errorMessage($field, '必须为数字类型');
                }
                break;
            case 'int':
                if(!is_int($this->model->$field)){
                    $this->errorMessage($field, '必须为整型');   
                }
                break;
            case 'string':
                if(!is_string($this->model->$field)){
                    $this->errorMessage($field, '必须为字符串');   
                }
                break;
        }
    }

    public function compare($field, $params){
        $c = $params['condition'];
        if($c == '='){
            if($this->model->$field != $this->model->$params['field']){
                $this->errorMessage($field, '不'.$c.$params['field'].'字段');
            }
        }elseif($c == '>') {
            if($this->model->$field <= $this->model->$params['field']){
                $this->errorMessage($field, '不'.$c.$params['field'].'字段');
            }
        }elseif($c == '>='){
            if($this->model->$field < $this->model->$params['field']){
                $this->errorMessage($field, '不'.$c.$params['field'].'字段');
            }
        }elseif($c == '<'){
            if($this->model->$field >= $this->model->$params['field']){
                $this->errorMessage($field, '不'.$c.$params['field'].'字段');
            }
        }elseif($c == '<='){
            if($this->model->$field > $this->model->$params['field']){
                $this->errorMessage($field, '不'.$c.$params['field'].'字段');
            }
        }
    }

    public function necessary($field, $state = true){
        if($state === true || $state){
            if($this->model->$field === NULL || $this->model->$field === ''){
                $this->errorMessage($field, '不能为空');
            }
        }
    }

    public function length($field, $length){
        $range = explode(',', $length);
        $strlen = strlen($this->model->$field);
        if($strlen<$range[0]){
            $this->errorMessage($field, '长度必须大于'.$range[0]);
        }

        if($strlen>$range[1]){
            $this->errorMessage($field, '长度必须小于'.$range[1]);
        }
    }

    public function errorMessage($field, $message){
        if(isset($this->model->errors[$field])){
            $this->model->errors[$field] .= ','.$message;
        }else{
            $this->model->errors[$field] = $field.'字段'.$message;
        }
    }

}