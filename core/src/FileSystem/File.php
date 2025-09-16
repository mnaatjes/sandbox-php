<?php

    namespace MVCFrame\FileSystem;
    use MVCFrame\FileSystem\Path;

    class File extends Path {

        private const PHP_EXTENSIONS=["php", "phtml"];
        private const CSV_DEFAULTS=[
            "delimiter"  => ",",
            "enclosure"  => '"',
            "escape"     => "\\",
            "hasHeaders" => true,
        ];

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
                // Case: CSV
                case 'csv':
                    return $this->loadCSV();

                // Case: HTML
                case 'html':
                    return $this->loadHTML();

                // Case: PHP File
                case 'php':
                case 'phtml':
                    return $this->loadPHP();

                // Default Case
                default: 
                    throw new \Exception("Unsupported File: " . $this->pathName . " CANNOT be loaded!");
            }
        }
        
        /**-------------------------------------------------------------------------*/
        /**
         * Returns assoc array of json data from file
         *
         * @return array
         */
        /**-------------------------------------------------------------------------*/
        private function loadJSON(): array{
            // Verify json file
            if(!$this->isJSON()){
                throw new \Exception("File: " . $this->pathName  ." is NOT a JSON file!");
            }
            // Read content from file
            $content = $this->read();

            // Attempt json decode
            // Return as assoc array
            $data = json_decode($content, true);

            // Check last decode for errors
            if(json_last_error() !== JSON_ERROR_NONE){
                // Error Occurred
                throw new \Exception("Failed to parse JSON properly for file: " . $this->pathName . " Error: " . json_last_error_msg());
            }

            // Return data array
            return $data;
        }

        /**-------------------------------------------------------------------------*/
        /**-------------------------------------------------------------------------*/
        private function loadYAML(){}

        /**-------------------------------------------------------------------------*/
        /**
         * Returns the value of the content in a php file.
         * - Intended for files which return a value!
         * - For files to include: use require()
         * - Utilizes static function to prevent leakage and control access to $this in file
         *
         * @return mixed
         */
        /**-------------------------------------------------------------------------*/
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

        /**-------------------------------------------------------------------------*/
        /**
         * Loads CSV and assembles array
         * - Looks for headers and uses them for key=>value assoc array assignment
         * - Check FileSystem constants for delimiters
         *
         * @return array
         */
        /**-------------------------------------------------------------------------*/
        private function loadCSV(): array{

            // Declare collector properties for data and headers
            $headers = [];
            $data    = [];

            // Open file stream
            if(($handle = fopen($this->pathName, 'r')) !== false){
                // File opened properly
                // Init row index
                $index = 0;

                // Perform while loop to process data until fgetcsv is false
                while(($row = fgetcsv(
                    $handle, 
                    0, 
                    self::CSV_DEFAULTS["delimiter"],
                    self::CSV_DEFAULTS["enclosure"],
                    self::CSV_DEFAULTS["escape"],
                )) !== false){

                    // Skip empty arrays
                    if(empty(array_filter($row))){
                        continue;
                    }

                    // Check for headers at row 0
                    if((self::CSV_DEFAULTS["hasHeaders"] === true) && $index === 0){
                        // Push headers
                        $headers[] = $row;
                    } else {
                        // Not on header row

                        // Check headers exist
                        // Assign headers as keys to values
                        if(self::CSV_DEFAULTS["hasHeaders"] && !empty($headers)){
                            // Pad row data if key count doesn't match
                            // Combine into row data and push
                            $data[] = array_combine($headers, array_pad($row, count($headers), NULL));
                        } else {
                            // No headers exist
                            // Push without keys
                            $data[] = $row;
                        }
                    }
                    // Increment Row Index
                    $index++;
                }
                // Loop complete
                // Close file
                fclose($handle);
            } else {
                // Unable to open file and read
                throw new \Exception("Could not open CSV File: " . $this->pathName);
            }

            // Return data array
            return $data;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Return HTML Content
         *
         * @return string
         */
        /**-------------------------------------------------------------------------*/
        private function loadHTML(): string{return $this->read();}

        /**-------------------------------------------------------------------------*/
        /**
         * Execute file by evaluating extension and running appropriate execution method
         * - To extract a value from a file see $this->load()!
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
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

        /**-------------------------------------------------------------------------*/
        /**
         * Executes a require() of a php file 
         * - Does not return a value!
         * - See load() and loadPHP() to load a return value from a php file
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
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

        /**-------------------------------------------------------------------------*/
        /**
         * Renders a file with extracted $data array values
         *
         * @param array $data
         * @return string Rendered and executed php file string with data extracted
         */
        /**-------------------------------------------------------------------------*/
        public function render(array $data=[]) {
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

            // Determine which Render() method to execute
            switch($this->getExtension()){

                // Case: HTML
                case 'html':
                    return $this->renderHTML($data);

                // Case: php
                case 'php':
                    return $this->renderPHP($data);

                // Default: No case
                default:
                    throw new \Exception("File: ". $this->pathName ." NOT Supported! Unable to execute file!");
            }
        }

        private function renderHTML(array $data){
            // Verify html document
            if(!$this->isHTML()){
                throw new \Exception("File: " . $this->pathName . " is NOT am HTML File and cannot be included!");
            }
            // Extract Content
            $content = $this->loadHTML();

            // Cycle through each data element and replace keys with value
            foreach($data as $key => $value){
                $content = str_replace("{{ " . $key . " }}", (string)$value, $content);
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Renders (extracts data and executes) php file
         *
         * @param array $data
         * @return string
         */
        /**-------------------------------------------------------------------------*/
        private function renderPHP(array $data): string{
            // Verify file is a php file
            if(!$this->isPHP()){
                throw new \Exception("File: " . $this->pathName . " is NOT a PHP File and cannot be included!");
            }

            // Start output Buffer
            ob_start();

            // Use anonymous static function to execute and bind data
            (static function ($file, array $data){
                // Perform extract with data array
                extract($data);

                //Execute PHP File
                require($file);
            })($this->pathName, $data);

            // Get output buffer content
            // Clean output buffer
            return ob_get_clean();
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Determines if a file is a php file by checking its extension against an array
         *
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        public function isPHP(): bool{
            // Get extension
            // Check against array
            return in_array($this->getExtension(), self::PHP_EXTENSIONS);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Checks if file has .json extension
         *
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        public function isJSON(): bool{return $this->getExtension() == "json";}

        /**-------------------------------------------------------------------------*/
        /**
         * Checks if file has .html extension
         *
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        public function isHTML(): bool{return $this->getExtension() == "html";}

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