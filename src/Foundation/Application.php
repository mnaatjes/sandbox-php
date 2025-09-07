<?php

	namespace MVCFrame\Foundation;
	use MVCFrame\ServiceContainer\Container;

	class Application extends Container {

		/** @var integer Instance Count to prevent more than one instance */
		private static int $instanceCount=0;

		private ?string $rootDir;

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function __construct(string $root_directory){
			// Check instance count and limit to 1
			if(static::$instanceCount !== 0){
				// Trigger Exception: Cannot Create more than one instance
				throw new \Exception("Cannot Instantiate more than ONE Instance of Application!");
			}

			// Set Instance Count
			static::$instanceCount = 1;

			// Configure Application
			$this->configureApplication($root_directory);

			// Execute Parent Constructor
			// Enables ReflectionCache
			parent::__construct();
			
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		private function configureApplication(string $root_directory){
			// Validate and Assign Root Directory
			if(!is_dir($root_directory)){
				// Directory does not exist
				throw new \Exception("Root Directory ".$root_directory." DOES NOT exist!");
			}

			// Assign Root Directory
			$this->rootDir = $root_directory;

			// Verify Environment:
			// Determine Basename
			// Attempt to load .env
			$baseDir = basename($this->rootDir);
			var_dump("Basename: ".$baseDir);
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
	}
?>