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
        private array $instances=[];

        /**-------------------------------------------------------------------------*/
        /**
         * Constructor of Service Container
         *
         * @param Application|null $application_instance
         */
        /**-------------------------------------------------------------------------*/
        private function __construct(){}

        /**-------------------------------------------------------------------------*/
        /**
         * Finds and returns existing instance
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public static function getInstance(){
			// Check if already declared
			if(!isset(self::$instance)){
				self::$instance = new ServiceContainer();
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
                array_key_exists($key, $this->bindings) || array_key_exists($key, $this->instances)
            );
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Bind entry into Bindings (closures) array
         *
         * @param string $key
         * @param [type] $resolver
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function bind(string $key, $resolver){
            // Store closure in bindings
            $this->bindings[$key] = [
                "resolver"  => $resolver,
                "shared"    => false
            ];
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Save entry into Shared (Singletons) Array
         *
         * @param string $key
         * @param [type] $resolver
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function share(string $key, $resolver){
            // Store closure in bindings
            $this->bindings[$key] = [
                "resolver"  => $resolver,
                "shared"    => true
            ];
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
            // Check Shared / Cache Instances
            if(isset($this->instances[$key])){
                // Return Existing Instance
                return $this->instances[$key];
            }

            // Check if key exists in bindings
            if(isset($this->bindings[$key])){
                // Bindings Contains Resolver
                // Pull and Resolve to instance
                $instance = $this->bindings[$key]["resolver"]();
            }

            // Not found in Instances OR Bindings Arrays
            // Attempt reflection
            else {
                // Get instance from Reflection Method
                $instance = $this->reflect($key);
            }

            // Check if Supplied Key is Shared
            $isShared = (isset($this->bindings[$key]) && ($this->bindings[$key]["shared"] === true));

            // Check if Supplied Key was Reflected; i.e. not bound
            $wasReflected = !$this->bindings[$key];

            // Store Instance in instances if marked as Shared
            // Store in instances if instance was Reflected
            if($isShared || $wasReflected){
                // Store instance in $instances array
                $this->instances[$key] = $instance;
            }

            // Return Instance
            return $instance;
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
                "instances" => $this->instances
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
        public function reflect(string $key){
            
			/**
             * Create Reflection class from Classname key
			 * @var \ReflectionClass
			 */
			$reflectionClass = new \ReflectionClass($key);

			/**
			 * Constructor of Reflected Class if present
			 * @var ?object $constructor
			 */
			$constructor = $reflectionClass->getConstructor();

            /**
             * Dependencies - if they exist - for the Reflected Class Instance
             * @var ?array $dependencies
             */
            $dependencies = [];

            if(!is_null($constructor)){
                // Constructor Exists
                // Check for parameters
                /**
                 * Parameters of Reflected Class if Constructor Present
                 * @var array $parameters
                 */
                $parameters = $constructor->getNumberOfParameters() > 0 ? $constructor->getParameters() : NULL;

                // Check if parameters exist and loop each to collect as dependencies
                if(!is_null($parameters)){
                    // Parameters Exist
                    // Loop parameters

                    foreach($parameters as $param){
                        /** @var ?object $type Object containing type information */
                        $type = $param->getType();

                        // Determine if Class or Interface
                        if($type && !$type->isBuiltin()){
                            // Resolve Dependency Class or Interface
                            $dependencies[] = $this->resolve($type->getName());
                        }

                        // Check If Primitive / Built-in has Default Value
                        else if($param->isDefaultValueAvailable()){
                            // Default exists
                            // Push default parameter value to dependencies
                            $dependencies[] = $param->getDefaultValue();
                        }

                        // Type is NOT a Class / Interface
                        // Type is NOT a primitive / built-in
                        // Parameter cannot be resolved
                        else {
                            // Throw Unresolved Type Exception
                            throw new \Exception("Unable to resolve primitive or un-typed parameter: " . $type->getName());
                        }
                    }
                }
            }

            // Return instance with dependencies if applicable
            // Throws ReflectionException if unable to resolve instance
            return empty($dependencies) && is_null($constructor)
                ? $reflectionClass->newInstance()
                : $reflectionClass->newInstanceArgs($dependencies);
        }
    }
?>