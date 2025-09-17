<?php
    /**
     * Declare Namespace
     */
    namespace MVCFrame\Support;
    use MVCFrame\FileSystem\File;


    /**
     * DotEnv (.env) Class for loading Environment Variables
     * 
     * @docs/versioning-rules.md 2.0.0
     * @since 1.0.0
     * - Created
     */
    class DotEnv {

        private static ?DotEnv $instance;

        /**
         * Filepath to .env file
         * @var Path|Null $path
         */
        protected ?File $path;

        /**
         * Holds the keys of the loaded environment variables
         * @var string[]
         */
        protected array $loadedKeys = [];

        /**-------------------------------------------------------------------------*/
        /**
         * Constructor for DotEnv Class
         */
        /**-------------------------------------------------------------------------*/
        public function __construct(?File $env_path){
            // Check Instance
            if(isset(self::$instance)){
                // Class already Instantiated
                throw new \Exception("A DotEnv instance has already been created. Unable to create another. To use, see: DotEnv::getInstance()");
            }

            // Set Instance
            self::$instance = $this;

            // Set default path
            $this->path = $env_path;

            // Load ENV Variables
            $this->load();
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Finds and returns existing instance
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public static function getInstance(){
			// Check if already declared
			if(is_null(self::$instance)){
                // Check for path parameter
                // Throw Exception
				throw new \Exception("DotEnv has NOT been properly instantiated!");
			}
			// Return instance
			return self::$instance;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Load .env file
         * 
         * @return void
         * @throws \RuntimeException if .env file not readable
         */
        /**-------------------------------------------------------------------------*/
        private function load(): void{
            /**
             * Open file and load lines into array
             * @var array $lines
             */
            $path = (string)$this->path;
            if (!is_readable($path) || is_dir($path)) {
                throw new \RuntimeException(sprintf('Unable to read the environment file at %s.', $path));
            }
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            /**
             * Loop and assign as Environment Variables:
             * - Skip comments
             * - Grab $key => $value pairs
             * - Sanitize entires & strip quotes
             * - Set $_ENV Variables
             */
            foreach($lines as $line){
                // Skip comments
                if(strpos(trim($line), "#") === 0){
                    continue;
                }

                // Explode into array of key, value pairs to validate
                $parts = explode("=", $line, 2);

                // Check for equals operator
                if(count($parts) !== 2){
                    continue;
                }

                // Grab key, value pairs
                list($key, $value) = $parts;

                // Sanitize
                $key    = trim($key);
                $value  = trim($value);

                // Strip quotes from values if exist
                if(in_array($value[0] ?? '', ['"', "'"])) { // <- if NULL, set empty to avoid error
                    $value = substr($value, 1, -1);
                }

                // Set ENV Variables
                if(!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)){
                    // Check if the value is a JSON string
                    $decoded = json_decode($value, true);
                    if(json_last_error() === JSON_ERROR_NONE){
                        // Store --> cannot use setENV
                        $_ENV[$key]     = $decoded;
                        $_SERVER[$key]  = $decoded;

                    } else {
                        // Put ENV variables (string values)
                        putenv(sprintf('%s=%s', $key, $value));
                        $_ENV[$key]     = $value;
                        $_SERVER[$key]  = $value;
                    }
                    $this->loadedKeys[] = $key;
                }
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Add/update an environment variable.
         *
         * @param string $key
         * @param mixed $value
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function add(string $key, $value): void
        {
            // Normalize key and put values
            $key = trim($key);
            if (is_string($value)) {
                putenv(sprintf('%s=%s', $key, $value));
            }

            // Set values in $_ENV and $_SERVER
            $_ENV[$key]     = $value;
            $_SERVER[$key]  = $value;

            // Update Loaded Keys
            if (!in_array($key, $this->loadedKeys)) {
                $this->loadedKeys[] = $key;
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Remove an environment variable.
         *
         * @param string $key
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function remove(string $key): void{
            if ($this->has($key)) {
                unset($_ENV[$key]);
                unset($_SERVER[$key]);
                putenv($key);
                
                // Update Array Keys
                $this->loadedKeys = array_values(array_filter($this->loadedKeys, function($k) use ($key) {
                    return $k !== $key;
                }));
            }
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Clear all loaded environment variables.
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function clear(): void{
            foreach ($this->loadedKeys as $key) {
                unset($_ENV[$key]);
                unset($_SERVER[$key]);
                putenv($key);
            }
            $this->loadedKeys = [];
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Check if an environment variable exists.
         *
         * @param string $key
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        public function has(string $key): bool{
            return array_key_exists($key, $_ENV);
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Get an environment variable.
         *
         * @param string $key
         * @param mixed $default
         * @return mixed
         */
        /**-------------------------------------------------------------------------*/
        public function get(string $key, $default = null){
            return $_ENV[$key] ?? $default;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Return assoc array of Key => Value pairs of ENV variables
         *
         * @return array
         */
        /**-------------------------------------------------------------------------*/
        public function all(): array{
            return array_combine($this->loadedKeys, array_map(function($key){
                return $this->get($key);
            }, $this->loadedKeys));
        }
    }

        