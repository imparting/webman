<?php

namespace support\medoo;

use ArrayAccess;
use JsonSerializable;
use PDO;

class Model implements ArrayAccess, JsonSerializable
{

    // 数据库链接名
    protected $connection = 'default';

    // 表名
    protected $table = '';

    // 主键
    protected $primary_key = 'id';

    // 主键自增
    protected $auto_increment = true;

    // 表列数组
    protected $columns = [];

    /**
     * 关联模型
     * @var array
     * $foreign = [
     * 'table1' => ['-', Table1::class, 'table1_id', 'id'], // one to one
     * 'table2' => ['*', Table2::class, 'table2_id', 'id'], // ont to many
     * ];
     */
    protected $foreign = [];

    // 软删除字段
    protected $soft_delete;

    // 软删除字段
    protected $deleted_at;

    // 创建时间字段名
    protected $created_at;

    // 更新时间字段名
    protected $updated_at;

    // 时间格式化
    protected $date_format = 'U';

    // 时间获取方式 db:数据库时间 cli:客户端程序时间
    protected $date_model = 'db';

    // 模型数据
    private $data = [];

    // 模型更改数据
    private $changes = [];

    // 字段过滤器
    private $filters = [];

    // 只读
    protected $read_only = false;

    /**
     * 保存数据到模型
     * @return bool
     * @throws MedooErrorException
     */
    public function save(): bool
    {
        if (empty($this->data)) {
            if (!$this->auto_increment && !isset($this->changes[$this->primary_key]))
                throw new MedooErrorException("Primary key is required.");
            if ($this->created_at) $this->changes[$this->created_at] = $this->getCurrentTime();
            if ($this->getDatabase()->insert($this->table, $this->changes)->rowCount()) {
                $this->changes = [];
                $this->find($this->getDatabase()->id());
                return true;
            } else {
                return false;
            }
        } elseif (!empty($this->changes)) {
            if ($this->read_only) {
                throw new MedooErrorException(static::class . " read only");
            }
            $changes = array_merge($this->data, $this->changes);
            if ($this->updated_at) $changes[$this->updated_at] = $this->getCurrentTime();
            $rowCount = $this->getDatabase()
                ->update($this->table, $changes, [$this->primary_key => $this->data[$this->primary_key]])
                ->rowCount();
            if ($rowCount) {
                $this->changes = [];
                $this->find($this->data[$this->primary_key]);
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * 更新操作，返回影响条数
     * @throws MedooErrorException
     */
    public function update(): int
    {
        if ($this->read_only) {
            throw new MedooErrorException("'" . static::class . "' read only!");
        }
        if (empty($this->changes)) {
            return 0;
        } else {
            $changes = $this->changes;
            if ($this->updated_at) $changes[$this->updated_at] = $this->getCurrentTime();
            $rowCount = $this->getDatabase()
                ->update($this->table, $changes, [$this->primary_key => $this->data[$this->primary_key]])
                ->rowCount();
            if ($rowCount) {
                $this->changes = [];
                $this->find($this->data[$this->primary_key]);
            }
            return $rowCount;
        }
    }

    /**
     * 删除模型
     * @return bool
     * @throws MedooErrorException
     */
    public function delete(): bool
    {
        if ($this->read_only) {
            throw new MedooErrorException(static::class . " read only");
        }
        if (!empty($this->data) && $id = $this->data[$this->primary_key] ?? null) {
            if ($this->soft_delete) {
                $deleted_update_data = [$this->soft_delete => 1];
                if ($this->deleted_at) $deleted_update_data[$this->deleted_at] = $this->getCurrentTime();
                $rowCount = $this->getDatabase()
                    ->update($this->table, $deleted_update_data, [$this->primary_key => $id])
                    ->rowCount();
            } else {
                $rowCount = $this->getDatabase()
                    ->delete($this->table, [$this->primary_key => $id])
                    ->rowCount();
            }
            if ($rowCount) {
                $this->destruct();
                return true;
            } else {
                return false;
            }
        } else {
            throw new MedooErrorException("There is no data to delete");
        }
    }

    /**
     * 按主键查询
     * @param $id
     * @return $this
     * @throws MedooErrorException
     */
    public function find($id): self
    {
        $where = [$this->primary_key => $id];
        if ($this->soft_delete) $where[$this->soft_delete] = 0;
        $data = $this->getDatabase()->get($this->table, $this->columns, $where) ?? [];
        $this->loadData($data);
        return $this;
    }

    /**
     * 查询满足条件的第一个数据
     * @param null $where
     * @return $this
     * @throws MedooErrorException
     */
    public function first($where = null): self
    {
        if ($this->soft_delete) $where[$this->soft_delete] = 0;
        $data = $this->getDatabase()->get($this->table, $this->columns, $where) ?? [];
        $this->loadData($data);
        return $this;
    }

    /**
     * 获取所有记录
     * @param null $where
     * @return array|null
     * @throws MedooErrorException
     */
    public function all($where = null): ?array
    {
        //$collection = new MedooCollection();
        if ($this->soft_delete) $where[$this->soft_delete] = 0;
        $items = $this->getDatabase()->select($this->table, $this->columns, $where);
        if ($items) {
            foreach ($items as $key => $item) {
                $static = new static();
                $static->loadData($item);
                $items[$key] = $static;
            }
            return $items;
        } else {
            return [];
        }
        //$collection->items = $items;
        //$collection->total = count($items);
        //return $collection;
    }

    /**
     * 分页
     * @param $page_size
     * @param $where
     * @param string $page_name
     * @param null $page
     * @return Collection
     * @throws MedooErrorException
     */
    public function paginate($page_size, $where = null, $page_name = 'page', $page = null): Collection
    {
        if ($this->soft_delete) $where[$this->soft_delete] = 0;
        $count = $this->getDatabase()->count($this->table, $where);

        $page = $page ?: $this->resolveCurrentPage($page_name);
        $offset = (max(0, $page - 1)) * $page_size;
        $where['LIMIT'] = [$offset, $page_size];
        return new Collection($this->all($where), $count, $page_size, $page);
    }

    /**
     * @param string $page_name
     * @return int
     */
    private function resolveCurrentPage($page_name = 'page'): int
    {
        $page = request()->input($page_name);
        if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int)$page >= 1) {
            return (int)$page;
        }
        return 1;
    }

    /**
     * 切片查询
     * $user->chunk(100, function($users){
     *      foreach($users=>$user){
     *          echo $user->username
     *      }
     * });
     * $user->chunk(100, 'id', function($users){
     *      foreach($users=>$user){
     *          echo $user->username
     *      }
     * });
     * $user->chunk(100, ['status' => 1, 'ORDER' => ['id' => 'ASC'], function($users){
     *      foreach($users=>$user){
     *          echo $user->username
     *      }
     * });
     * @param int $count
     * @param null|string|array $where
     * @param callable|null $callback
     * @return bool
     * @throws MedooErrorException
     */
    public function chunk(int $count, $where, $callback = null): bool
    {
        if (is_null($callback) && is_callable($where)) {
            $callback = $where;
            $where = null;
        }
        //$where['ORDER'] = [$this->primary_key => "ASC"];
        if (is_string($where)) {
            $where = ['ORDER' => [$where => "ASC"]];
        }
        if ($this->soft_delete) $where[$this->soft_delete] = 0;
        $page = 1;
        do {
            $offset = (max(0, $page - 1)) * $count;
            $where['LIMIT'] = [$offset, $count];
            $results = $this->all($where);
            $countResults = count($results);
            if ($countResults == 0) {
                break;
            }
            if ($callback($results, $page) === false) {
                return false;
            }
            unset($results);
            $page++;
        } while ($countResults == $count);
        return true;
    }

    /**
     * 加载关联模型
     * @throws MedooErrorException
     */
    public function loadForeign()
    {
        if (isset($this->data[$this->primary_key]) && !empty($this->foreign)) {
            $names = array_keys($this->foreign);
            foreach ($names as $name) {
                $this->__get($name);
            }
        }
    }

    /**
     * 模型填充数据
     * @param array $data
     * @throws MedooErrorException
     */
    public function loadData(array $data)
    {
        if (empty($this->data)) {
            if (!empty($data)) {
                foreach ($this->columns as $column) {
                    $this->data[$column] = $data[$column] ?? null;
                }
            }
        } else {
            throw new MedooErrorException('The data has been filled in');
        }
    }

    /**
     * 判断列名是否存在
     * @param $name
     * @return bool
     */
    private function checkColumnExists($name): bool
    {
        return in_array($name, $this->columns);
    }

    /**
     * 放弃修改
     * @return $this
     */
    public function reset(): self
    {
        $this->changes = [];
        return $this;
    }

    private function destruct()
    {
        $this->data = [];
        $this->changes = [];
    }

    /**
     * 重新载入模型
     * @return $this
     * @throws MedooErrorException
     */
    public function refresh(): self
    {
        $this->changes = [];
        $primaryKeyId = $this->data[$this->primary_key] ?? null;
        $this->data = [];
        if ($primaryKeyId) $this->find($primaryKeyId);
        return $this;
    }

    /**
     * 设置模型属性值
     * @param $name
     * @param $value
     * @throws MedooErrorException
     */
    public function __set($name, $value)
    {
        if ($this->checkColumnExists($name)) {
            if ($this->read_only) {
                throw new MedooErrorException(static::class . " read only");
            }
            if (empty($this->data)) {
                $this->changes[$name] = $value;
            } else {
                if ($this->primary_key == $name) {
                    throw new MedooErrorException("Primary Key read only and can't set");
                }
                if ($this->data[$name] !== $value) {
                    $this->changes[$name] = $value;
                }
            }
        } else {
            throw new MedooErrorException("'{$name}' parameter is invalid");
        }
    }

    /**
     * 获取模型成员属性
     * @param $name
     * @return $this|array|mixed|null
     * @throws MedooErrorException
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->filter($name, $this->changes[$name] ?? $this->data[$name]);
        } else {
            if (in_array($name, array_keys($this->foreign))
                && isset($this->foreign[$name][0], $this->foreign[$name][1])
                && in_array($this->foreign[$name][0], ['-', '*'])
                && class_exists($this->foreign[$name][1])
            ) {
                $oneOrMany = $this->foreign[$name][0];
                /**
                 * @var Model $foreignClass
                 */
                $foreignClass = new $this->foreign[$name][1];
                $foreign_key = $this->foreign[$name][2] ?? $foreignClass->table . '_id';
                $local_key = $this->foreign[$name][3] ?? $this->primary_key;
                //if ($foreignClass->checkColumnExists($foreign_key) && $this->checkColumnExists($local_key)) {
                switch ($oneOrMany) {
                    case '-':// one to one
                        $this->data[$name] = $foreignClass->first([$foreign_key => $this->$local_key]);
                        break;
                    case '*':// one to many
                        $this->data[$name] = $foreignClass->all([$foreign_key => $this->$local_key]);
                        break;
                }
                return $this->data[$name];
//                } else {
//                    throw new MedooErrorException("'{$foreign_key}' or '{$local_key}' key config error");
//                }
            } else {
                throw new MedooErrorException("'{$name}' foreign config error");
            }
        }
    }

    /**
     * 跟字段添加过滤器
     * @param $field
     * @param callable $callable
     * @param mixed ...$callables
     */
    public function addFilter($field, callable $callable, ...$callables)
    {
        $this->filters[$field][] = $callable;
        if (!empty($callables)) {
            foreach ($callables as $ca) {
                $this->filters[$field][] = $ca;
            }
        }
    }

    /**
     * 执行过滤器
     * @param $field
     * @param $value
     * @return mixed
     */
    private function filter($field, $value)
    {
        if (isset($this->filters[$field])) {
            foreach ($this->filters[$field] as $filter) {
                $value = $filter($value);
            }
        }
        return $value;
    }

    /**
     * 获取当前时间
     * @return false|mixed|string
     * @throws MedooErrorException
     */
    public function getCurrentTime()
    {
        if ('cli' == $this->date_model) {
            return date($this->date_format);
        } elseif ('db' == $this->date_model) {
            if ('Y-m-d H:i:s' == $this->date_format) return $this->getCurrentDbTime();
            return date($this->date_format, strtotime($this->getCurrentDbTime()));
        }
        return null;
    }

    /**
     * 获取当前数据库时间
     * @return mixed
     * @throws MedooErrorException
     */
    private function getCurrentDbTime()
    {
        return $this->getDatabase()
            ->query('SELECT CURRENT_TIMESTAMP')
            ->fetch(PDO::FETCH_NUM)[0];
    }

    /**
     * @return Medoo
     * @throws MedooErrorException
     */
    private function getDatabase(): Medoo
    {
        return Db::getInstance($this->connection);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws MedooErrorException
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset($offset)
    {
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        if (!empty($this->filters) && !empty($this->data)) {
            $data = $this->data;
            foreach ($this->filters as $filed => $filters) {
                foreach ($filters as $filter) {
                    $data[$filed] = $filter($data[$filed]);
                }
            }
            return $data;
        } else {
            return (array)$this->data;
        }
    }
}
