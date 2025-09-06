<?php

	use MVCFrame\ServiceContainer\Container;

	/**
	 * Check for app() method
	 */
	if(!function_exists("app")){
		/**-------------------------------------------------------------------------*/
		/**
		 * Helper function linking ServiceContainer to app() interface
		 * @param  string   $key     [description]
		 * @param  callable $handler [description]
		 * @return [type]            [description]
		 */
		/**-------------------------------------------------------------------------*/
	    function app(string $key="", callable $handler=NULL){
	    	
	    	// Check for parameter zero parameters
	    	if(empty(trim($key)) && is_null($handler)){
	    		// Return entire Service Container object
	    		return Container::getInstance();
	    	}

	    	// Check for one or multiple parameters
	    	if(!empty(trim($key)) && is_null($handler)){
		    	// Return resolved instance
		        return Container::getInstance()->resolve($key);

	    	} else if (!empty($key) && !is_null($handler)){
	    		// Bind to $bindings
	    		$container = Container::getInstance();
	    		$container->bind($key, $handler);
	    	}

	    }
	}
?>