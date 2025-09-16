<?php

    namespace MVCFrame\FileSystem;
    use MVCFrame\FileSystem\File;
    use MVCFrame\FileSystem\Path;
    use MVCFrame\Foundation\ServiceRegistry;
use PDO;

    class FileSystem {

        private ?ServiceRegistry $registry;
        private static $instance;
        private ?Path $basePath;

		/**
		 * Required Directories assoc array for Application
		 * @var array APP_DIR
		 */
		private const APP_DIR=[
			"app" 		=> "/app",
			"bootstrap" => "/bootstrap",
			"config" 	=> "/config",
			"database" 	=> "/database",
            "env"       => "/.env",
			"public" 	=> "/public",
			"resource" 	=> "/resources",
			"routes"	=> "/routes",
			"storage" 	=> "/storage",
		];

        /**
         * Framework Directories
         * @var array FRAME_DIR
         */
        private const FRAME_DIR=[
            "config"        => "/Config",
            "config.app"    => "/Config/app.php"
        ];

        /**-------------------------------------------------------------------------*/
        /**
         * Constructor for FileSystem
         *
         * @param string $base_path
         */
        /**-------------------------------------------------------------------------*/
        public function __construct(string|Path|Directory $base_path, ?ServiceRegistry $service_registry){
            // Register instance
            self::$instance = $this;

            // Create path instance of basepath string
            $this->basePath = is_string($base_path) ? Path::create($base_path) : $base_path;

            // Assign Registry
            $this->registry = $service_registry;

            // Register base path
            $this->registerPath("base", $this->basePath);

            // Verify basepath Exists
            if(!$this->basePath->exists()){
                throw new \Exception("Base path: " .(string)$this->basePath. " does NOT exist!");
            }

            // Register Framework Filepath Dependencies
            $this->registerFrameworkPaths();
            
            // Register Application Filepaths
            $this->registerApplicationPaths();
        }
        /**-------------------------------------------------------------------------*/
        /**
         * Finds and returns existing instance
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public static function getInstance(){
            // Ensure Registry Exists
            if(!is_a(ServiceRegistry::getInstance(), ServiceRegistry::class)){
                throw new \Exception("Service Registry Instance Missing! Please Instantiate Service Registry");
            }

			// Check if already declared
			if(!isset(self::$instance)){
				throw new \Exception("FileSystem must be instantiated before instance can be retrieved!");
			}
            
			// Return instance
			return self::$instance;
        }
        /**-------------------------------------------------------------------------*/
        /**
         * Validates and Registers Framework Dependent Filepaths
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        private function registerFrameworkPaths(): void{
            // Load Default Filepaths
            foreach(self::FRAME_DIR as $key => $default){
                // Assign path
                $path = Path::join($this->basePath, Path::create("/vendor/mnaatjes/mvc-framework/src" . $default));

                // Verify Existance
                if(!$path->exists()){
                    // Path does not exist
                    throw new \Exception("Path: " . (string)$path . " does NOT exist!");
                }

                // Determine prefix and normalize alias
                $alias = $this->normalize($key, $this->determinePrefix($path));
                
                // Store in Registry
                $this->registerPath($alias, $path);
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Verifies and registers Application Dependent Filepaths
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        private function registerApplicationPaths(): void{
            // Load Application Files
            // Overwrites defaults if conflict
            foreach(self::APP_DIR as $key=>$dir){
                $path = Path::join($this->basePath, Path::create($dir));
                
                // Verify Existance
                if(!$path->exists()){
                    // Path does not exist
                    throw new \Exception("Path: " . (string)$path . " does NOT exist!");
                }

                // Determine prefix and normalize alias
                $alias = $this->normalize($key, $this->determinePrefix($path));

                // Store in Registry
                $this->registerPath($alias, $path);
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Normalizes alias from type (file or path) and returns properly formatted alias
         *
         * @param string $alias
         * @param string $type
         * @return string
         */
        /**-------------------------------------------------------------------------*/
        private function normalize(string $alias, string $prefix): string{
            // Ensure "Config" not prepended
            if(!str_starts_with($alias, $prefix)){
                // Prepend config. to alias
                return $prefix . "." . $alias;

            } else if(strpos($alias, ".") !== strlen($prefix)){
                // Period missing after config
                // Inject period
                return substr($alias, 0, strlen($prefix)) . "." . substr($alias, strlen($prefix));
                
            } else {
                // Alias properly formatted going in
                return $alias;
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Determine type of $value and return proper alias prefix
         *
         * @param [type] $value
         * @return string
         */
        /**-------------------------------------------------------------------------*/
        private function determinePrefix($value): string{
            // Determine fail condition: not string or object
            if(!is_string($value) && !is_object($value)){
                throw new \Exception("Unable to determine path prefix!");
            }

            // Check if string
            if(is_string($value)){
                // Value is sting
                // Render $value as path object
                $value = Path::create($value);
            }

            // Evaluate Object type
            if($value instanceof Directory){
                return "dir";
            } else if($value instanceof File){
                return "file";
            } else if($value instanceof Path){
                return "path";
            } else {
                // Cannot resolve prefix
                throw new \Exception("Unable to determine path prefix!");
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Returns path or file from alias
         *
         * @uses reg() ServiceRegistry helper function
         * @param string $alias
         * @return mixed
         */
        /**-------------------------------------------------------------------------*/
        public function getPath(string $alias){return $this->registry->lookup($alias);}
        
        /**-------------------------------------------------------------------------*/
        /**
         * Registers path or file to ServiceRegistry under alias-prefix "path." or "file."
         *
         * @param string $alias
         * @param string|Path|File $value
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function registerpath(string $alias, string|Path|File $value): void{


            // Check and Normalize Alias
            $alias = $this->normalize($alias, $this->determinePrefix($value));

            // Register
            $this->registry->register($alias, $value);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Retuns array of all files and paths from ServiceRegistry
         *
         * @return array
         */
        /**-------------------------------------------------------------------------*/
        public function all():array{
            // Pull registry data
            $registry = $this->registry->all();

            // Pull all properties under "config" array
            return [
                "path"  => array_key_exists("path", $registry) ? $registry["path"] : [],
                "dir"   => array_key_exists("file", $registry) ? $registry["dir"] : [],
                "file"  => array_key_exists("file", $registry) ? $registry["file"] : []
            ];
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Array of alias => path (string) pairs
         *
         * @return array
         */
        /**-------------------------------------------------------------------------*/
        public function list(): array{return $this->flatten();}

        /**-------------------------------------------------------------------------*/
        /**
         * Flattens ServiceRegistry keys: path, dir, file into alias=>value(string) array
         *
         * @param array|null $arr
         * @param string $prefix
         * @return array
         */
        /**-------------------------------------------------------------------------*/
        private function flatten(?array $arr=NULL, string $prefix=""): array{
            // Declare accumulator
            $acc = [];

            // Determine if array is null
            // Populate with data from registry if null
            $arr = is_null($arr) ? $this->all() : $arr;

            // Loop and Flatten
            foreach($arr as $key=>$value){
                // Current alias
                $curr = !empty($prefix) ? $prefix . "." . $key : $key;

                // If the value is an array & not empty --> flatten again
                if(is_array($value) && !empty($value)){
                    // Recurse
                    // Merge Results
                    $acc = array_merge($acc, $this->flatten($value, $curr));
                } else {
                    // Skip values not dot-notation
                    if(strpos($curr, ".") === false){
                        continue;
                    }
                    // Otherwise value scalar or empty array
                    $acc[$curr] = is_object($value) ? (string)$value : $value;
                }
            }
            // Return flattened acc array
            return $acc;
        }
    }

?>