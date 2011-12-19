<?php
namespace Phuby;

function &call_class_method($class, $method, $arguments = array()) {
		//eval('$result = &'.build_function_call(array($class, $method), $arguments).';');
    $result = &call_class_method_array(array($class,$method),$arguments);
    //$result = &call_user_func_array($class::${$method},$arguments);
    //$result = call_user_func_array(array($class,$method),$args);
    //$method = new \ReflectionMethod($class,$method);
    //$result =& $method->invokeArgs(null,$arguments);
    //$result = &$class::$method(extract($arguments));
    return $result;
}

function &call_class_method_array($function,$arguments = array())
{   
  if(!is_array($function)) $function = array($function);
  if(is_object($function[0])) $function[0] = get_class($function[0]);
	$class = $function[0];
	$method = $function[1];
	switch(count($arguments))
	{
		case 0: $result = &$class::$method();break;
		case 1: $result = &$class::$method($arguments[0]);break;
		case 2: $result = &$class::$method($arguments[0],$arguments[1]);break;
		case 3: $result = &$class::$method($arguments[0],$arguments[1],$arguments[2]);break;
	}
	return $result;
}

function proc($block) {
	return new \Phuby\Proc($block);
}

function evaluate_block($block, $binding = array()) {
    # implict return
    /*$lines = explode(';', $block);
    $last =& $lines[count($lines)-2];
    
    if (strpos($last, 'return') === false) $last = 'return '.$last;
    $block = join(';', $lines);
    echo "Binding: <br />";
		print_r($binding);
		echo "Lines: <br />";
		print_r($lines);
    $parameters = array_merge(array_keys($binding), array($block));
    eval('$proc = '.build_function_call('\Phuby\proc', $parameters, 'parameters').';');
    return $proc->call_array(array_values($binding));
		*/
		return call_user_func_array($block,$binding);
}

// Convenience function
function a () {
  $args = func_get_args();
  return new Arr($args);
}
//Need to do this to bootstrap Phuby classes
new Object();