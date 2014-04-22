<?php
namespace L\db;
use PDO;
class Model {

    public $db;
    public $table;
    public $order = NULL;
    public $limit = NULL;
    public $errors = [];
    public $params = [];
    public $statement;
    public $tablePrefix = '';
    public $condition = NULL;
    public $attribute = [];

    public function __construct(){
      $db = l()->db;
      $this->dsn = $db['connectionString'];
      $this->tablePrefix = $db['tablePrefix'];
      if(!isset($GLOBALS['db'])){
        $GLOBALS['db'] = new PDO($db['connectionString'], $db['username'], $db['password']);
      }
      $this->db = $GLOBALS['db'];
      $this->getTableName();
      $this->init();
    }

    public function __get($name){
      if(isset($this->attribute[$name])){
        return $this->attribute[$name];
      }
    }

    public function __set($name, $value){
      if(isset($this->attribute[$name])){
        $this->attribute[$name] = $value;
      }
    }

    public function init(){

    }

    public function findAll(){
      $this->buildSelectStatement();
      $resultArray = $this->execute()->fetchAll(PDO::FETCH_ASSOC);
      $results = [];
      foreach ($resultArray as $k => $v) {
        $results[$k] = clone $this;
        $results[$k]->attribute = $v;
      }
      return $results;
    }

    public function find(){
      $this->limit(1);
      $this->buildSelectStatement();
      $this->attribute = $this->execute()->fetch(PDO::FETCH_ASSOC);
      return $this;
    }

    public function count(){
      $this->statement  = 'select count(*) as row_num from ';
      $this->statement .= $this->table;
      if($this->condition !== NULL){
        $this->statement .= ' '.$this->condition;
      }
      
      return $this->execute()->fetch(PDO::FETCH_ASSOC)['row_num'];
    }

    public function validate(){
      $validate = new Validate;
      foreach ($this->rules as $v) {
        $columns = array_shift($v);
        array_walk($v, function($value,$key)use($validate){
          $validate->$key();
        });
      }
    }

    public function buildSelectStatement(){
      $this->statement  = 'select * from ';
      $this->statement .= $this->table;
      if($this->condition !== NULL){
        $this->statement .= ' '.$this->condition;
      }

      if($this->order !== NULL){
        $this->statement .= ' '.$this->order;
      }
      
      if($this->limit !== NULL){
        $this->statement .= ' '.$this->limit;
      }
    }

    public function execute($prepare = NULL){
      $prepare or $prepare = $this->statement;
      $statement = $this->db->prepare($prepare);
      foreach ($this->params as $k => $v) {
        $statement->bindValue($k, $v);
      }
      $statement->execute();
      return $statement;
    }

    public function where($condition){
      $this->condition = 'where ';
      if(is_string($condition)){
        $this->condition .= $condition;
      }
      
      if(is_array($condition)){
        $length = count($condition);
        $i = 0;
        foreach ($condition as $k => $v) {
          $i++;
          if($i == $length){
            $this->condition .= "$k = '$v'";  
          }else{
            $this->condition .= "$k = '$v'&&";
          }
        }
      }
      return $this;
    }

    public function limit($limit){
      $this->limit = 'limit '.$limit;
      return $this;
    }

    public function order($order){
      $this->order = 'order by '.$order;
      return $this;
    }

    public function getColumn(){
      $statement = 'show columns from '.$this->table;
      $result = $this->db->query($statement);
      $columns = $result->FetchAll(PDO::FETCH_ASSOC);
      foreach ($columns as $k => $v) {
        $this->attribute[$v['Field']] = '';
      }
    }

    public function getTableName(){
      $class = get_class($this);
      $classPath = explode('\\', $class);
      $table = end($classPath);
      $strArr = str_split($table);
      foreach ($strArr as $k => $v) {
        if($k !== 0){
          if($v === strtoupper($v)){
            $strArr[$k] = '_'.strtolower($v);
          }
        }else{
          $strArr[$k] = strtolower($v);
        }
      }
      $this->table = $this->tablePrefix.implode($strArr);
    }
} 