<?php

    use MVCFrame\Support\DotEnv;

    // Check for DotEnv Instance
    if(!function_exists('env')){
        /**
         * Helper function for DotEnv Method
         *
         * @param string $key
         * @param mixed $value
         * @return void|DotEnv|mixed
         */
        function env(...$args){
            // Get instance of DotEvn
            $instance = DotEnv::getInstance();

            // Check number of arguments
            switch(count(func_get_args())){
                // Two Arguments
                case 2:
                    // Validate 1st argument
                    if(!is_string($args[0])){
                        // Throw exception
                        throw new \Exception("ENV variable key must be a string!");                        
                    }

                    // Execute env save
                    $instance->add($args[0], $args[1]);
                    
                // Single argument
                case 1:
                    // Validate
                    if(!is_string($args[0])){
                        // Throw exception
                        throw new \Exception("ENV variable key must be a string!");
                    }
                    // Return value
                    return $instance->get($args[0]);

                // Case: 0 arguments
                case 0:
                    // No arguments
                    // Return instance
                    return $instance;

                // Too Many Arguments
                default:
                    // Throw Exception
                    throw new \Exception("Too many arguments! max number of arguments is 2: string key, mixed value");
            }
            
        }
    }

?>