<?php
namespace L\db;
use PDO, Exception;;
class Model {

    /**
     * Database connect handle
     * @var PDO Object
     */
    public $db;

    /**
     * model operate mode, default insert
     * @var string
     */
    public $mode;
    
    /**
     * Database table name
     * @var string
     */
    public $table;

    /**
     * Validate errors
     * @var array
     */
    public $errors = [];

    /**
     * sql statement bind params
     * @var array
     */
    public $params = [];

    /**
     * sql statement
     * @var string
     */
    public $statement;

    /**
     * table prefix
     * @var string
     */
    public $tablePrefix = '';

    /**
     * table columns
     * @var array
     */
    public $columns;

    /**
     * table primary key
     * @var string
     */
    private $primaryKey;

    /**
     * sql statement condition
     * @var string
     */
    protected $_condition = [
        'where' => NULL,
        'limit' => NULL,
        'order' => NULL,
        'join'  => NULL,
    ];

    /**
     * table columns attributes and value
     * @var array
     */
    public $attribute = [];

    /**
     * ensure attributes was verified
     * @var boolean
     */
    public $verified = false;

    public function __construct($mode = 'insert') {
        $db_config = l()->db;          // get database config
        static $db;
        if (!isset($db)) {
            $db = new PDO($db_config['connectionString'], $db_config['username'], $db_config['password']);
        }
        $this->db = $db;
        $this->tablePrefix = $db_config['tablePrefix'];
        $this->mode = $mode;
        $this->getTableName();  
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

    protected function init() {

    }

    /**
     * set attributes
     * @param array $attribute
     */
    public function setAttributes($attribute){
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * findAll description
     * @param  array $params
     * @return find results
     */
    public function findAll($params = []) {
        $this->params = $params;
        $this->buildSelectStatement();
        $resultArray = $this->execute()->fetchAll(PDO::FETCH_ASSOC);
        $this->mode = 'update';
        $results = [];
        foreach ($resultArray as $k => $v) {
            $results[$k] = clone $this;
            $results[$k]->attribute = $v;
        }
        return $results;
    }

    /**
     * find description
     * @param  array $params
     * @return $this
     */
    public function find($params = []) {
        $this->params = $params;
        $this->limit(1);
        $this->buildSelectStatement();
        $this->attribute = $this->execute()->fetch(PDO::FETCH_ASSOC);
        $this->mode = 'update';
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
        if(empty($this->errors)){
            $this->verified = true;
        }

        return $this;
    }

    public function buildSelectStatement() {
        $this->statement  = 'select * from ';
        $this->statement .= $this->table;
        if ($this->condition['where'] !== NULL) {
            $this->statement .= ' '.$this->condition['where'];
        }

        if ($this->condition['order'] !== NULL) {
            $this->statement .= ' '.$this->condition['order'];
        }

        if ($this->condition['limit'] !== NULL) {
            $this->statement .= ' '.$this->condition['limit'];
        }
    }

    public function execute($prepare = NULL) {
        $prepare or $prepare = $this->statement;
        $statement = $this->db->prepare($prepare);
        $statement->execute($this->params);
        return $statement;
    }

    public function where($condition) {
        $this->condition['where'] = 'where ';
        if (is_string($condition)) {
            $this->condition['where'] .= $condition;
        }

        if (is_array($condition)) {
            $length = count($condition);
            $i = 0;
            foreach ($condition as $k => $v) {
                $i++;
                if ($i == $length) {
                    $this->condition['where'] .= "$k = '$v'";
                }else {
                    $this->condition['where'] .= "$k = '$v'&&";
                }
            }
        }
        return $this;
    }

    private function limit($limit) {
        $this->limit = 'limit '.$limit;
        return $this;
    }

    private function order($order) {
        $this->order = 'order by '.$order;
        return $this;
    }

    public function save(){
        if($this->verified !== true && empty($this->errors)){
            $this->validate();
        }
        
        if($this->verified !== true){
            return false;
        }

        array_walk($this->attribute, function($v, $k){
            in_array($k, $this->columns) and $this->params[':'.$k] = $v;
        });
        var_dump($this->params);

        $primaryKey = $this->primaryKey;
        if(isset($this->$primaryKey) && $this->$primaryKey){
            return $this->update();
        }else{
            return $this->insert();
        }
    }

    public function insert(){
        $columns = '';
        $params  = '';
        foreach ($this->columns as $v) {
            if($this->primaryKey == $v){
                continue;
            }
            $columns .= $v.',';
            $params  .= ':'.$v.',';
        }
        $columns = rtrim($columns, ',');
        $params = rtrim($params, ',');
        $this->statement = 'insert into '.$this->table.' ('.$columns.') values ('.$params.')';
        $this->execute();
    }

    public function update(){

    }

    private function getColumn() {
        $statement = 'show columns from '.$this->table;
        $result = $this->db->query($statement);
        $columns = $result->FetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $v) {
            $this->columns[] = $v['Field'];
            if($v['Key'] == 'PRI'){
                $this->primaryKey = $v['Field'];
            }
        }
    }

    private function getTableName() {
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