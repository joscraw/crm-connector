<?php

namespace CRMConnector\Models;

use ArrayIterator;

/**
 * Class Collection
 * @package CRMConnector\Models
 */
class Collection implements \IteratorAggregate
{
    private $items = array();

    public function addItem($obj, $key = null)
    {
        if ($key == null) {
            $this->items[] = $obj;
        }
        else {
            $this->items[$key] = $obj;
        }
    }
    public function deleteItem($key)
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
    }
    public function getItem($key)
    {
        if (isset($this->items[$key]))
        {
            return $this->items[$key];
        }
    }
    public function keys()
    {
        return array_keys($this->items);
    }
    public function length()
    {
        return count($this->items);
    }
    public function keyExists($key)
    {
        return isset($this->items[$key]);
    }

    public function getIterator() {
        return new ArrayIterator($this->items);
    }
}