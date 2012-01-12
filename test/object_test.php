<?php

require_once '../phuby.php';

use \Phuby\Object;

class Whoa {
    function super_test($name) {
        return "Hello {$name} from super";
    }
    
    function testing() {
        return 'cooool';
    }
    
    static function thehell($name)
    {
        return "BLAH $name";
    }
}

class Dude {
    public $test_property = 'cool';
    
    function super_test($name) {
        echo "I like the name $name\n";
        return $this->super($name);
    }
    
    function testing2() {
        echo 'totally';
        return 'this is a returned value';
    }
    
    function delegated() {
        echo 'delegated from Dude'."\n";
    }
}

class UhOh {
    function testing() {
        $this->super();
    }
}

class Testing extends Object {
    
    function real_method() {
        return 'real_method';
    }
    
    protected function protected_method() {
        return 'protected_method';
    }
    
}
Testing::extend('Whoa', 'Dude');

echo "<pre>";

$t = new Testing;

$t->testing();
echo "\n";

$t->testing2();
echo "\n";

echo $t->testing2();
echo "\n";

echo $t->send('testing').' guy';
echo "\n";

echo $t->respond_to('real_method');
echo "\n";

echo $t->respond_to('testing');
echo "\n";

echo $t->respond_to('protected_method');
echo "\n";

echo $t->respond_to('invalid');
echo "\n";

echo $t->super_test('sean');
echo "\n";

echo $t->is_a('Testing');
echo "\n";

echo $t->is_a('Invalid');
echo "\n";

// $dup = $t->dup();
//print_r($dup);

Testing::delegate('delegated', 'Dude');
$t->delegated();

//$t->super();

echo $t->test_property;

$t->test_property = "TEST";

echo $t->test_property;

echo Testing::thehell("man");

echo "</pre>";
?>