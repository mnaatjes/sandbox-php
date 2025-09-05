<?php

	namespace MVCFrame\Tests;
	use MVCFrame\Tests\Interfaces\CarInterface;
    use MVCFrame\Tests\Cars\Toyota;
    use MVCFrame\Tests\Cars\Volvo;
    use MVCFrame\Tests\Cars\Lada;

	class CarFactory {
		/**
		 * Make function returns car interface object
		 */
		public function volvo(): CarInterface {
			return new Volvo();
		}

		/** 
		 * Return a toyota
		 */
		public function toyota($params): CarInterface{
			return new Toyota($params);
		}

		/**
		 * Alert
		 */
		public function alert($args){
			var_dump($args);
			var_dump("Well, There's Your Problem!");
		}
	}

?>