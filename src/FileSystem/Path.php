<?php

	namespace MVCFrame\FileSystem;

	class Path {

		private readonly ?string $path;
		private ?string $type;

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		private function __construct(string $path){

			// Set path property
			$this->path = $path;

			// Set type
			$this->type = $this->isFile() ? "file" : "directory";
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public function __toString(): string{
			return $this->path;
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

			// Set path
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

		public function exists(): bool{
			return $this->isFile() ? $this->isFile() : $this->isDir();
		}

		public function isDir(){
			return is_dir($this->path);
		}

		public function isFile(){
			return is_file($this->path);
		}

		public function isPopulated(): ?bool{
			return $this->isDir() ? file_exists($this->path) : NULL;
		}

		public function getParent(): self{
			return self::create(dirname($this->path));
		}

		public function getBasename(): string{
			return basename($this->path);
		}

		public function getType(): string{
			return $this->type;
		}
	}
?>