<?php

	namespace MVCFrame\Tests\Cars;
	use MVCFrame\Tests\Interfaces\CarInterface;

    class Lada implements CarInterface {
        private string $name = "Lada";
        public function __construct(){
            //var_dump("The East is Red!");
        }
        public function honk(): void {var_dump("Comrade! Comrade!");}
        public function getName(){return $this->name;}
    }
    
?>