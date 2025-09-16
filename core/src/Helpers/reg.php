<?php

    // Check for reg function
    use MVCFrame\Foundation\ServiceRegistry;

    if(!function_exists('reg')){
        function reg(...$args){
            // Get instance
            $instance = ServiceRegistry::getInstance();

            // Determine output given number of arguments
            switch(func_num_args()){
                // Case: Two Arguments
                case 2:
                    // Check if string
                    if(!is_string($args[0])){
                        throw new \TypeError("First argument must be type String!");
                    }
                    // Perform register
                    return $instance->register($args[0], $args[1]);

                // Case: Single Argument
                case 1:
                    // Check if string
                    if(!is_string($args[0])){
                        throw new \TypeError("Single argument must be type String!");
                    }
                    // Return get
                    return $instance->lookup($args[0]);

                // Case: No arguments
                case 0:
                    // Return ServiceRegistry instance
                    return $instance;

                // Too Many Arguments
                default:
                    throw new \Exception("Too many arguments for ServiceRegistry helper function!");
            }
        }
    }

?>