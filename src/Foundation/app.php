<?php

	use MVCFrame\Tests\Classes\ServiceContainer;

	/**
	 * Check for app() method
	 */
	if(!function_exists("app")){
		/**
		 * Helper function linking ServiceContainer to app() interface
		 *
		 * @param string $key
		 * @return void
		 */
	    function app(string $key){
	        return ServiceContainer::getInstance()->resolve($key);
	    }
	}
?>