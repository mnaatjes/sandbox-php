<?php

	namespace MVCFrame\FileSystem;
	use MVCFrame\FileSystem\Directory;
	use MVCFrame\FileSystem\File;

	class Path {

		protected readonly ?string $pathName;

		/**-------------------------------------------------------------------------*/
		/**
		 * Private Construct of Path: Use static Path:Create
		 *
		 * @param string $path
		 */
		/**-------------------------------------------------------------------------*/
		private function __construct(string $path_name){

			// Set path property
			$this->pathName = $path_name;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Renders string from object
		 *
		 * @return string
		 */
		/**-------------------------------------------------------------------------*/
		public function __toString(): string{
			return $this->pathName;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Creates a Path Object from a path string
		 * @param  string $path [description]
		 * @return [type]       [description]
		 */
		/**-------------------------------------------------------------------------*/
		public static function create(string $path): self{
			// Normalize Path
			$normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

			// Determine if File or Directory
			if(is_file($path)){
				return new File($path);
			} else if(is_dir($path)){
				return new Directory($path);
			}

			// Path is neither file or directory
			// Return generic path
			return new self($normalizedPath);
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Joins Multiple Path objects together and forms a new Path Object
		 * @param  [type] $paths [description]
		 * @return [type]        [description]
		 */
		/**-------------------------------------------------------------------------*/
		public static function join(...$paths){
			// Convert Path instances to strings
			$parts = array_map('strval', $paths);

			// Join string paths by implosion
			$joinedPath = preg_replace('~[/\\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $parts));
			
			// Create new Path Instance
			// Return Instance
			return self::create($joinedPath);
		}

		/**
		 * Determines if file / dir / path exists
		 *
		 * @return boolean
		 */
		public function exists(): bool{
			return $this->isFile() ? $this->isFile() : $this->isDir();
		}

		/**
		 * Determines if object is a directory
		 *
		 * @return boolean
		 */
		public function isDir(){return is_dir($this->pathName);}

		/**
		 * Determines if object is a file
		 *
		 * @return boolean
		 */
		public function isFile(){return is_file($this->pathName);}

		/**
		 * Determines if object is populated
		 *
		 * @return boolean|null
		 */
		public function isPopulated(): ?bool{return $this->isDir() ? file_exists($this->pathName) : NULL;}

		/**
		 * Returns the parent path / dir of the current object
		 *
		 * @return self
		 */
		public function getParent(): self{return self::create(dirname($this->pathName));}

		/**
		 * Returns the base name of the current object
		 *
		 * @return string
		 */
		public function getBasename(): string{return basename($this->pathName);}

		/**
		 * Returns type defined in __construct
		 *
		 * @return string
		 */
		public function getType(): string{return $this->type;}
	}
?>