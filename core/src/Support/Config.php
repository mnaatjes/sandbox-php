<?php
    namespace MVCFrame\Support;

use MVCFrame\FileSystem\FileSystem;
use MVCFrame\Foundation\ServiceRegistry;
    use MVCFrame\FileSystem\Path;
    use MVCFrame\Support\DotEnv;

    class Config {

        private ?ServiceRegistry $registry;
        private ?FileSystem $fileSys;
        private static ?Config $instance;

        /**-------------------------------------------------------------------------*/
        /**
         * Constructor for Config Class
         *
         * @param ServiceRegistry|null $registry_instance
         */
        /**-------------------------------------------------------------------------*/
        public function __construct(?ServiceRegistry $registry_instance, ?FileSystem $filesys_instance){
            // Check instance already created
            if(isset(self::$instance)){
                throw new \Exception("Config instance has already been created!");
            }

            // Set instance
            self::$instance = $this;

            // Set Registry
            $this->registry = $registry_instance;

            // Set FileSystem
            $this->fileSys = $filesys_instance;

            // Load Configuration Files
            // Register Configuration Values
            $this->load();
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

            // Ensure FileSystem Exists
            if(!is_a(FileSystem::getInstance(), FileSystem::class)){
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
        /**-------------------------------------------------------------------------*/
        private function load(){
            // Load Default and Application Configuration Files
            $defaultDir = $this->fileSys->getpath("dir.default.config");
            $appDir     = $this->fileSys->getpath("dir.config");

            var_dump($defaultDir);
            
            // Load Application Configs
            // Merge values
        }

        private function loadDefaultConfigs(){}
        private function loadAppConfigs(){}
        
        /**-------------------------------------------------------------------------*/
        /**
         * Returns all "config" sub-array values
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function all(){
            // Pull all properties under "config" array
            return $this->registry->all()["config"];
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Adds alias => value to ServiceRegistry under "config" sub-array
         *
         * @param string $alias
         * @param [type] $value
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function set(string $alias, $value){
            // Check and Normalize Alias
            $alias = $this->normalize($alias);

            // Register
            $this->registry->register($alias, $value);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Normalizes alias and returns value from ServiceRegistry
         *
         * @param string $alias
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function get(string $alias){
            // Check and format alias
            $alias = $this->normalize($alias);

            // Return from registry
            return $this->registry->lookup($alias);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Normalizes alias string for Configuration sub-array key of ServiceRegistry
         *
         * @param string $alias
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        private function normalize(string $alias){
            // Ensure "Config" not prepended
            if(!str_starts_with($alias, "config")){
                // Prepend config. to alias
                return "config." . $alias;

            } else if(strpos($alias, ".") !== strlen("config")){
                // Period missing after config
                // Inject period
                return substr($alias, 0, strlen("config")) . "." . substr($alias, strlen("config"));
                
            } else {
                // Alias properly formatted going in
                return $alias;
            }
        }
    }

?>