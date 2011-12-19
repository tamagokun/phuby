# Tamagokun's Fork

This is an effort to make this library much faster and safer by using some new features added in PHP 5.3+

Important changes are:
- nearly all `call_class_method` calls are replaced by calling the method directly.
- all `eval()` calls have been removed
- `Proc` and a number of other features have been replaced by using actual Closure functions rather than a string passed into `eval()`
- Removing calls by reference where not needed

# phuby

rubyisms in php

(pronounced foo-bee)


## Installation

	git clone git://github.com/shuber/phuby.git


## Features

* A base `Object` class (all classes should inherit from this)
* Mixins - `Post::extend('Validations');`
* alias\_method - `Post::alias_method('save_with_validation', 'save');`
* alias\_method\_chain - `Post::alias_method_chain('save', 'validation');`
* Procs - `$proc = new Proc('name', 'echo "hello $name";'); $proc->call('Sean');`
* Various classes and modules from the standard ruby library like `Enumerable`, `Arr`, `Hash`, `Struct`, etc


## Usage

	require_once 'phuby/phuby.php';

Make sure your classes extend `Object`

	class User extends Object {
	    # ...
	}

More coming soon...


## Testing

Phuby uses SimpleSpec BDD extension to SimpleTest, just download simple test and 
put it as part of your php include_path or 'phuby/spec/simpletest'

* SimpleTest
* SimpleSpec class 

please run spec/runner.php


## Contact

Problems, comments, and suggestions all welcome: [shuber@huberry.com](mailto:shuber@huberry.com)