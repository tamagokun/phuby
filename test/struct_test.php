<?php

require_once '../phuby.php';

use \Phuby\Struct;

// $customer = new Struct('first_name', 'last_name');
$customer = Struct::new_instance('first_name', 'last_name');

$sean = $customer->instance('Sean', 'Huber');

echo $sean->first_name."\n";
echo $sean->last_name."\n";
echo $sean->invalid."\n";

// print_r($sean);

?>