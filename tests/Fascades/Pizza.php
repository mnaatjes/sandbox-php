<?php
	
	namespace MVCFrame\Tests\Fascades;
	use MVCFrame\Tests\Classes\Fascade;
	use MVCFrame\Tests\Classes\Pizza as ConcretePizza;
	class Pizza extends Fascade {

		public static function getFascadeAccessor(){
			return ConcretePizza::class;
		}

	}

?>