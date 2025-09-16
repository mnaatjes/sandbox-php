<?php

    namespace MVCFrame\FileSystem;
    use MVCFrame\FileSystem\Path;

    class File extends Path {

        private const PHP_EXTENSIONS=["php", "phtml"];

        /**-------------------------------------------------------------------------*/
        /**
         * Determines if object is a file and returns contents
         *
         * @return string
         */
        /**-------------------------------------------------------------------------*/
        public function read(): string{
            // Double-check file exists
            // Throw exception if object is not a file
            if(!$this->isFile()){
                throw new \Exception("Path object is NOT a file!");
            }

            // Get contents and return
            return file_get_contents($this->pathName);

        }

        /**-------------------------------------------------------------------------*/
        /**
         * Writes to file
         * 
         * @uses file_put_contents()
         *
         * @param string $content
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function write(string $content): void{
            // Double-check file exists
            // Throw exception if object is not a file
            if(!$this->isFile()){
                throw new \Exception("Path object is NOT a file!");
            }

            // Put contents
            file_put_contents($this->pathName, $content);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Load file based on extension and return stored value
         *
         * @uses $this->pathName
         * @uses $this->read()
         * @return mixed
         */
        /**-------------------------------------------------------------------------*/
        public function load(){
            // Verify file
            if(!$this->isFile()){
                throw new \Exception("Path: " . $this->pathName . " is NOT a File!");
            }

            // Verify File Exists
            if(!$this->exists()){
                throw new \Exception("File: " . $this->pathName . " does NOT Exist!");
            }

            // Verify file is readable
            if(!is_readable($this->pathName)){
                throw new \Exception("File: " . $this->pathName . " is NOT Readable!");
            }

            // Route to load method
            // Determine method based on extension
            switch($this->getExtension()){
                // Case: PHP File
                case 'php':
                case 'phtml':
                    return $this->loadPHP();

                // Default Case
                default: 
                    throw new \Exception("Unsupported File: " . $this->pathName . " CANNOT be loaded!");
            }
        }
        
        private function loadJSON(){}
        
        private function loadYAML(){}

        /**
         * Returns the value of the content in a php file.
         * - Intended for files which return a value!
         * - For files to include: use require()
         * - Utilizes static function to prevent leakage and control access to $this in file
         *
         * @return void
         */
        private function loadPHP(){
            // Verify file is a php file
            if(!$this->isPHP()){
                throw new \Exception("File: " . $this->pathName . " is NOT a PHP File and cannot be included!");
            }

            // Perform require() within anonymous function
            return (static function ($file){
                return require($file);
            })($this->pathName);
        }
        
        private function loadCSV(){}

        public function execute(){
            // Verify file
            if(!$this->isFile()){
                throw new \Exception("Path: " . $this->pathName . " is NOT a File!");
            }

            // Verify File Exists
            if(!$this->exists()){
                throw new \Exception("File: " . $this->pathName . " does NOT Exist!");
            }

            // Verify file is readable
            if(!is_readable($this->pathName)){
                throw new \Exception("File: " . $this->pathName . " is NOT Readable!");
            }

            // Switch based on extension
            switch($this->getExtension()){
                // Case: php
                case 'php':
                    return $this->executePHP();

                // Default: No case
                default:
                    throw new \Exception("File: ". $this->pathName ." NOT Supported! Unable to execute file!");
            }
        }

        /**
         * Executes a require() of a php file 
         * - Does not return a value!
         * - See load() and loadPHP() to load a return value from a php file
         *
         * @return void
         */
        private function executePHP(){
            // Verify file is a php file
            if(!$this->isPHP()){
                throw new \Exception("File: " . $this->pathName . " is NOT a PHP File and cannot be included!");
            }

            // Perform require() within anonymous function
            return (static function ($file){
                require($file);
            })($this->pathName);
        }

        public function isPHP(){
            // Get extension
            // Check against array
            return in_array($this->getExtension(), self::PHP_EXTENSIONS);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Returns the exension string of the file
         *
         * @return string
         */
        /**-------------------------------------------------------------------------*/
        public function getExtension(){return strtolower(pathinfo($this->pathName, PATHINFO_EXTENSION));}
    }

?>