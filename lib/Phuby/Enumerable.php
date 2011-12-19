<?php
namespace Phuby;

class Enumerator extends Object implements \Iterator, \ArrayAccess, \Countable {
    
    public $array;
    public $default;
    public $valid = false;
    
    function initialize($array = array(), $default = null) {
        $this->array = $array;
        $this->default = $default;
    }
    
    function count() {
        return count($this->array);
    }
    
    function current() {
        return current($this->array);
    }
    
    function getIterator() {
        return $this;
    }
    
    function key() {
        return key($this->array);
    }
    
    function offsetExists($offset) {
        return isset($this->array[$offset]);
    }
    
    function offsetGet($offset, $default = null) {
        if (is_null($default)) $default = $this->default;
        return ($this->offsetExists($offset)) ? $this->array[$offset] : $default;
    }
    
    function offsetSet($offset, $value) {
        $this->array[$offset] = $value;
    }
    
    function offsetUnset($offset) {
        unset($this->array[$offset]);
    }
    
    function next() {
        $this->valid = (next($this->array) !== false);
    }
    
    function rewind() {
        $this->valid = (reset($this->array) !== false);
    }
    
    function valid() {
        return $this->valid;
    }

}

abstract class Enumerable extends Enumerator { }

abstract class EnumerableMethods {
    
    function all($block) {
        foreach ($this as $key => $value) if (!evaluate_block($block, array(&$value,&$key))) return false;
        return true;
    }
    
    function any($block) {
        foreach ($this as $key => $value) if (evaluate_block($block, array(&$value,&$key))) return true;
        return false;
    }
    
    function clear() {
        $this->array = array();
        return $this;
    }
    
    function collect($block) {
        $result = new Arr;
        foreach ($this as $key => $value) $result[] = evaluate_block($block, array(&$value,&$key));
        return $result;
    }
    
    function detect($block) {
        foreach ($this as $key => $value) if (evaluate_block($block, array(&$value,&$key))) return $value;
        return null;
    }
    
    function filter($callback = null) {
    		$class = $this->class;
    		return $class::new_instance(array_filter($this->array,$callback));
    }
    
    function has_key($key) {
        return $this->keys()->has_value($key);
    }
    
    function has_value($value) {
        return in_array($value, $this->array);
    }
    
    function index($object) {
        foreach ($this as $key => $value) if ($value == $object) return $key;
        return null;
    }
    
    function inject($object, $block) {
        foreach ($this as $key => $value) $object = evaluate_block($block, array(&$value,&$key,&$object));
        return $object;
    }
    
    function keys() {
        return array_keys($this->array);
    }
    
    function none($block) {
        foreach ($this as $key => $value) if (evaluate_block($block, array(&$value,&$key))) return false;
        return true;
    }
    
    function partition($block) {
    		$class = $this->class;
    		$passed = $class::new_instance();
    		$failed = $class::new_instance();
        foreach ($this as $key => $value) {
            if (evaluate_block($block, array(&$value,&$key))) {
                $passed[$key] = $value;
            } else {
                $failed[$key] = $value;
            }
        }
        return new Arr(array($passed, $failed));
    }
    
    function reject($block) {
    		$class = $this->class;
    		$result = $class::new_instance();
        foreach ($this as $key => $value) if (!evaluate_block($block, array(&$value,&$key))) $result[$key] = $value;
        return $result;
    }
    
    function rindex($object) {
        $index = null;
        foreach ($this as $key => $value) if ($value == $object) $index = $key;
        return $index;
    }
    
    function replace($array) {
        if ($array instanceof Enumerable) $array = $array->array;
        $this->array = $array;
        return $this;
    }
    
    function select($block) {
    		$class = $this->class;
		    $result = $class::new_instance();
        foreach ($this as $key => $value) if (evaluate_block($block, array(&$value,&$key))) $result[$key] = $value;
        return $result;
    }
    
    function shift() {
        return empty($this->array) ? $this->default : array_shift($this->array);
    }
    
    function sort($sort_flags = null) {
        if (is_null($sort_flags)) $sort_flags = SORT_REGULAR;
        $array = $this->array;
        asort($array, $sort_flags);
        $class = $this->class;
        return $class::new_instance($array);
    }
    
    function sort_by($block, $sort_flags = null) {
    		$sorted = $this->inject(new Hash, function($v,$k,$o) use($block) { $o[$k] = evaluate_block($block,array(&$v,&$k)); return $o; })->sort($sort_flags);
    		$class = $this->class;
    		$result = $class::new_instance();
        foreach ($sorted as $key => $value) $result[$key] = $this[$key];
        return $result;
    }
    
    function to_native_a() {
        $result = $this->array;
        foreach ($result as $key => $value) {
            if ($value instanceof Enumerable) {
                $result[$key] = $value->to_native_a();
            }
        }
        return $result;
    }
    
    function values() {
        return array_values($this->array);
    }
    
    function values_at($keys) {
        $keys = func_get_args();
        $result = new Arr;
        foreach ($keys as $key) $result[] = $this[$key];
        return $result;
    }
    
}

Enumerable::extend('Phuby\EnumerableMethods');

Enumerable::alias_method('at', 'offsetGet');
Enumerable::alias_method('fetch', 'offsetGet');
Enumerable::alias_method('length', 'count');
Enumerable::alias_method('map', 'collect');
Enumerable::alias_method('size', 'count');
Enumerable::alias_method('store', 'offsetSet');