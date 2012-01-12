<?php

require_once '../phuby.php';

use \Phuby\Arr,\Phuby\Hash;

// print_r(Enumerable::methods());

// print_r(Module::$mixins);

$e = new Arr;

// print_r(Arr::mixins());
// print_r(Arr::methods());

$e[] = 'ing';
$e[] = 'cool';
$e[] = 'wow';

echo $e[99];
$e->default = 'TEST DEFAULT';
echo $e[99]."\n";

foreach ($e as $k => $v) {
    echo $k.' => '.$v."\n";
}

echo "***COLLECT***\n";
print_r($e->collect(function($v,$k) { return $k; })->array);

echo "***ALIAS METHOD MAP***\n";
print_r($e->map(function($v,$k) { return $k; })->array);

if($e->any(function($v,$k){ return $k == 1;})) echo "true\n";
if($e->any(function($v,$k){ return $k == 4;})) echo "true\n";

echo "***INJECT***\n";
print_r($e->inject(array(), function($v,$k,$o){ $o["injected_$k"] = $v; return $o;}));

$e = new Hash;
$e['short'] = 4;
$e['this is a longer one'] = 12;
$e['this is long'] = 2;

echo "***ARRAY***\n";
print_r($e->array);

echo "***SORT***\n";
print_r($e->sort()->array);

echo "***SORT_BY***\n";
print_r($e->sort_by(function($v,$k){ return strlen($k); })->array);

echo "***FLATTEN***\n";
$e = new Arr(array(1, 2, 3, new Arr(array(4, 5, 6, new Arr(array(7, 8, 9))))));
print_r($e->flatten()->array);

echo "***CHUNK***\n";
$e = new Arr(array(1,2,3,4,5,6,7,8));
print_r($e->chunk(3)->to_native_a());