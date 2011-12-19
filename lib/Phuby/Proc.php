<?php
namespace Phuby;

class Proc extends Object { }

abstract class ProcMethods {
    
    public $binding = array();
    public $block;
    public $parameters;
    
    function initialize($block) {
        $this->block = $block;
        $this->parameters = array();
        $func = new \ReflectionFunction($this->block);
        foreach($func->getParameters() as $parameter) $this->parameters[] = $parameter->name;
    }
    
    function call($values = null) {
        $values = func_get_args();
        return $this->call_array($values);
    }
    
    function call_array($values = array()) {
        $arguments = array();
        foreach ($this->parameters as $index => $parameter) {
            if (isset($values[$index])) {
                $arguments[$parameter] = $values[$index];
            } else {
                trigger_error('Missing argument '.($index + 1).' in Proc::call()', E_USER_WARNING);
            }
        }
        return call_user_func_array($this->block,$arguments);
    }
    
}

Proc::extend('Phuby\ProcMethods');