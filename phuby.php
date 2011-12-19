<?php

spl_autoload_register(function($class) {
	if(!class_exists($class))
	{
		$file = __DIR__."/lib/" . str_replace('\\','/',$class).'.php';
		if(file_exists($file)) require $file;
	}
	if(!class_exists($class) && !interface_exists($class)) return false;
});

require_once 'lib/Phuby/Phuby.php';