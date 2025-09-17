<?php

    namespace MVCFrame\Foundation;
    use MVCFrame\Foundation\RegistryMetaData;

    class RegistryItem {
        
        private RegistryMetaData $metaData;
        private $value;

        public function __construct($value, RegistryMetaData $meta_data){
            // Make meta-data
            $this->metaData = $meta_data;

            // Assign Value
            $this->value = $value;
        }

        public function getValue(){return $this->value;}

        public function getMetaData(){return $this->metaData;}

        public function update(){}


        public function resolveType(){
            // Use gettype
            $type = gettype($this->value);
            // Resolve Type of meta-data
            if(is_object($this->value)){
                // Determine if class

            }

            // Return type
            return $type;
        }

        private function resolveClass(){

        }
    }
?>