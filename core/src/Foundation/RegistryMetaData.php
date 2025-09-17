<?php

    namespace MVCFrame\Foundation;

    class RegistryMetaData {

        private const PERMISSIONS_DEFAULT=0;

        private ?string $type=NULL;
        private int $tsRegistered;
        private int $tsUpdated;

        public function __construct(
            private array $tags=[],
            private bool $readOnly=false,
            private ?string $category=NULL,
            private ?int $tsExpires=NULL,
            private ?string $locationRegistered,
            private int $permissions=self::PERMISSIONS_DEFAULT
        ){

            // Generate TS Registered
            $this->tsRegistered = time();
            $this->tsUpdated    = time();

            // Set Default Permissions
            $this->permissions = self::PERMISSIONS_DEFAULT;
        }
         
        public function getType(){return $this->type;}
        public function taggedWith(string $tag): bool{return in_array($tag, $this->tags);}

        public function inCategory(string $category): bool{return $this->category === $category;}

        public function isReadOnly(): bool{return $this->readOnly;}

        public function isCached(): bool{return is_int($this->tsExpires);}
        
        public function toArray(){

            return [
                "type"  => $this->type,
                "tags"  => $this->tags,
                "readOnly"  => $this->readOnly,
                "category"  => $this->category,
                "tsRegistered"  => $this->tsRegistered,
                "tsUpdated"     => $this->tsUpdated,
                "tsExpires"     => $this->tsExpires,
                "permissions"   => $this->permissions,
                "locationRegistered" => $this->locationRegistered
            ];
        }

        private function createTS(){

        }
    }
?>