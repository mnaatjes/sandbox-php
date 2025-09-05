<?php
    
    // Include autoloader
    require_once(__DIR__ . '/../vendor/autoload.php');


    use MVCFrame\Tests\Classes\TestApplication;
    use MVCFrame\Tests\Cars\Toyota;
    use MVCFrame\Tests\Cars\Volvo;
    use MVCFrame\Tests\CarFactory;
    
    // Debugging
    $app = new TestApplication();

    /** 
     * Helper function
     * @param string $type
     */
    function dealership($args=""){
        // Declare factory
        $factory = new CarFactory();

        // Return factory
        if(func_num_args() === 0){
            return $factory;
        }

        // Return and call
        return $factory->alert($args);

    }

    dealership()->toyota(["camry", "yaris"]);
    dealership(["99", "98", "97"]);
?>