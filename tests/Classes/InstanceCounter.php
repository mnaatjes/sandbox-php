<?php

	namespace MVCFrame\Tests\Classes;

	class InstanceCounter {
		public static int $count = 0;
		public function __construct(){
			self::$count++;
		}
		public function countOff(){
			var_dump(self::$count);
		}

	}
?>