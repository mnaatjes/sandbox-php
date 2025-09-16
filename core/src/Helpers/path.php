<?php
    // Namespace
    use MVCFrame\FileSystem\FileSystem;

    // Check for path function
    if(!function_exists('path')){

        function path(...$args){
            // Get instance
            //$instance = FileSystem::getInstance();

            throw new \Exception("Helper function not complete!");
        }
    }

?>