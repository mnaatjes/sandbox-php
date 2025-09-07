<?php

	namespace MVCFrame\Foundation;
	use MVCFrame\ServiceContainer\Container;
	use MVCFrame\FileSystem\Path;
	use MVCFrame\FileSystem\PathRegistry;

	class Application extends Container {

		/** @var integer Instance Count to prevent more than one instance */
		private static int $instanceCount=0;

		private ?PathRegistry $pathRegistry;

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
			// Create rootDir path instance
			// Validate and Assign Root Directory
			$rootDir = Path::create($root_directory);
			if(!$rootDir->exists()){
				// Directory does not exist
				throw new \Exception("Root Directory ".$root_directory." DOES NOT exist!");
			}

			// Determine Environment of Framework
			// TODO: Use env loading
			$env = $rootDir->getBasename() === "tests" ? "dev" : "production";

			// Instantiate Path Registry
			$this->pathRegistry = new PathRegistry($rootDir, $env);



			// Verify Environment:
			// Determine Basename
			// Attempt to load .env
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