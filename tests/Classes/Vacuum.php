<?php

	namespace MVCFrame\Tests\Classes;

	class Vacuum {

		private static array $bag=[];
		public function __construct(string ...$args){
			static::$bag = array_merge(static::$bag, $args);
		}
		public function dumpOut(){
			var_dump(json_encode(static::$bag, JSON_PRETTY_PRINT));
		}
		public function emptyBag(){
			return array_reduce(static::$bag, function($acc, $item){
				if(is_object($item)){
					$acc[] = $item;
				}
				return $acc;
			}, []);
		}
	}
?>