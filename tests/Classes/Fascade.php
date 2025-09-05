<?php

	namespace MVCFrame\Tests\Classes;

	abstract class Fascade {
		/**
		 * [$resolvedInstance description]
		 * @var [type]
		 */
		protected static $resolvedInstance;

		/**
		 * [getFascadeAccessor description]
		 * @return [type] [description]
		 */
		abstract protected static function getFascadeAccessor();

		/**
		 * [getFascadeRoot description]
		 * @return [type] [description]
		 */
		public static function getFascadeRoot(){
			// Check for existing resolved instance
			if(static::$resolvedInstance){
				return static::$resolvedInstance;
			}

			// Get accessor key
			$accessor = static::getFascadeAccessor();

			// Resolve
			static::$resolvedInstance = app($accessor);

			// Return
			return static::$resolvedInstance;
		}

		/**
		 * [__callStatic description]
		 * @param  [type] $method    [description]
		 * @param  [type] $arguments [description]
		 * @return [type]            [description]
		 */
		public static function __callStatic($method, $arguments){
			$instance = static::getFascadeRoot();

			// Return instance with methods and argumetns as regular class
			return $instance->$method(...$arguments);
		}

	}
?>