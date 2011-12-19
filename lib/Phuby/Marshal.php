<?php
namespace Phuby;

class Marshal extends Object { }

abstract class MarshalMethods {
    
    function dump($object) {
        return serialize($object);
    }
    
    function load($data) {
        return unserialize($object);
    }
    
}

Marshal::extend('Phuby\MarshalMethods');

Marshal::alias_method('restore', 'load');