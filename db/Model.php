<?php
namespace L\db;
use PDO, Exception;;
class Model {

    public $db;
    public $table;
    public $order = NULL;
    public $limit = NULL;
    public $errors = [];
    public $params = [];
    public $statement;
    public $tablePrefix = '';
    public $columns;
    public $primaryKey;
    public $condition = NULL;
    public $attribute = [];
    public $verified = false;

    public function __construct() {
        $db_config = l()->db;          // get database config
        $this->dsn = $db_config['connectionString'];
        $this->tablePrefix = $db_config['tablePrefix'];
        static $db;
        if (!isset($db)) {
            $db = new PDO($db['connectionString'], $db['username'], $db['password']);
        }
        $this->db = $GLOBALS['db'];
        $this->getTableName();  // 获取表名
        $this->getColumn();
        $this->init();
    }

    public function __get($name) {
        if (isset($this->attribute[$name])) {
            return $this->attribute[$name];
        }
    }

    public function __set($name, $value) {
        if (isset($this->attribute[$name])) {
            $this->attribute[$name] = $value;
        }
    }

    public function init() {

    }

    public function setAttributes($attribute){
        $this->attribute = $attribute;
        return $this;
    }

    public function findAll() {
        $this->buildSelectStatement();
        $resultArray = $this->execute()->fetchAll(PDO::FETCH_ASSOC);
        $results = [];
        foreach ($resultArray as $k => $v) {
            $results[$k] = clone $this;
            $results[$k]->attribute = $v;
        }
        return $results;
    }

    public function find() {
        $this->limit(1);
        $this->buildSelectStatement();
        $this->attribute = $this->execute()->fetch(PDO::FETCH_ASSOC);
        return $this;
    }

    public function count() {
        $this->statement  = 'select count(*) as row_num from ';
        $this->statement .= $this->table;
        if ($this->condition !== NULL) {
            $this->statement .= ' '.$this->condition;
        }

        return $this->execute()->fetch(PDO::FETCH_ASSOC)['row_num'];
    }

    public function validate() {
        $validate = new Validate;
        $validate->model = $this;
        $validate->rules = $this->rules;
        $validate->execute();
        if(!empty($this->errors)){
            $this->verified = true;
        }

        return $this;
    }

    public function buildSelectStatement() {
        $this->statement  = 'select * from ';
        $this->statement .= $this->table;
        if ($this->condition !== NULL) {
            $this->statement .= ' '.$this->condition;
        }

        if ($this->order !== NULL) {
            $this->statement .= ' '.$this->order;
        }

        if ($this->limit !== NULL) {
            $this->statement .= ' '.$this->limit;
        }
    }

    public function execute($prepare = NULL) {
        $prepare or $prepare = $this->statement;
        $statement = $this->db->prepare($prepare);
        foreach ($this->params as $k => $v) {
            $statement->bindValue($k, $v);
        }
        $statement->execute();
        return $statement;
    }

    public function where($condition) {
        $this->condition = 'where ';
        if (is_string($condition)) {
            $this->condition .= $condition;
        }

        if (is_array($condition)) {
            $length = count($condition);
            $i = 0;
            foreach ($condition as $k => $v) {
                $i++;
                if ($i == $length) {
                    $this->condition .= "$k = '$v'";
                }else {
                    $this->condition .= "$k = '$v'&&";
                }
            }
        }
        return $this;
    }

    public function limit($limit) {
        $this->limit = 'limit '.$limit;
        return $this;
    }

    public function order($order) {
        $this->order = 'order by '.$order;
        return $this;
    }

    public function save(){
        if($this->verified !== true && empty($this->errors)){
            $this->validate();
        }

        if($this->verified !== true){

        }else{
            // if($this->)
        }
    }

    public function getColumn() {
        $statement = 'show columns from '.$this->table;
        $result = $this->db->query($statement);
        $columns = $result->FetchAll(PDO::FETCH_ASSOC);
        var_dump($columns);
        foreach ($columns as $v) {
            $this->columns[] = $v['Field'];
            if($v['Key'] == 'PRI'){
                $this->primaryKey = $v['Field'];
            }
        }
    }

    public function getTableName() {
        $class = get_class($this);
        $classPath = explode('\\', $class);
        $table = end($classPath);
        $strArr = str_split($table);
        foreach ($strArr as $k => $v) {
            if ($k !== 0) {
                if ($v === strtoupper($v)) {
                    $strArr[$k] = '_'.strtolower($v);
                }
            }else {
                $strArr[$k] = strtolower($v);
            }
        }
        $this->table = $this->tablePrefix.implode($strArr);
    }
}