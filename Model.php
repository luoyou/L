<?php
namespace L;

class Model {

    public $connect;
    public $charset;
    public $tablePrefix;

    public function __construct(){
        $db = l()->db;
        $this->connect = $db['connectionString'];
        $this->tablePrefix = $db['tablePrefix'];
        $this->charset = $db['charset'];
    }
} 