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
            foreach ($rule as $k => $v) {
                if(method_exists($this, $k)){
                    $this->$k($field, $v);
                }else{
                    var_dump($k);
                }
            }
        }
    }

    public function type($field, $type){
        
    }

    public function compare($field, $params){
        $c = $params['condition'];
        if($c == '='){
            if($this->model->filed != $this->model->$params['field']){
                $this->errorMessage($field, '不'.$c.$params['field'].'字段');
            }
        }elseif($c == '>') {
            if($this->model->filed <= $this->model->$params['field']){
                $this->errorMessage($field, '不'.$c.$params['field'].'字段');
            }
        }elseif($c == '>='){
            if($this->model->filed < $this->model->$params['field']){
                $this->errorMessage($field, '不'.$c.$params['field'].'字段');
            }
        }elseif($c == '<'){
            if($this->model->filed >= $this->model->$params['field']){
                $this->errorMessage($field, '不'.$c.$params['field'].'字段');
            }
        }elseif($c == '<='){
            if($this->model->filed > $this->model->$params['field']){
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