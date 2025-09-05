<?php
    
    // Include autoloader
    require_once(__DIR__ . '/../vendor/autoload.php');


    use MVCFrame\Tests\Classes\TestApplication;
    use MVCFrame\Tests\Cars\Toyota;
    use MVCFrame\Tests\Cars\Volvo;
    use MVCFrame\Tests\CarFactory;
    use MVCFrame\Tests\Classes\Pizza as ConcretePizza;
    use MVCFrame\Tests\Fascades\Pizza;
    use MVCFrame\Tests\Classes\ServiceContainer;

    // Debugging
    //$app = new TestApplication();

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

    //dealership()->toyota(["camry", "yaris"]);
    //dealership(["99", "98", "97"]);
    

    ServiceContainer::getInstance()->bind(ConcretePizza::class, function(){return new ConcretePizza();});

    Pizza::withCheese();
    Pizza::bake();
?>