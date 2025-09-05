<?php

	namespace MVCFrame\Tests\Cars;
	use MVCFrame\Tests\Interfaces\CarInterface;

    class Lada implements CarInterface {
        public function honk(): void {var_dump("Comrade! Comrade!");}
    }
    
?>