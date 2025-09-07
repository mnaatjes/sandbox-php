<?php

	namespace MVCFrame\FileSystem;

	class PathRegistry {

		private ?Path $rootDir;

		private const REQUIRED_DIR=[
			"app" 		=> ["dev" => "/", "production" => "app/"],
			"config" 	=> ["dev" => "bootstrap/", "production" => "config/"],
			"database" 	=> ["dev" => "db/", "production" => "database/"],
			"public" 	=> ["dev" => "/../public/", "production" => "public/"],
			"storage" 	=> ["dev" => "storage/", "production" => "storage/"],
			"resource" 	=> ["dev" => "resources/", "production" => "resources/"],
			"routes"	=> ["dev" => "routes/", "production" => "routes/"],
		];

/**
 	- [ ] **basePath:** The root directory of your Laravel project.
	- [ ] **appPath:** The path to the app directory.
	- [ ] **configPath:** The path to the config directory.
	- [ ] **databasePath:** The path to the database directory.
	- [ ] **publicPath:** The path to the public directory.
	- [ ] **storagePath:** The path to the storage directory.
	- [ ] **resourcePath:** The path to the resources directory.
 */
	}
?>