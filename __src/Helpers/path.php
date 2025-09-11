<?php

	// Check for path function
	use MVCFrame\Foundation\Application;
	use MVCFrame\FileSystem\Path;

	if(!function_exists('path')){
		/**
		 * Path function returns PathRegistry instance from Application Instance
		 * @param  [type] $args [description]
		 * @return [type]       [description]
		 */
		function path(...$args){
			// Path Registry Instance
			$instance 		= Application::getInstance();
			$pathRegistry 	= $instance->getPathRegistry();

			// Case: No arguments
			// Return PathRegistry Instance
			if(empty(func_get_args())){
				return $pathRegistry;
			}

			// Check Number of Arguments:
			if(count(func_get_args()) === 1){
				// Single Argument Cases:
				// Define parameter
				$param = $args[0];

				// Determine Case
				if(str_contains($param, '/') || str_contains($param, '\\')){
					// Case: '/path/to/file' (string)$path
					// Return Path::create()
					return Path::create($param);
					
				} else if(str_contains($param, '.')){
					// Case: 'group.name.to.file' $alias
					// Return PathRegistry->getPath()
					return $pathRegistry->getPath($param);
				}
			} else if(count(func_get_args()) >= 2){
				// 2 or More Arguments Cases:
				// Check all arguments are Path Objects
				$all = array_reduce($args, function(bool $every, $item){
					if($every === false){
						return false;
					}
					return is_a($item, Path::class);
				}, true);

				// Case: All arguments are Path instances
				if($all === true){
					// Return Path::join
					return Path::join(...$args);
				}

				// Case: NOT all arguments Path Instances
				// Check for 3 Arguments all strings
				if(count(func_get_args()) === 3){
					// Check all 3 are strings
					$allStr = array_reduce($args, function(bool $every, $item){
						// Check every
						if($every === false){
							return false;
						}

						// Return Evaluation
						return is_string($item) || is_a($item, Path::class);
					}, true);

					// Evaluate if all strings
					if($allStr === true){
						// Check first argument is valid group
						// Check format second
						// Check Path
						$group = (bool)preg_match('/^[a-z]*$/', $args[0]) ? $args[0] : NULL;
						$name  = $args[1];
						$path  = is_a($args[2], Path::class) ? (string)$args[2] : $args[2];

						// Validate again
						if(!is_null($group) && !is_null($path)){
							// Return PathRegistry->register()
							return $pathRegistry->register($group, $name, $path);
						}
					}
				}
			}
		}
	}
?>