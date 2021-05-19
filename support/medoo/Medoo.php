<?php

namespace support\medoo;

use PDOStatement;

class Medoo
{
    private $medoo;

    public function __construct($options)
    {
        $this->medoo = new \Medoo\Medoo($options);
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

    public function id(string $name = null): ?string
    {
        return $this->medoo->id($name);
    }

    public function query(string $statement, array $map = []): ?PDOStatement
    {
        return $this->medoo->query($statement, $map);
    }
}