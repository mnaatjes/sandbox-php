<?php

    namespace MVCFrame\Support;

    class KeyHashMap {

        /**
         * @var array Internal storage for the hash map
         */
        private array $storage=[];

        /**
         * @var array Data array from an assoc array nested structure
         */
        private array $data=[];

        /**
         * @var callable Function that takes an item and returns a string key
         */
        private $keyGen;

        public function __construct(callable $key_generator){
            // Set Key Generator 
            $this->keyGen = $key_generator;
        }

        public function generateKey($item){
            return ($this->keyGen)($item);
        }

        public function has($item){
            $key = $this->generateKey($item);

            return isset($this->storage[$key]);
        }
    }
?>