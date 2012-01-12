<?php
namespace Phuby;

function proc($block) {
	return new \Phuby\Proc($block);
}

function evaluate_block($block, $binding = array()) {
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