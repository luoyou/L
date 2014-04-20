<?php
namespace L\db;

class Validate {

  public function __construct($model){
    $model->limit = 1;
    var_dump($model);
  }

}