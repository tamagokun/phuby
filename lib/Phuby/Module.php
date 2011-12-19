<?php
namespace Phuby;

abstract class Module {
    
    static $mixins = array();
    
    static function alias($new, $old) {
        $class = get_called_class();
        $methods = &$class::methods();
        $methods[$new] = isset($methods[$old]) ? $methods[$old] : array(array($class, $old));
        $methods[$old]= array(array($class, $new));
    }
    
    static function alias_method($new, $old) {
        $class = get_called_class();
        $mixins = &$class::mixins();
        array_push($mixins['aliases'], array($new, $old));
        $class::alias($new,$old);
        $class::update_derived_modules();
    }
    
    static function alias_method_chain($method, $with) {
        $class = get_called_class();
        $class::alias_method($method.'_without_'.$with, $method);
        $class::alias_method($method, $method.'_with_'.$with);
    }
    
    static function aliases() {
        $class = get_called_class();
        $mixins = $class::mixins();
        return $mixins['aliases'];
    }
    
    static function ancestors() {
        $class = get_called_class();
        $mixins = $class::mixins();
        return $mixins['ancestors'];
    }
    
    static function extend($modules) {
        $modules = (is_array($modules)) ? $modules : func_get_args();
        $class = get_called_class();
        $mixins = &$class::mixins();
        foreach ($modules as $module) {
            if (!in_array($module, $mixins['ancestors'])) {
                if (in_array('Module', class_parents($module))) {
                    foreach (array_reverse($module::ancestors()) as $ancestor) {
                        if (!in_array($ancestor, $mixins['ancestors'])) array_unshift($mixins['ancestors'], $ancestor);
                    }
                }
                array_unshift($mixins['ancestors'], $module);
            }
        }
        $class::update_derived_modules();
    }
    
    static function new_instance($arguments = null) {
        $arguments = func_get_args();
        $class = get_called_class();
        return $class::new_instance_array($arguments);
    }
    
    static function new_instance_array($arguments = array()) {
        $rc = new \ReflectionClass(get_called_class());
        $instance = $rc->newInstanceArgs($arguments);
        return $instance;
    }
    
    static function &methods() {
        $class = get_called_class();
        $mixins = &$class::mixins();
        if (!$mixins['methods']) {
            $mixins['methods'] = array();
            $ancestors = array();
            foreach ($mixins['ancestors'] as $ancestor) {
                if (isset(Module::$mixins[$ancestor]) && Module::$mixins[$ancestor]['methods']) {
                    $mixins['methods'] = (array)Module::$mixins[$ancestor]['methods'];
                    foreach ($ancestors as $key => $module) {
                        if (in_array($module, Module::$mixins[$ancestor]['ancestors'])) unset($ancestors[$key]);
                    }
                    break;
                } else {
                    array_unshift($ancestors, $ancestor);
                }
            }
            $methods = get_class_methods($class);
            foreach ($ancestors as $ancestor) {
                foreach (get_class_methods($ancestor) as $method) {
                    if (!in_array($method, $methods)) {
                        if (!isset($mixins['methods'][$method])) $mixins['methods'][$method] = array();
                        array_unshift($mixins['methods'][$method], array($ancestor, $method));
                    }
                }
            }
            foreach ($mixins['aliases'] as $alias) {
                Module::alias($class, $alias[0], $alias[1]);
            }
        }
        return $mixins['methods'];
    }
    
    static function &mixins() {
        $class = get_called_class();
        if (!isset(Module::$mixins[$class])) {
            $mixins = array('aliases' => array(), 'ancestors' => array($class), 'methods' => false, 'properties' => false);
            foreach (class_parents($class) as $parent) {
                if (!in_array($parent, $mixins['ancestors'])) {
                    if (in_array('Module', class_parents($parent))) {
                    		$method = __FUNCTION__;
                    		$parent_mixins = $parent::$method();
                        $mixins['ancestors'] = array_merge($mixins['ancestors'], $parent_mixins['ancestors']);
                    } else {
                        array_push($mixins['ancestors'], $parent);
                    }
                }
            }
            Module::$mixins[$class] = $mixins;
        }
        return Module::$mixins[$class];
    }
    
    static function &properties() {
        $class = get_called_class();
        $mixins = $class::mixins();
        if (!$mixins['properties']) {
            $ancestors = array();
            foreach ($mixins['ancestors'] as $ancestor) {
                if (isset(Module::$mixins[$ancestor]) && Module::$mixins[$ancestor]['properties']) {
                    $mixins['properties'] = $properties;
                    break;
                } else {
                    array_unshift($ancestors, $ancestor);
                }
            }
            $mixins['properties'] = array();
            $properties = get_class_vars($class);
            foreach ($ancestors as $ancestor) {
                foreach (get_class_vars($ancestor) as $property => $value) {
                    if (!isset($properties[$property])) $mixins['properties'][$property] = $value;
                }
            }
        }
        return $mixins['properties'];
    }
    
    static function update_derived_modules() {
        $class = get_called_class();
        $ancestors = $class::ancestors();
        foreach (Module::$mixins as $module => &$mixins) {
            if ($module != $class && in_array($class, $mixins['ancestors'])) {
                array_splice($mixins['ancestors'], array_search($class, $mixins['ancestors']), count($mixins['ancestors']), $ancestors);
                $mixins['methods'] = false;
                $mixins['properties'] = false;
            }
        }
    }
    
}