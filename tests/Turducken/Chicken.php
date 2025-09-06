<?php

    namespace MVCFrame\Tests\Turducken;
    use MVCFrame\Tests\Interfaces\EngastrationInterface;

    class Chicken implements EngastrationInterface{
        private ?int $temp;
        public function __construct(string $str="", int $temp=365){
            $this->temp = $temp;
        }
        public function vocalize(): void{
            var_dump("Cluck... Cluck..");
        }
        public function getTemp(){var_dump("Chicken is: " . ($this->temp > 350 ? "DONE" : "Undercooked"));}
    }
?>