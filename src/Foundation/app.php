<?php

	use MVCFrame\Tests\Classes\ServiceContainer;

	if(!function_exists("app")){
	    function app(string $key){
	        return ServiceContainer::getInstance()->resolve($key);
	    }
	}
?>