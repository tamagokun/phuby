<?php
namespace Phuby;

class Object extends Module {
    
    public $class;
    public $instance_variables;
    public $superclass;
    
    function __clone() {
        $this->send_array('cloned');
    }
    
    function __construct($arguments = null) {
        $this->class = get_class($this);
        $class = $this->class;
        $this->instance_variables = $class::properties();
        $this->superclass = array_pop(class_parents($this->class));
        if ($this->respond_to('initialize')) {
            $arguments = func_get_args();
            $this->send_array('initialize', $arguments);
        }
    }
    
    function __destruct() {
        if ($this->respond_to('finalize')) $this->send_array('finalize');
    }
    
    function respond_to($method) {
        $class = $this->class;
        $methods = $class::methods();
        return in_array($method, get_class_methods($this->class)) || (in_array($method, array_keys($methods)) && !empty($methods[$method]));
    }
    
    function send_array($method, $arguments = array()) {
    		$class = $this->class;
        $methods = $class::methods();
        if (!$this->respond_to($method)) {
            trigger_error('Undefined method '.$this->class.'::'.$method.'()', E_USER_ERROR);
        } else if (!isset($methods[$method]) || empty($methods[$method])) {
            $result = call_user_func_array(array($this, $method), $arguments);
            return $result;
        } else {
            $class = $methods[$method][0][0];
            $class_method = $methods[$method][0][1];
            switch(count($arguments))
						{
							case 0: $result = &$class::$class_method();break;
							case 1: $result = &$class::$class_method($arguments[0]);break;
							case 2: $result = &$class::$class_method($arguments[0],$arguments[1]);break;
							case 3: $result = &$class::$class_method($arguments[0],$arguments[1],$arguments[2]);break;
						}
            return $result;
        }
    }
    
    function super($arguments = null) {
        $arguments = func_get_args();
        $caller = array_pop(array_slice(debug_backtrace(), 1, 1));
        
        if (empty($caller)) {
            trigger_error($this->class.'::super() must be called from inside of an instance method', E_USER_ERROR);
        } else {
        		$class = $this->class;
            $methods = &$class::methods();
            $aliases = $class::aliases();
            $method = $caller['function'];
            foreach (array_reverse($aliases) as $alias) {
                if ($alias[1] == $method) {
                    $method = $alias[0];
                    break;
                }
            }
            if (isset($methods[$method]) && !empty($methods[$method])) {
                $callee = array_shift($methods[$method]);
                $result = $this->send_array($method, $arguments);
                array_unshift($methods[$method], $callee);
            } else {
                //eval('$result = &'.build_function_call(array(get_parent_class($this), $method), $arguments).';');
            		$class = get_parent_class($this);
            		switch(count($arguments))
								{
									case 0: $result = &$class::$method();break;
									case 1: $result = &$class::$method($arguments[0]);break;
									case 2: $result = &$class::$method($arguments[0],$arguments[1]);break;
									case 3: $result = &$class::$method($arguments[0],$arguments[1],$arguments[2]);break;
								}
            }
            return $result;
        }
    }
    
    public function __call($method, $arguments = array()) {
        $result = $this->send_array('method_missing', array($method, $arguments));
        return $result;
    }
    
    public static function __callStatic($method, $arguments = array()) {
	    $result = null;
	    $methods = call_class_method(get_called_class(),'methods');
	    if(isset($methods[$method]))
	    	$result = call_class_method($methods[$method][0][0],$method,$arguments);
	    return $result;
    }
    
    public function __get($property) {
        if (isset($this->$property)) {
            return $this->instance_variables[$property];
        } else {
            $this->instance_variables = array_merge(call_class_method($this->class, 'properties'), $this->instance_variables);
            if (isset($this->instance_variables[$property])) {
                return $this->instance_variables[$property];
            } else {
                //trigger_error('Undefined property $'.$property, E_USER_ERROR);
            }
        }
        return null;
    }
    
    public function __isset($property) {
        return isset($this->instance_variables[$property]);
    }
    
    public function __set($property, $value) {
        $this->instance_variables[$property] = $value;
    }
    
    public function __unset($property) {
        unset($this->instance_variables[$property]);
    }
    
}

abstract class ObjectMethods {
    
    function cloned() { }
    
    function dup() {
        return clone $this;
    }
    
    function inspect() {
        ob_start();
        print_r($this);
        return ob_get_clean();
    }
    
    function instance_variables() {
        return $this->instance_variables;
    }
    
    function is_a($class) {
        return $this instanceof $class;
    }
    
    function method_missing($method, $arguments = array()) {
        $result = $this->send_array($method, $arguments);
        return $result;
    }
    
    function send($method, $arguments = null) {
        $arguments = func_get_args();
        $method = array_shift($arguments);
        $result = $this->send_array($method, $arguments);
        return $result;
    }
    
}

Object::extend('Phuby\ObjectMethods', 'Phuby\Delegator');

Object::alias_method('is_an', 'is_a');