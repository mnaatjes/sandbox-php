<?php

	namespace MVCFrame\ServiceContainer;

use ReflectionClass;

	class ReflectionCache {

		/** @var array Resolved Instances in key=>value pairs */
		private array $resolutions=[];

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function register(string $key){
			// Validate classname
			if(!class_exists($key)){
				throw new \Exception("Unable to resolve the provided class in " . __FUNCTION__);
			}

			// Attempt to reflect class
			$reflectionClass = new \ReflectionClass($key);
			var_dump($reflectionClass);
			// Get constructor
			// Find Params of Constructor, i.e. Dependencies
				// Recursively attempt to resolve dependencies
			// Resolve Instance of class
				// Fill Dependencies
			// Register 
			// Return True
			return true;
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function has(string $key): bool{
			return array_key_exists($key, $this->resolutions);
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
	}
?>