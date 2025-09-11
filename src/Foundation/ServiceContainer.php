<?php

    namespace MVCFrame\Foundation;

    class ServiceContainer {
        /**
         * Parent Application instance
         *
         * @var Application|null
         */
        private ?Application $app;

        /**
         * Self Instance: Only one allowed
         *
         * @var ServiceContainer|null
         */
        private static ?ServiceContainer $instance;

        private array $bindings=[];
        private array $shared=[];
        private array $cache=[];

        /**-------------------------------------------------------------------------*/
        /**
         * Constructor of Service Container
         *
         * @param Application|null $application_instance
         */
        /**-------------------------------------------------------------------------*/
        private function __construct(?Application $application_instance){
            // Apply application instance
            $this->app = $application_instance;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Finds and returns existing instance
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public static function getInstance(?Application $application_instance){
			// Check if already declared
			if(!isset(self::$instance)){
				self::$instance = new ServiceContainer($application_instance);
			}
			// Return instance
			return self::$instance;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Checks bindings, shared instances, and cache for key
         *
         * @param string $key
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        public function has(string $key): bool{
            return (
                array_key_exists($key, $this->bindings) ||
                array_key_exists($key, $this->shared) || 
                array_key_exists($key, $this->cache)
            );
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Bind entry into Bindings (closures) array
         *
         * @param string $key
         * @param [type] $value
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function bind(string $key, $value){
            // TODO: Determine type
            $this->bindings[$key] = $value;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Save entry into Shared (Singletons) Array
         *
         * @param string $key
         * @param [type] $value
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function share(string $key, $value){
            // Store closure in bindings
            $this->bindings[$key] = $value;

            // Store $key in Shared
            // Set value to NULL i.e. instance not used
            $this->shared[$key] = NULL;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Resolves Instance by Key in order:
         * - Shared
         * - Bindings
         * - Reflects
         * - Cache
         * - Exception
         *
         * @param string $key
         * @return void
         * @throws \Exception Unable to resolve
         */
        /**-------------------------------------------------------------------------*/
        public function resolve(string $key){
            // Check Shared and Resolved Instance
            if(array_key_exists($key, $this->shared) && !is_null($this->shared[$key])){
                // Key exists in shared
                // Return instance from shared
                return $this->shared[$key];
                
            }

            // Check Bindings for closure
            if(array_key_exists($key, $this->bindings)){
                // Key exists in bindings as closure
                // Execute Binding Closure
                $resolver = $this->bindings[$key];

                // Instantiate Closure Object
                // Append Application instance as parameter
                $instance = $resolver($this->app);

                // Check that the key is marked in the Shared array
                if(array_key_exists($key, $this->shared) && is_null($this->shared[$key])){
                    // Key exists in shared
                    // Store instance in Shared
                    $this->shared[$key] = $instance;

                    // Return instance from shared
                    return $this->shared[$key];
                }

                // Key Not associated with Shared Array
                // Return Resolved Instance
                return $instance;
            }

            // Key NOT in Shared
            // Key NOT in Bindings
            // Check if key can be instantiated as a Class
            if(class_exists($key)){
                // Key references a classname
                // Reflect classname
                $this->reflect($key);

                // Return instance in cache
                return $this->cache[$key];
            }

            // Key is NOT Classname
            // Key cannot be resolved
            // Throw exception
            throw new \Exception("Unable to resolve key: " . $key);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Returns assoc array of all key => value pairs of Container
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function all(){
            return [
                "bindings" => $this->bindings,
                "shared" => $this->shared,
                "cache" => $this->cache
            ];
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Uses Reflection API to resolve Dependencies and append them to cache array
         *
         * @param string $key
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        private function reflect(string $key){
			/**
			 * Temporary array for holding dependencies for class instantiation
			 * @var array $dependencies
			 */
			$dependencies = [];

			// Validate Classname from $key
			if(!class_exists($key)){
				throw new \Exception("Unable to resolve the provided class in " . __FUNCTION__);
			}

			/**
			 * @var \ReflectionClass
			 */
			$reflectionClass = new \ReflectionClass($key);
			
			/**
			 * Constructor of Reflected Class if present
			 * @var ?object $constructor
			 */
			$constructor = $reflectionClass->getConstructor();
			
			/**
			 * Parameters of Reflected Class if Constructor Present
			 * @var array $parameters
			 */
			$parameters = !is_null($constructor) ? $constructor->getParameters() : [];

			// Check if parameters exist
			if(!empty($parameters)){
				// Parameters Exist
				// Loop parameters
				foreach($parameters as $param){
					// Gather properties

					/**
					 * Type of parameter
					 * @var ?ReflectionType $type
					 */
					$type = $param->getType();

					/**
					 * Name of Class
					 * @var ?string $name
					 */
					$name = $type ? $type->getName() : NULL;

					/**
					 * Is parameter optional
					 * @var boolean $isOptional
					 */
					$isOptional = $param->isOptional();

					/**
					 * Determines if type is NOT a built-in php type
					 * @var boolean $isClass
					 */
					$isClass = $type && !$type->isBuiltin();

					/**
					 * Returns true if default value is available from __construct() parameter
					 * @var boolean $hasDefault
					 */
					$hasDefault = $param->isDefaultValueAvailable();

					// Validate Cases and Throw Exceptions if Invalid
					if(!$isOptional && !$type){
						// Case: Not-optional with Unknown Type
						throw new \Exception("Unable to resolve Class: " . $key . " with Parameter of Unknown Type");
					} else if(!$isOptional && !$isClass){
						// Case: Not-optional and Built-in PHP type (String, Object, etc)
						throw new \Exception("Unable to resolve Class: " . $key . " with Parameter of Built-in Type: " . $type);
					} else if(!$isOptional && !class_exists($name)){
						// Case: Not-optional and Cannot Find Class / Class does NOT Exist
						throw new \Exception("Unable to resolve Class: " .$key. ". Parameter of Class: " .$name. " does NOT EXIST!");
					} else if($isOptional && !$isClass && !$hasDefault){
						// Case: Optional, Type: Built-in; but Default Value not available
						throw new \Exception("Unable to resolve Class: " .$key. ". Opitonal Parameter: " .$name. " has NO Default Value!");
					}

					// Case: Optional Parameter is Not a Class
					if($isOptional && !$isClass){
						/**
						 * Determines and returns default value based on if it is a constant
						 * @var mixed $defaultValue
						 */
						$defaultValue = ($hasDefault && $param->isDefaultValueConstant()) 
							?  constant($param->getDefaultValueConstantName())
							: $param->getDefaultValue();
						
						// Append Default Value to Dependencies array
						$dependencies[] = $defaultValue;
					}

					// Case: Parameter is a Class
					if($isClass){
						// Register Class Instance
						$this->reflect($name);

						// Get instance and append as Dependency
						$dependencies[] = $this->resolve($name);
					}
				}
			}
			// Resolve Instance
			$instance = $reflectionClass->newInstanceArgs($dependencies);

			// Register Instance
			$this->cache[$key] = $instance;
			
			// Exit
			return;
        }
    }
?>