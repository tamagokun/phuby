<?php

class Enumerator extends Object implements Iterator, ArrayAccess, Countable {
    
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
    
    function offsetGet($offset) {
        return ($this->offsetExists($value)) ? $this->array[$offset] : $this->default;
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

abstract class EnumerableMethods {
    
    function all($block) {
        foreach ($this as $key => $value) if (!evaluate_block($block, get_defined_vars())) return false;
        return true;
    }
    
    function any($block) {
        foreach ($this as $key => $value) if (evaluate_block($block, get_defined_vars())) return true;
        return false;
    }
    
    function clear() {
        $this->array = array();
        return $this;
    }
    
    function collect($block) {
        $result = new A;
        foreach ($this as $key => $value) $result[] = evaluate_block($block, get_defined_vars());
        return $result;
    }
    
    function detect($block) {
        foreach ($this as $key => $value) if (evaluate_block($block, get_defined_vars())) return $value;
        return null;
    }
    
    function includes($object) {
        return in_array($object, $this->array);
    }
    
    function inject($object, $block) {
        foreach ($this as $key => $value) $object = evaluate_block($block, get_defined_vars());
        return $object;
    }
    
    function none($block) {
        foreach ($this as $key => $value) if (evaluate_block($block, get_defined_vars())) return false;
        return true;
    }
    
    function partition($block) {
        $passed = $this->new_instance();
        $failed = $this->new_instance();
        foreach ($this as $key => $value) {
            if (evaluate_block($block, get_defined_vars())) {
                $passed[$key] = $value;
            } else {
                $failed[$key] = $value;
            }
        }
        return new A(array($passed, $failed));
    }
    
    function reject($block) {
        $result = $this->new_instance();
        foreach ($this as $key => $value) if (!evaluate_block($block, get_defined_vars())) $result[$key] = $value;
        return $result;
    }
    
    function select($block) {
        $result = $this->new_instance();
        foreach ($this as $key => $value) if (evaluate_block($block, get_defined_vars())) $result[$key] = $value;
        return $result;
    }
    
    function sort($sort_flags = null) {
        if (is_null($sort_flags)) $sort_flags = SORT_REGULAR;
        $array = $this->array;
        asort($array, $sort_flags);
        return $this->new_instance($array);
    }
    
    function sort_by($block, $sort_flags = null) {
        $sorted = $this->inject(new H, '$object[$key] = evaluate_block(\''.$block.'\', get_defined_vars()); return $object;')->sort($sort_flags);
        $result = $this->new_instance();
        foreach ($sorted as $key => $value) $result[$key] = $this[$key];
        return $result;
    }
    
    function to_a() {
        return new A($this->array);
    }
    
    function to_h() {
        return new H($this->array);
    }
    
}

abstract class Enumerable extends Enumerator { }

extend('Enumerable', 'EnumerableMethods');

?>