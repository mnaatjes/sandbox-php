<?php

	namespace MVCFrame\Tests\Cars;
	use MVCFrame\Tests\Interfaces\CarInterface;

    class Toyota implements CarInterface {
        public function __construct($params) {
            var_dump($params);
        }
        public function honk(): void {var_dump("honkuu honkuu!");}
    }
    
?>