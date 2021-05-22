<?php

namespace support\medoo;

use Medoo\Raw;
use PDOStatement;

/**
 * Class Medoo
 * @method // PDOStatement insert(string $table, array $values, string $primary_key = null) Insert one or more records into the table.
 * @method // get(string $table, $join = null, $columns = null, $where = null) Get only one record from the table.
 * @method // array select(string $table, $join, $columns = null, $where = null) Select data from the table.
 * @method // PDOStatement delete(string $table, array $where) Delete data from the table.
 * @method // PDOStatement update(string $table, $data, $where = null) Modify data from the table.
 * @method // PDOStatement replace(string $table, array $columns, $where = null) Replace old data with a new one.
 * @method // int count(string $table, $join = null, $column = null, $where = null) Count the number of rows from the table.
 * @method // array rand(string $table, $join = null, $columns = null, $where = null) Randomly fetch data from the table.
 * @method // string max(string $table, $join, $column = null, $where = null) Get the maximum value of the column.
 * @method // string min(string $table, $join, $column = null, $where = null) Get the minimum value of the column.
 * @method // string sum(string $table, $join, $column = null, $where = null) Calculate the total value of the column.
 * @method // string avg(string $table, $join, $column = null, $where = null) Calculate the average value of the column.
 * @method string id(string $name = null) Return the ID for the last inserted row.
 * @method action(callable $actions) Start a transaction.
 * @method bool has(string $table, $join, $where = null) Determine whether the target data existed from the table.
 * @method PDOStatement drop(string $table) Drop a table.
 * @method PDOStatement create(string $table, $columns, $options = null) Create a table.
 * @method string columnQuote(string $column) Quote column name for use in a query.
 * @method string tableQuote(string $table) Quote table name for use in a query.
 * @method string quote(string $string) Quote a string for use in a query.
 * @method Raw raw(string $string, array $map = []) Build a raw object.
 * @method PDOStatement exec(string $statement, array $map = [], callable $callback = null) Execute the raw statement.
 * @method // PDOStatement query(string $statement, array $map = []) Execute customized raw statement.
 * @method self debug() Enable debug mode and output readable statement string.
 * @method beginDebug() Enable debug logging mode.
 * @method array debugLog() Disable debug logging and return all readable statements.
 * @method string last() Return the last performed statement.
 * @method array log() Return all executed statements.
 * @method array info() Get information about the database connection.
 * @package support\medoo
 */
class Medoo
{
    private $medoo;

    public $pdo;

    public function __construct($options)
    {
        $this->medoo = new \Medoo\Medoo($options);
        $this->pdo = $this->medoo->pdo;
    }

    /**
     * @param string $table
     * @param array $values
     * @param string|null $primary_key
     * @return PDOStatement|null
     */
    public function insert(string $table, array $values, string $primary_key = null): ?PDOStatement
    {
        return $this->medoo->insert($table, $values, $primary_key);
//        Redis::getInstance([])->setHash($addMysqlId,$info);
//        Redis::getInstance([])->setZSet($addMysqlId,$addMysqlId,'');
    }

    /**
     * @param string $table
     * @param null $join
     * @param null $columns
     * @param null $where
     * @return false|mixed
     */
    public function get(string $table, $join = null, $columns = null, $where = null)
    {
        return $this->medoo->get($table, $join, $columns, $where);
//        Redis::getInstance([])->getHash($v);
    }

    /**
     * @param string $table
     * @param array $join
     * @param null $columns
     * @param null $where
     * @return array|null
     */
    public function select(string $table, $join, $columns = null, $where = null): ?array
    {
        return $this->medoo->select($table, $join, $columns, $where);

//        $total = Redis::getInstance([])->getZSetTotal('List'); //数据总量
//        if (!empty($key)){
//            $this->key = $key;
//        }
//        $name = $this->redisPrefix.$this->key;
//        $total = $this->objRedis->zCard($name);
//        return $total;
//        $tmpList = Redis::getInstance([])->getZSetList('List',$offset,$end);
//        if (!empty($key)){
//            $this->key = $key;
//        }
//        $name = $this->redisPrefix.$this->key;
//        $list = $this->objRedis->zRange($name,$offset,$end);
//        return $list;
//        foreach {Redis::getInstance([])->getHash($v);}
//        $res = array();
//        if (is_string($keys)){
//            $res = $this->objRedis->hGetAll($this->redisPrefix.'_'.$keys);
//        }
//        if (is_array($keys)){
//            foreach ($keys as $v){
//                $res[$v] = $this->objRedis->hGetAll($this->redisPrefix.'_'.$v);
//            }
//        }
//        return $res;
    }

    /**
     * @param string $table
     * @param array $where
     * @return PDOStatement|null
     */
    public function delete(string $table, array $where): ?PDOStatement
    {
        return $this->medoo->delete($table, $where);
    }

    /**
     * @param string $table
     * @param $data
     * @param null $where
     * @return PDOStatement|null
     */
    public function update(string $table, $data, $where = null): ?PDOStatement
    {
        return $this->medoo->update($table, $data, $where);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param null $where
     * @return PDOStatement|null
     */
    public function replace(string $table, array $columns, $where = null): ?PDOStatement
    {
        return $this->medoo->replace($table, $columns, $where);
    }

    /**
     * @param string $table
     * @param null $join
     * @param null $column
     * @param null $where
     * @return int|null
     */
    public function count(string $table, $join = null, $column = null, $where = null): ?int
    {
        return $this->medoo->count($table, $join, $column, $where);
    }

    /**
     * @param string $table
     * @param null $join
     * @param null $columns
     * @param null $where
     * @return array
     */
    public function rand(string $table, $join = null, $columns = null, $where = null): array
    {
        return $this->medoo->rand($table, $join, $columns, $where);
    }

    public function max(string $table, $join, $column = null, $where = null): ?string
    {
        return $this->medoo->max($table, $join, $column, $where);
    }

    public function min(string $table, $join, $column = null, $where = null): ?string
    {
        return $this->medoo->min($table, $join, $column, $where);
    }

    public function avg(string $table, $join, $column = null, $where = null): ?string
    {
        return $this->medoo->avg($table, $join, $column, $where);
    }

    public function sum(string $table, $join, $column = null, $where = null): ?string
    {
        return $this->medoo->sum($table, $join, $column, $where);
    }

    public function query(string $statement, array $map = []): ?PDOStatement
    {
        return $this->medoo->query($statement, $map);
    }

    public function __call($name, $args)
    {
        return $this->medoo->$name(...$args);
    }
}