<?php
	
	namespace MVCFrame\Tests\Classes;
	/** 
	 * 
	 */
	class ServiceContainer {
		/** 
		 * @var ServiceContainer
		 */
		private static ?ServiceContainer $instance=NULL;

		/** 
		 * @var array
		 */
		protected array $bindings=[];

		/**
		 * Check for an instance of the ServiceContainer and create one if none exists
		 * @return ServiceContainer object instance
		 */
		public static function getInstance(){
			// Check if already declared
			if(is_null(self::$instance)){
				self::$instance = new ServiceContainer();
			}

			// Return instance
			return self::$instance;
		}

		/**
		 * Binds key to handler (class, method, callable) in bindings dependency array
		 * @param  string   $key     [description]
		 * @param  callable $handler [description]
		 * @return [type]            [description]
		 */
		public function bind(string $key, callable $handler){
			$this->bindings[$key] = $handler;
		}

		public function resolve(string $key){
			// Check for array key
			if(!array_key_exists($key, $this->bindings)){
				throw new \Exception("Unable to resolve binding!");
			}

			/** @var mixed Resolver */
			$resolver = $this->bindings[$key];

			// Return resolver default as method
			return $resolver();
		}


	}

?>