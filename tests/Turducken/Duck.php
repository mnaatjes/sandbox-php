<?php

    namespace MVCFrame\Tests\Turducken;
    use MVCFrame\Tests\Interfaces\EngastrationInterface;

    class Duck implements EngastrationInterface {
        public ?Chicken $chicken;
        public function __construct(Chicken $chicken){
            $this->chicken = $chicken;
        }
        public function vocalize(): void{
            var_dump("Quack!");
        }
    }
?>