<?php

    namespace MVCFrame\Foundation;

    class ServiceRegistry {

        /**
         * Top Level Categories used to flatten for search() method
         * @var const CATEGORIES
         */
        private const CATEGORIES=[
            "file",
            "dir",
            "path",
            "config",
            "default"
        ];

        private ?Application $app;

        private static ?ServiceRegistry $instance;

        private array $data=[];

        public function __construct(){}

        /**-------------------------------------------------------------------------*/
        /**
         * Finds and returns existing instance
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public static function getInstance(){
			// Check if already declared
			if(!isset(self::$instance)){
				self::$instance = new ServiceRegistry();
			}
			// Return instance
			return self::$instance;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Sets a new value into the registry data array
         *
         * @param string $alias
         * @param [type] $value
         * @param array $other_metadata
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function register(string $alias, $value, array $other_metadata=[]){
            // Explode dot-notation alias
            $keys = explode(".", $alias);

            // Get category for meta-data from keys
            // Create meta-data object
            $metaData = new RegistryMetaData(...array_merge(
                $other_metadata, [
                    "category" => $keys[0]
                ]
            ));

            // Create reference to data array
            $temp = &$this->data;

            // Loop reference data array
            foreach($keys as $key){
                // Create new Key:
                // Check if key exists in data reference array
                // Check if key value is an array
                if(!isset($temp[$key]) || !is_array($temp[$key])){
                    // establish new array at key
                    $temp[$key] = [];
                }

                // Increate depth search
                // Key exists
                // Change reference 1 level down
                $temp = &$temp[$key];
            }

            // Save value under nested key
            $temp = $value;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Check if the Registry contains a value based on the alias
         *
         * @param string $alias
         * @return boolean
         */
        /**-------------------------------------------------------------------------*/
        public function has(string $alias){
            // Explode dot-notation alias
            $keys = explode(".", $alias);

            // Create reference to data array
            $temp = &$this->data;

            // Check keys by level:
            // Checks in order from outer to inner
            // Loop Keys
            foreach($keys as $key){
                // Key not set
                // Key does not exist in data reference array
                if(!isset($temp[$key])){
                    return false;
                }

                // Key exists
                // Reference deeper into array
                $temp = &$temp[$key];
            }

            // Loop Finished
            // All keys / full alias exists
            return true;
        }

        /**-------------------------------------------------------------------------*/
        /**
         * Search for and return value from Registry data
         *
         * @param string $alias
         * @param [type] $default
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function lookup(string $alias, $default=NULL){
            // Explode dot-notation alias
            $keys = explode(".", $alias);

            // Create reference to data array
            $temp = &$this->data;

            // Loop Keys by depth
            foreach($keys as $key){
                // Check if key has is set in array
                if(!isset($temp[$key])){
                    // Value does NOT exist
                    // Return default
                    return $default;
                }

                // Key is set
                // Reference deeper level
                $temp = &$temp[$key];
            }
            // Loop Complete
            // Temp at value
            // Return temp reference to value
            return $temp;
        }

        public function search(string $sub_alias): array{


            // Return Default
            return [];
        }
        /**-------------------------------------------------------------------------*/
        /**
         * Returns Contents of Data array
         *
         * @return void
         */
        /**-------------------------------------------------------------------------*/
        public function all(){
            return $this->data;
        }
    }
?>
