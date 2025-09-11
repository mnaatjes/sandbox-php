<?php

    use MVCFrame\Tests\Classes\Pizza;
    use MVCFrame\FileSystem\Path;
use MVCFrame\Tests\Classes\FailSon;
use MVCFrame\Tests\Classes\MagicEightball;
use MVCFrame\Tests\Classes\Vacuum;
use MVCFrame\Tests\Classes\SovietCarWash;
use MVCFrame\Tests\Turducken\Turkey;

    // Include autoloader
    require_once(__DIR__ . '/../vendor/autoload.php');

    // Require bootstrap/app.php
    require_once(__DIR__ . '/bootstrap/app.php');
    
    $alias = "config.mixed";
    
    //var_dump("Has: " . ($app->has($alias) === true ? "Yes" : "No"));

    /**
     * Array of strings, arrays, and various functions, classes, etc
     * @var array $mixedTypes
     */
    $mixedTypes = [
        // The final array containing various data types

        // A string literal
        'string_element' => 'Hello, World!',

        // An indexed array
        'array_element' => [1, 'two', 3.0],

        // An object literal (instance of stdClass)
        'object_element' => (object) [
            'id' => 123,
            'status' => 'active'
        ],

        // A Closure (an anonymous function that can be stored in a variable)
        'closure_element' => function ($a, $b) {
            return $a + $b;
        },

        // reference to callable function
        "trim",

        // reference to class name
        \MVCFrame\Tests\Cars\Toyota::class,

        // int value
        12,

        // object
        \MVCFrame\FileSystem\Path::create("/"),

        // array 
        [1, 2, 3, 4],

        // An anonymous function (syntactically the same as a closure in this context)
        'anonymous_function_element' => function () {
            return date('Y-m-d');
        },

        // An instance of a named class
        'class_instance_element' => new Pizza(),

        // A fully qualified class name as a string using the ::class constant
        'class_name_element' => new \MVCFrame\Tests\Classes\MagicEightball(),

        // An instance of an anonymous class (available since PHP 7)
        'anonymous_class_element' => new class {
            private $message = 'This is from an anonymous class.';

            public function getMessage(): string
            {
                return $this->message;
            }
        },

        "last"
    ];

    // Debugging
    var_dump($app->all());
?>