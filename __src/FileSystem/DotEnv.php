<?php
    /**
     * Declare Namespace
     */
    namespace MVCFrame\FileSystem;
    use MVCFrame\FileSystem\Path;
    
    /**
     * DotEnv (.env) Class for loading Environment Variables
     * 
     * @docs/versioning-rules.md 2.0.0
     * @since 1.0.0
     * - Created
     */
    class DotEnv {

        /**
         * Filepath to .env file
         * @var Path|Null $path
         */
        protected ?Path $path;

        /**
         * Holds the keys of the loaded environment variables
         * @var string[]
         */
        protected array $loadedKeys = [];

        /**-------------------------------------------------------------------------*/

        /**-------------------------------------------------------------------------*/
        public function __construct(?Path $env_path=NULL){
            $this->path = is_null($env_path) ? path("base.config.env") : $env_path;

            // Load ENV Variables
            $this->load();
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Load .env file
         * 
         * @return void
         * @throws \RuntimeException if .env file not readable
         */
        /**-------------------------------------------------------------------------*/
        public function load(): void
        {
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

        /**
         * Add/update an environment variable.
         *
         * @param string $key
         * @param mixed $value
         * @return void
         */
        public function add(string $key, $value): void
        {
            $key = trim($key);
            if (is_string($value)) {
                putenv(sprintf('%s=%s', $key, $value));
            }
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;

            if (!in_array($key, $this->loadedKeys)) {
                $this->loadedKeys[] = $key;
            }
        }

        /**
         * Remove an environment variable.
         *
         * @param string $key
         * @return void
         */
        public function remove(string $key): void
        {
            if ($this->has($key)) {
                unset($_ENV[$key]);
                unset($_SERVER[$key]);
                putenv($key);
                
                $this->loadedKeys = array_values(array_filter($this->loadedKeys, function($k) use ($key) {
                    return $k !== $key;
                }));
            }
        }

        /**
         * Clear all loaded environment variables.
         *
         * @return void
         */
        public function clear(): void
        {
            foreach ($this->loadedKeys as $key) {
                unset($_ENV[$key]);
                unset($_SERVER[$key]);
                putenv($key);
            }
            $this->loadedKeys = [];
        }

        /**
         * Check if an environment variable exists.
         *
         * @param string $key
         * @return boolean
         */
        public function has(string $key): bool
        {
            return array_key_exists($key, $_ENV);
        }

        /**
         * Get an environment variable.
         *
         * @param string $key
         * @param mixed $default
         * @return mixed
         */
        public function get(string $key, $default = null)
        {
            return $_ENV[$key] ?? $default;
        }

        public function getKeys(){
            return $this->loadedKeys;
        }

        public function getValues(){
            return array_map(function($key){
                return $this->get($key);
            }, $this->loadedKeys);
        }

        public function showKeys(){
            var_dump($this->getKeys());
        }

        public function showValues(){
            var_dump($this->getValues());
        }

        public function getAll(){
            return array_combine($this->getKeys(), $this->getValues());
        }

        public function showAll(){
            var_dump($this->getAll());
        }
    }

        