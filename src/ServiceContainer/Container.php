<?php

	namespace MVCFrame\ServiceContainer;
	use MVCFrame\ServiceContainer\ReflectionCache;

	/**-------------------------------------------------------------------------*/
	/** 
	 * Service Container Class
	 * Singleton
	 *
	 * 
	 */
	/**-------------------------------------------------------------------------*/
	class Container {
		/** 
		 * @var ServiceContainer
		 */
		private static ?Container $instance=NULL;

		private ?ReflectionCache $cache;

		/** 
		 * Stores Services and Dependenies in key=>value pairs
		 * @var array $bindings
		 */
		protected array $bindings=[];

		/**
		 * Tracks instance of shared - singleton - Dependencies
		 * - NULL meaning has not been instantiated
		 * - Resolver saved as value when instance created
		 * @var array $shared
		 */
		protected array $shared=[];

		/**-------------------------------------------------------------------------*/
		private function __construct(){
			// Instantiate ReflectionCache
			$this->cache = new ReflectionCache();

			// TODO: Register all ServiceProviders
		}
		/**-------------------------------------------------------------------------*/
		/**
		 * Check for an instance of the ServiceContainer and create one if none exists
		 * @return Container object instance
		 */
		/**-------------------------------------------------------------------------*/
		public static function getInstance(){
			// Check if already declared
			if(is_null(self::$instance)){
				self::$instance = new Container();
			}

			// Return instance
			return self::$instance;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Binds key to handler (class, method, callable) in bindings dependency array
		 * @param  string   $key     [description]
		 * @param  callable $handler [description]
		 * @return [type]            [description]
		 */
		/**-------------------------------------------------------------------------*/
		public function bind(string $key, callable $handler): void{
			// Bind $handler to $bindings
			$this->bindings[$key] = $handler;
		}

		/**-------------------------------------------------------------------------*/
		public function bindShared(string $key, callable $handler): void{
			//Bind key and value to $bindings
			$this->bindings[$key] = $handler;

			//Track state of singleton instantiation: default NULL
			$this->shared[$key] = NULL;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Helper Method for binding singleton instances to shared array
		 * @param  string   $key     [description]
		 * @param  callable $handler [description]
		 * @return [type]            [description]
		 */
		/**-------------------------------------------------------------------------*/
		public function singleton(string $key, callable $handler): void{
			// call bindShared
			$this->bindShared($key, $handler);
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Resolves (makes) the object instance / callable held in the $bindings array
		 * Laravel "make()" method
		 * 
		 * @param  string $key [description]
		 * @return [type]      [description]
		 */
		/**-------------------------------------------------------------------------*/
		public function resolve(string $key){
			// Check for array key
			if(!array_key_exists($key, $this->bindings)){
				// Attempt to register
				$this->cache->register($key);
				
				// double check $key available in cache
				
				// Return instance from ReflectionCache
					// On failure: trigger exception! 
				return;
			}

			// First: Check if array key exists in $shared and its state
			if(array_key_exists($key, $this->shared) && $this->shared[$key] !== NULL){
				// Singleton instance has been created. 
				// Render existing instance from shared array
				$instance = $this->shared[$key];
				return $instance;
			}

			// Second: Resolver (singleton or regular) exists in $bindings array
			// Resolve binding
			/** @var mixed Resolver */
			$resolver = $this->bindings[$key];

			// Make instance
			// bind ServiceContainer instance as parameter
			/** @var callable Instance of binding */
			$instance = $resolver($this);

			// TODO: Determine if $handler contains an object
			// TODO: Find dependencies on __construct()
			// TODO: ReflectionCache()

			// Third: Check if key exists in $shared and store instance in $shared
			if(array_key_exists($key, $this->shared) && $this->shared[$key] === NULL){
				// Register handler with $shared array
				$this->shared[$key] = $instance;
			}

			// Return resolver default as method
			return $instance;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Prints Shared and Bindings Array
		 */
		/**-------------------------------------------------------------------------*/
		public function debug(){
			printf('%s', json_encode([
				"shared" => $this->shared,
				"bindings" => $this->bindings
			], JSON_PRETTY_PRINT));
		}

		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**
		 * Possible Features:
		 * - Add properties to $bindings (or ReflectionCache array) to make sorting faster?
		 * 		-> $this->cache[$key] = ["instance => {}, "method"=> "name", "type" ="controller"]
		 * 		-> getControllers(){ return array_reduce($cache, function(){return has type "controller"}){}}
		 */
		/**-------------------------------------------------------------------------*/

	}	
?>