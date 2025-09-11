<?php

	// Check for function
	if(!function_exists("utils")){
		/**-------------------------------------------------------------------------*/
		/**
		 * Helper function for Utility Class
		 * @param  [type] $args [description]
		 * @return [type]       [description]
		 */
		/**-------------------------------------------------------------------------*/
		function utils(...$args){
			// Return instance of Utility Class
			return \MVCFrame\Foundation\Utility::getInstance();
		}
	}
?>