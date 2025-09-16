<?php

    namespace MVCFrame\FileSystem;
    use MVCFrame\FileSystem\File;
    use MVCFrame\FileSystem\Path;
    use MVCFrame\Foundation\ServiceRegistry;

    class FileSystem {

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
        public function __construct(?string $base_path=NULL){
            // Register instance
            self::$instance = $this;

            // Create path instance of basepath string
            $this->basePath = Path::create($base_path);

            // Verify basepath Exists
            if(!$this->basePath->exists()){
                throw new \Exception("Base path: " .(string)$this->basePath. " does NOT exist!");
            }

            // register major filepaths
            $this->register();

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
         * Registers Framework and Application paths with ServiceRegistry
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        private function register(){
            // Load Default Filepaths
            foreach(self::FRAME_DIR as $key => $default){
                // Assign target
                $target = Path::join($this->basePath, Path::create("/vendor/mnaatjes/mvc-framework/src" . $default));
                
                // Check if file
                var_dump($target->isFile());
                $target->isFile() ? File::create((string)$target) : $target;
                var_dump(get_class($target));
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
        private function normalize(string $alias, string $type): string{
            // Check if first key matches $type
            $prefix = substr($alias, 0, strlen($type));
            if($prefix !== $type){
                // Alias does not begin with type
                $result = $type . "." . $alias;
            } else if(strpos($alias, ".") !== strlen($type)){
                $result = substr($alias, 0, strlen($type)) . "." . substr($alias, strlen($type));
            } else {
                $result = $alias;
            }

            // Return result
            return $result;

        }
        /**-------------------------------------------------------------------------*/
        /**
         * Returns path or file from alias
         *
         * @param string $alias
         * @return mixed
         */
        /**-------------------------------------------------------------------------*/
        public function get(string $alias){
            // Check for first period
            if(substr($alias, 0, 4) !== "file" && substr($alias, 0, 4) !== "path"){
                // Missing prefix key to alias
                // Throw Exception
                throw new \Exception("Must include prefix 'file.' or 'path.' in alias!");

            } else if(strpos($alias, ".") !== 4){
                // Prefix key exists
                // Period Missing
                $alias = substr($alias, 0, 4) . "." . substr($alias, 4);
            }

            // return registry get
            reg($alias);
        }
        
        /**-------------------------------------------------------------------------*/
        /**
         * Registers path or file to ServiceRegistry under alias-prefix "path." or "file."
         *
         * @param string $alias
         * @param string|Path|File $value
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function set(string $alias, string|Path|File $value): void{
            // Declare type
            $type = NULL;

            // Determine Type
            if(is_string($value)){
                // Raw path or file value
                // Determine tupe
                $type = strrpos($value, ".") ? "file" : "path";

            } else if(is_object($value)){
                // Get type from class name
                $is_a = get_class($value);
                $last = strrpos($is_a, "/");
                $type = ($last !== false) ? strtolower(substr($is_a, $last + 1)) : NULL;
            }

            // Ensure type is no longer null
            if(is_null($type)){
                throw new \Exception("Unable to resolve Path or File instance!");
            }

            // Check and Normalize Alias
            $alias = $this->normalize($alias, $type);

            // Register
            reg($alias, $value);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Retuns array of all files and paths from ServiceRegistry
         *
         * @return array
         */
        /**-------------------------------------------------------------------------*/
        public function all():array{
            $registry = reg()->all();
            // Pull all properties under "config" array
            return [
                "path" => array_key_exists("path", $registry) ? $registry["path"] : [], 
                "file" => array_key_exists("file", $registry) ? $registry["file"] : [], 
            ];
        }
    }

?>