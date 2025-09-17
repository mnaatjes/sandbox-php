<?php

    namespace MVCFrame\Foundation;
    use MVCFrame\FileSystem\Path;
    use MVCFrame\FileSystem\File;
    use MVCFrame\FileSystem\FileSystem;
    use MVCFrame\Support\DotEnv;
    use MVCFrame\Support\Config;

    class Application {

        /**
         * Application Instance
         *
         * @var Application|null
         */
        private static ?Application $instance;

        private ?ServiceContainer $container;

        private ?ServiceRegistry $registry;

        private ?FileSystem $fileSys;

        private ?DotEnv $env;

        private ?Config $config;

        /**-------------------------------------------------------------------------*/
        /**
         * Construct for Application
         * - Allows only 1 instnace
         * - Requires valid base-path as parameter
         *
         * @param string $base_path
         */
        /**-------------------------------------------------------------------------*/
        public function __construct(string $base_path){
            // Enforce Singleton Behavior:
            // Validate and Configure Instance
            // Check for existing instance
            if(isset(self::$instance)){
                throw new \Exception("Application instance already exists!");
            }

            // Set instance
            self::$instance = $this;

            // Create Registry Instance
            $this->registry = ServiceRegistry::getInstance();

            // Create Instance of FileSystem:
            // Self-Orients Application & Registers Paths
            $this->fileSys = new FileSystem($base_path, $this->registry);
            
            // Create DotEnv Instance
            // DotEnv loads and registers on instantiation
            $this->env = new DotEnv($this->fileSys->getPath("file.env"));

            // DEBUGGING: Test Registry Item
            
            // Create Config Instance
            // Load Configurations and Register
            //$this->config = new Config($this->registry, $this->fileSys);

            // Create Container Instance
            //$this->container = ServiceContainer::getInstance();
        }
        
        /**-------------------------------------------------------------------------*/
        /**
         * Finds and returns existing instance
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public static function getInstance(){
			// Check if already declared
			if(is_null(self::$instance)){
				throw new \Exception("Application has NOT been instantiated! Create new Application(dirname(__DIR___)) in bootstrap/app.php");
			}
			// Return instance
			return self::$instance;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Checks if provided value is a dependency:
         * - Callable
         * - Classname
         *
         * @param [type] $value
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        private function isDependency($value): bool{

           // Check String Value
            if(is_string($value)){
                // Value string can be classname or defined function
                // Returns false if neither callable or classname
                return is_callable($value) || class_exists($value);
            }

            // Check if callable
            return is_callable($value);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Checks if Key matches pattern
         *
         * @param string $key
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        private function isValidKey(string $key){
            // Rules:
            // Start with string
            // Letters, numbers, "_" or "\" are valid
            // Must end with letter or number
            $pattern = '/^[a-zA-Z0-9](?:[a-zA-Z0-9_\\\\]*[a-zA-Z0-9])?$/';
            return preg_match($pattern, $key) == 1;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Checks if Alias matches pattern
         *
         * @param string $alias
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        private function isValidAlias(string $alias){
            // Rules:
            // Start with letter
            // End with number or letter
            $pattern = '/^[a-zA-Z0-9](?:[a-zA-Z0-9_-]*[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9_-]*[a-zA-Z0-9])?)*$/';

            return preg_match($pattern, $alias) == 1;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Adds a new value to the Container or Registry based on Alias rules and type
         *
         * @uses MVCFrame\Foundation\Application->set()
         * 
         * @param string $key
         * @param [type] $value
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function add(string $key, $value): void{
            // Check if value at key already exists
            if($this->registry->has($key) || $this->container->has($key)){
                // Cannot add an existing key or alias
                throw new \Exception("Cannot add() existing Key or Alias to Container or Registry! Use set() to overwrite existing record!");
            }

            // Use set() to apply
            $this->set($key, $value);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Store Container dependency as singleton instance in Shared array
         *
         * @param string $key
         * @param [type] $value
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function share(string $key, $value){
            // Execute set() with singleton value true
            $this->set($key, $value, true);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Sets value in either Registry or Container depending on type and format of key
         * - Overwrites existing at alias / key
         * - Aliases (dot-notation) and non-callables saved in ServiceRegistry
         * - Keys, Classnames and instances, callables, closures saved in ServiceContainer
         *
         * @param string $key
         * @param [type] $value
         * @param bool $isSingleton - Default: false; True: save to Container->$shared array
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function set(string $key, $value, $isSingleton=false): void{
            // Determine if alias or key
            $isAlias = str_contains($key, ".") ? true : false;

            // Validate Alias
            if($isAlias && !$this->isValidAlias($key)){
                // Invalid Alias
                throw new \Exception("Invalid Alias: " . $key);
            }

            // Validate Key
            if(!$isAlias && !$this->isValidKey($key)){
                // Invalid Key
                throw new \Exception("Invalid Key: " . $key);
            }

            // Determine if Dependency
            $isDependency = $this->isDependency($value);

            // Non-Alias Dependency
            if(!$isAlias && $isDependency) {
                // Value IS a valid dependency and belongs in Container
                // Determine if Shared or Closure Binding
                if($isSingleton){
                    // Save to Container shared array
                    // Ensure key not already registerd
                    if($this->container->has($key)){
                        // Key already exists and cannot be overwritten
                        throw new \Exception("Container key: " . $key . " Already Exists! Shared Keys cannot be overwritten!");
                    }
                    // Store in Container Shared array
                    $this->container->share($key, $value);

                } else {
                    // Save to bindings
                    $this->container->bind($key, $value);
                }

            } else if($isAlias && !$isDependency){
                // Value is NOT a dependency and belongs in Registry
                $this->registry->register($key, $value);
            } else {
                // Cannot set with either Container or Registry
                throw new \Exception("Unable to add / set in either Container or Registry! Alias must contain a period!");
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Searches Registry and Container for Value from key / alias
         *
         * @param string $key
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        public function has(string $key): bool{
            // Determine if seeking value from Container or Registry:
            // Determine if dot-map alias
            if(str_contains($key, ".")){
                // $key is an alias
                // Check Registry
                return $this->registry->has($key);
            } else {
                // Check Container
                return $this->container->has($key);
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Retrieve value from Registry or Container based on key
         *
         * @param string $key
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function get(string $key){
            // Check Registry
            if($this->registry->has($key)){
                // Found in Registry
                return $this->registry->lookup($key);

            }

            // Perform Resolve with Container
            // Validate $key
            if(!$this->isValidKey($key)){
                // Invalid Container Key
                throw new \Exception("Invalid Dependency / Container Key: " . $key);
            }

            // Resolve from Container
            // If Dependency does not exist - will be saved to cache and resolved
            return $this->container->resolve($key);

        }

        /**-------------------------------------------------------------------------*/
        /**
         * Returns all data from Registry and Container
         *
         * @uses MVCFrame\Foundation\ServiceRegistry->all();
         * @uses MVCFrame\Foundation\ServiceContainer->all();
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function all(): array{
            return [
                "Registry" => $this->registry->all(),
                "Container" => $this->container->all()
            ];
        }

        /**-------------------------------------------------------------------------*/
        /**-------------------------------------------------------------------------*/
        /**-------------------------------------------------------------------------*/
        /**-------------------------------------------------------------------------*/
        /**-------------------------------------------------------------------------*/
        /**-------------------------------------------------------------------------*/
    }
?>