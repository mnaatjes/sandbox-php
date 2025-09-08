<?php

	namespace MVCFrame\FileSystem;
	use MVCFrame\Foundation\Utility;

	class PathRegistry {

		private ?Path $rootDir;

		/**
		 * Required Directories assoc array by environment
		 * @var const REQUIRED_DIR
		 */
		private const REQUIRED_DIR=[
			"app" 		=> ["dev" => "", "production" => "/app"],
			"config" 	=> ["dev" => "/bootstrap", "production" => "/config"],
			"database" 	=> ["dev" => "/db", "production" => "/database"],
			"public" 	=> ["dev" => "/../public", "production" => "/public"],
			"storage" 	=> ["dev" => "/storage", "production" => "/storage"],
			"resource" 	=> ["dev" => "/resources", "production" => "/resources"],
			"routes"	=> ["dev" => "/routes", "production" => "/routes"],
		];

		/**
		 * Central Registry Array of all Paths
		 *
		 * @var array
		 */
		private array $registry=[];

		/**-------------------------------------------------------------------------*/
		/**
		 * Construct for PathRegistry instance
		 *
		 * @param Path $rootDir
		 * @param string $env
		 * @return void
		 */
		/**-------------------------------------------------------------------------*/
		public function __construct(Path $rootDir, $env){
			// Assign Root Directory
			$this->rootDir = $rootDir;

			// Configurate Base Paths
			foreach(self::REQUIRED_DIR as $key => $dir){
				// Determine Environment of Framework
				if($env === "dev"){
					// Framework in Development Environment
					// Combine and instantiate path
					$path = Path::create(
						$this->rootDir . $dir[$env]
					);

					// Register paths
					$this->register("base", $key, $path);
				}
			}
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Create the alias string
		 *
		 * @param string $group
		 * @param string $name
		 * @return void
		 */
		/**-------------------------------------------------------------------------*/
		public function createAlias(string $group, string $name){
			// Sanitize and Normalize Group
			// [a-z] to lower
			$group = Utility::sanitizeLetters($group, Utility::CASE_LOWER);

			// Sanitize and Normalize Name
			// Str to lower
			// Trim - remove whitespace
			// Remove all characters but [a-z] and periods
			$name  = strtolower(trim($name));
			$name  = preg_replace('/[^a-z.]/', '', $name);

			// Assemble Alias
			// Validate: Ensure it doesnt already exist
			$alias = $group . "." . $name;
			if($this->hasAlias($alias)){
				// Throw exception
				throw new \Exception(printf('Alias: %s Already Exists and has Path: ', $alias, $this->getPath($alias)));
			}

			// Validation Check Passed
			// Return alias
			return $alias;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Registers and Adds alias to Registry
		 * - Assembles alias from $group and $name
		 * - Validates and sanitizes
		 * - Assigns path to alias in registry
		 *
		 * @param string $group
		 * @param string $name
		 * @param string|Path $path
		 * @return void
		 */
		/**-------------------------------------------------------------------------*/
		public function register(string $group, string $name, string|Path $path): void{
			// Create Alias
			$alias = $this->createAlias($group, $name);

			// Ensure $path parameter is a Path Object
			$path = is_a($path, Path::class) ? $path : Path::create($path);

			// Verify Path
			if(!$path->exists()){
				// Path does not exist!
				throw new \Exception(printf('Path: %s does NOT exist!', (string)$path));
			}
			// Add to Registry
			$this->add($alias, $path);
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Uses alias and path to add element to registry array
		 *
		 * @param string $alias
		 * @param string|Path $path
		 * @return void
		 */
		/**-------------------------------------------------------------------------*/
		private function add(string $alias, string|Path $path): void{
			// Explode alias into array keys
			$keys = explode(".", $alias);

			// Reference Registry
			$temp = &$this->registry;

			// Loop and Populate Temp Array
			foreach($keys as $key){
				if(!isset($temp[$key]) || !is_array($temp[$key])){
					// Form new array
					$temp[$key] = [];
				}
				// Key exists as array
				// Change $temp reference 1 level down
				// $temp now references $registry->array[key]
				$temp = &$temp[$key];
			}
			// Alias injected into $temp / $this->reference
			// Assign path to $this->registry[$key] by reference
			$temp = $path;
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function show(bool $json=false): void{
			$temp = array_map(
				function($group){
					return array_map(function($name){
						return (string)$name;
					}, $group);
				}, $this->registry
			);

			// Format
			$temp = $json ? json_encode($temp, JSON_PRETTY_PRINT) : $temp;

			// Display
			var_dump($temp);
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function listAliases(): array{
			// Register $temp as Registry
			$temp = &$this->registry;

			// Return Aliases
			return $this->collectKeys($temp);
			
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function listPaths(){
			// Reference Registry
			$temp 		= &$this->registry;
			$results 	= $this->collectPaths($temp);
			return $results;
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function collectPaths($arr){
			$results = [];

			foreach($arr as $value){
				if(is_array($value)){
					$results = array_merge($results, $this->collectPaths($value));
				}
				
				if(is_a($value, Path::class)){
					$results[] = $value;
				}
			}
			return $results;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Collects nested array keys to form all aliases
		 *
		 * @param [type] $arr
		 * @param string $prefix
		 * @return array
		 */
		/**-------------------------------------------------------------------------*/
		private function collectKeys($arr, $prefix=""): array{
			$results = [];

			foreach($arr as $key => $value){
				$newKey = $prefix ? $prefix . "." . $key : $key;
				if(is_array($value) && !empty($value)){
					$results = array_merge($results, $this->collectKeys($value, $newKey));
				} else {
					$results[] = $newKey;
				}
			}

			// Return Results
			return $results;
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		private function format(){}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function updatePath(string $alias, string|Path $new_path): void{
			// Check is alias exists
				// Throw exception if not founds
			
			// Create Path instance

			// Overwrite old path instance with new
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Checks if alias exists in registry and returns True or False
		 *
		 * @param string $alias
		 * @return boolean
		 */
		/**-------------------------------------------------------------------------*/
		public function hasAlias(string $alias): bool{
			$keys = explode(".", $alias);
			$currentLevel = $this->registry;

			foreach ($keys as $key) {
				if (!is_array($currentLevel) || !array_key_exists($key, $currentLevel)) {
					return false;
				}
				$currentLevel = $currentLevel[$key];
			}
			return true;
		}
		public function aliasExists(string $alias): bool{return $this->hasAlias($alias);}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function hasGroup(string $group){
			return is_array($this->registry[$group]);
		}
		public function groupExists(string $group){ return $this->groupExists($group);}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function hasPath(string|Path $path){
			// Check type
			if(is_a($path, Path::class)){
				$path = (string)$path;
			}

			// Loop iterator
			$temp = &$this->registry;
			foreach($temp as $key => $value){
				
			}
			// Return in array bool
			throw new \Exception("Function not Finished");
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getGroup(string $group){
			// TODO: Change to work with depth
			// Return array from group
			if($this->hasGroup($group)){
				return $this->registry[$group];
			} else {
				// Group does not exist
				// Throw Exception
				throw new \Exception("Group: " .$group. "does not exist!");
			}

		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getPath(string $alias){
			/**
			 * Default Value for Result
			 * @var ?Path $value
			 */
			$value = NULL;

			// Check if path exists
			if($this->aliasExists($alias)){
				$elements 	= explode(".", $alias);
				$temp 		= &$this->registry;
				$keys 		= [];
				$ref 		= "";
				$flag 		= false;
				
				foreach($elements as $key){
					if(array_key_exists($key, $temp)){
						$keys[] = $key;
						$temp 	= &$temp[$key];

						// Check Ref
						$imploded = implode(".", $keys);
						if($alias === $imploded){
							$flag = true;
							break;
						}
					}
				}

				if($flag === true){
					// Get nested value from keys
					$registry 	= $this->registry;
					$value 		= array_reduce($keys, function($carry, $key){
						// Check $registry for array keys until nothing left
						if(is_array($carry[$key]) && isset($carry[$key])){
							// Keep nesting into registry array
							return $carry[$key];
						} else if(isset($carry[$key]) && is_a($carry[$key], Path::class)){
							return $carry[$key];
						}
						return NULL;
					}, $registry);
				}
			}

			// Check Value
			if(is_null($value)){
				// Throw Exception
				throw new \Exception("Path does NOT exist for Alias: " . $alias);
			}

			// Return Path
			return $value;
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getRootDir(){}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getAppDir(){}
		
		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getConfigDir(){}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getDBDir(){}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getPublicDir(){}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getStorageDir(){}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getResourceDir(){}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function getRoutesDir(){}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function listFiles(){}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function listDirectories(){}
		
		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function listGroups(){}
		
		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
	}
?>