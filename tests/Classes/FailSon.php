<?php

    namespace MVCFrame\Tests\Classes;
    use MVCFrame\Tests\Cars\Lada;
    const FRUIT = "apple";

    class FailSon {
        //private ?string $param;
        public ?Lada $car;
        public function __construct(Lada $something, string $fruit=FRUIT, string $str="Princey", int $num=12){
            //$this->param = $param;
            $this->car = $something;
        }
    }
?>