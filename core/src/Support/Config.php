<?php
    namespace MVCFrame\Support;
    use MVCFrame\Foundation\ServiceRegistry;
    use MVCFrame\FileSystem\Path;
    use MVCFrame\Support\DotEnv;

    class Config {

        private static ?Config $instance;

        /**-------------------------------------------------------------------------*/
        /**
         * Constructor for Config Class
         *
         * @param ServiceRegistry|null $registry_instance
         */
        /**-------------------------------------------------------------------------*/
        private function __construct(){
            // Set instance
            self::$instance = $this;

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

			// Check if already declared
			if(!isset(self::$instance)){
				self::$instance = new Config();
			}
            
			// Return instance
			return self::$instance;
        }

        /**-------------------------------------------------------------------------*/
        /**-------------------------------------------------------------------------*/
        private function load(){
            
            // Get all files

            // Load Default Configs
            
            // Load Application Configs
            // Merge values
        }
        /**-------------------------------------------------------------------------*/
        /**
         * Returns all "config" sub-array values
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function all(){
            // Pull all properties under "config" array
            return reg()->all()["config"];
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
            reg($alias, $value);
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
            return reg($alias);
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