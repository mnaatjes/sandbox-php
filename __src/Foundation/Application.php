<?php

	namespace MVCFrame\Foundation;
	use MVCFrame\ServiceContainer\Container;
	use MVCFrame\FileSystem\Path;
	use MVCFrame\FileSystem\PathRegistry;
	use MVCFrame\FileSystem\DotEnv;

	class Application extends Container {

		/** @var integer Instance Count to prevent more than one instance */
		private static int $instanceCount=0;
		private static ?Application $instance=NULL;

		private ?PathRegistry $pathRegistry;

		private ?DotEnv $envManager;

		/**-------------------------------------------------------------------------*/
		/**
		 * Application Constructor
		 *
		 * @param string $root_directory
		 */
		/**-------------------------------------------------------------------------*/
		public function __construct(string $root_directory){
			// Check instance count and limit to 1
			if(static::$instanceCount !== 0){
				// Trigger Exception: Cannot Create more than one instance
				throw new \Exception("Cannot Instantiate more than ONE Instance of Application!");
			}

			// Set Instance Count
			// Set instance
			static::$instanceCount = 1;
			self::$instance = $this;

			// Configure Application
			$this->configureApplication($root_directory);

			// Execute Parent Constructor
			// Enables ReflectionCache
			parent::__construct();
			var_dump($this->cache);
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Check for an instance of the Application
		 * @throws \Exception If Application not instantiated
		 * @return self
		 */
		/**-------------------------------------------------------------------------*/
		public static function getInstance(): self{
			// Check if already declared
			if(is_null(self::$instance)){
				// Application has not yet been instantiated
				// Throw Exception
				throw new \Exception("Application has not yet been instantiated! Must create Application before making call");
			}

			// Return instance
			return self::$instance;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Configures Application in Cascade of Importance
		 * @param  string $root_directory [description]
		 * @return [type]                 [description]
		 */
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

			// Register ENV Path
			path("base", "config.env", Path::join(path("base.config"), path("/.env")));
			
			$this->envManager = new DotEnv();
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getPathRegistry(){
			// Return Instance Path Registry
			return $this->pathRegistry;
		}

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