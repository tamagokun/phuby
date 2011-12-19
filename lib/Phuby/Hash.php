<?php
namespace Phuby;

class Hash extends Enumerable { }

abstract class HashMethods {
    
    function invert() {
    		$class = $this->class;
    		return $class::new_instance(array_flip($this->array));
    }
    
    function merge($hash) {
        if ($hash instanceof Enumerable) $hash = $hash->array;
        $class = $this->class;
        return $class::new_instance(array_merge($this->array,$hash));
    }
    
    function shift() {
        return (empty($this->array)) ? $this->super() : new Arr(array($this->keys()->shift(), $this->super()));
    }
    
    function to_a() {
    		return $this->inject(new Arr, function($v,$k,$o) { $o[] = new Arr(array($key,$value)); return $o;});
    }
    
    function update($hash) {
        if ($hash instanceof Enumerable) $hash = $hash->array;
        $this->array = array_merge($this->array, $hash);
        return $this;
    }
    
}

Hash::extend('Phuby\HashMethods');

Hash::alias_method('flip', 'invert');