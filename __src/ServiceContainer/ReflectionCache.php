<?php

	namespace MVCFrame\ServiceContainer;

	/**
	 * Reflection Cache for Storing and Accessing auto-wired object instances
	 * @version 1.0.0
	 * 
	 */
	class ReflectionCache {

		/** @var array Resolved Instances in key=>value pairs */
		private array $resolvedInstances=[];

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function register(string $key): void{

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
						$this->register($name);

						// Get instance and append as Dependency
						$dependencies[] = $this->getInstance($name);
					}
				}
			}
			// Resolve Instance
			$instance = $reflectionClass->newInstanceArgs($dependencies);

			// Register Instance
			$this->resolvedInstances[$key] = $instance;
			
			// Exit
			return;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Grabs instance from cache
		 *
		 * @param string $key
		 * @return void
		 */
		/**-------------------------------------------------------------------------*/
		public function getInstance(string $key){
			// Validate existance in cache
			if(!$this->has($key)){
				// Attempt to register
			}

			// Resolve instance and return
			return $this->resolvedInstances[$key];
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Determines if a ClassName key and instance exists in the cache
		 *
		 * @param string $key
		 * @return boolean
		 */
		/**-------------------------------------------------------------------------*/
		public function has(string $key): bool{
			return array_key_exists($key, $this->resolvedInstances);
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Returns array of resolved instances for debugging
		 *
		 * @return array
		 */
		/**-------------------------------------------------------------------------*/
		public function getInstances(): array{
			return $this->resolvedInstances;
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
	}
?>