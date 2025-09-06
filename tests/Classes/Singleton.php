<?php

	namespace MVCFrame\Tests\Classes;

	class Singleton {
		private static ?Singleton $instance=NULL;
		private static int $count=0;

		public static function getInstance(){
			// Check if already declared
			if(is_null(self::$instance)){
				self::$instance = new Singleton();
				self::$count++;
			}

			// Return instance
			return self::$instance;
		}
		public function countOff(){
			var_dump("Singletons: " . self::$count);
		}
	}
?>