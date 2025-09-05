<?php

	namespace MVCFrame\Tests\Classes;

	class Pizza {
		private static ?Pizza $instance = NULL;
		private array $toppings = [];

		public static function getInstance(){
			if(self::$instance === NULL){
				self::$instance = new self();
			}
			return self::$instance;
		}

		public static function __callStatic(string $method, $arguments){
			// Declare instance
			$instance = self::getInstance();
			return $instance->$method(...$arguments);
		}

		public function anchovies(){
			var_dump("anchovies");
		}

		public function withCheese(){
			$this->toppings[] = "cheese";
			return $this;
		}

		public function addSausage(){
			$this->toppings[] = "sausage";
			return $this;
		}

		public function addOlives(){
			$this->toppings[] = "green olives";
			return $this;
		}

		public function bake(){
			$ingredients = implode(", ", $this->toppings);
			var_dump("The pizza has been baked with: ". $ingredients);
		}
	}
?>