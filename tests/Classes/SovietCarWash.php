<?php

    namespace MVCFrame\Tests\Classes;
    use MVCFrame\Tests\Cars\Lada;

    class SovietCarWash {
        public ?Lada $car=NULL;
        public function __construct(Lada $car){
            $this->car = $car;
        }
        public function wash(){
            var_dump("Washing a " . $this->car->getName() . "!");
        }
    }
?>