<?php

    namespace MVCFrame\Foundation;

    #[\Attribute(\Attribute::TARGET_CLASS)]
    class ServiceCategory {

        private string $name;

        public function __construct(string $name){
            $this->name = $name;
        }

        public function getName(): string{return $this->name;}

    }
?>