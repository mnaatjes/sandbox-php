<?php
    
    // Include autoloader
    require_once(__DIR__ . '/../vendor/autoload.php');


    //use MVCFrame\Tests\Classes\TestApplication;
    use MVCFrame\Tests\Cars\Toyota;
    use MVCFrame\Tests\Cars\Volvo;
    use MVCFrame\Tests\CarFactory;
    use MVCFrame\Tests\Classes\Pizza as ConcretePizza;
    use MVCFrame\Tests\Fascades\Pizza;
    use MVCFrame\ServiceContainer\Container;
    use MVCFrame\Tests\Classes\InstanceCounter;
    use MVCFrame\Tests\Classes\Singleton;
    use MVCFrame\Tests\Classes\Vacuum;

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
    

    //Container::getInstance()->bind(ConcretePizza::class, function(){return new ConcretePizza();});

    //Pizza::withCheese();
    //Pizza::bake();
    
    // Instance Debugging
    //app(InstanceCounter::class, function(){return new InstanceCounter();});
    app(ConcretePizza::class, function(){return new ConcretePizza();});
    app()->singleton(InstanceCounter::class, function(){return new InstanceCounter();});
    app()->singleton(Singleton::class, function(){return Singleton::getInstance();});

    $instances = [
        app(InstanceCounter::class),
        app(InstanceCounter::class),
        app(InstanceCounter::class),
        app(InstanceCounter::class),
        app(InstanceCounter::class),
        app(InstanceCounter::class),
        app(InstanceCounter::class),
        app(InstanceCounter::class),
        app(Singleton::class),
        app(Singleton::class),
        app(Singleton::class),
        app(Singleton::class),
        app(Singleton::class),
    ];

    foreach($instances as $instance){
        //$instance->countOff();
    }

    $hoover     = new Vacuum("wool", "bags", "kitty litter");
    $bisel      = new Vacuum("poop", "hair", "food");

    app(Vacuum::class, function($app){
        return new Vacuum(
            $app->resolve(InstanceCounter::class),
            $app->resolve(Singleton::class),
            $app->resolve(ConcretePizza::class)
        );
    });

    //var_dump(app(Vacuum::class)->emptyBag());

    app(\MVCFrame\Tests\Classes\TestApplication::class);
?>