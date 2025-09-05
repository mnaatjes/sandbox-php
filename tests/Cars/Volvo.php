<?php

	namespace MVCFrame\Tests\Cars;
	use MVCFrame\Tests\Interfaces\CarInterface;

	class Volvo implements CarInterface {
        public function honk(): void {var_dump("honk boink boink!");}
    }

?>