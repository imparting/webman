<?php


namespace support\medoo;


use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;

class Collection implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    public $total = 0;
    public $items = [];
    public $page_size;
    public $last_page;
    public $current_page;

    public function __construct($items, $total, $page_size, $current_page)
    {
        $this->total = $total;
        $this->items = $items;
        $this->page_size = $page_size;
        $this->last_page = ceil($total / $page_size);
        $this->current_page = $current_page;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->$offset);
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }

    public function jsonSerialize(): array
    {
        return (array)$this;
    }
}
