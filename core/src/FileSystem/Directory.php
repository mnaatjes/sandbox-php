<?php

    namespace MVCFrame\FileSystem;

    class Directory extends Path {

        /**
         * Returns array of directory contents
         *
         * @return array
         */
        public function listContents(): array{
            // Double-check is Directory
            if(!$this->isDir()){
                throw new \Exception("Path is NOT a Directory!");
            }

            // Assemble array
            // Remove [".", ".."]
            return array_diff(scandir($this->pathName), [".", ".."]);
        }

        public function makeDir(){
            // Double check this path object does not exist
            if(!$this->exists()){
                // Set permissions and create directory
                mkdir($this->pathName, 0777, true);
            }
        }
    }
?>