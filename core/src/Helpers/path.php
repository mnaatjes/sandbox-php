<?php
    // Namespace
    use MVCFrame\FileSystem\FileSystem;

    // Check for path function
    if(!function_exists('path')){

        function path(...$args){
            // Get instance
            $instance = FileSystem::getInstance();

            // Determine output given number of arguments
            switch(func_num_args()){
                case 2:
                    // Check if string
                    if(!is_string($args[0])){
                        throw new \TypeError("First argument must be type String!");
                    }
                    // Perform register
                    return $instance->set($args[0], $args[1]);

                // Case: Single Argument
                case 1:
                    // Check if string
                    if(!is_string($args[0])){
                        throw new \TypeError("Single argument must be type String!");
                    }
                    // Return get
                    return $instance->get($args[0]);

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