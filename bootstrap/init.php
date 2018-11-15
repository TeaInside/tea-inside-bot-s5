<?php

if (!defined("_ICETEA_INIT")) {

	define("_ICETEA_INIT", 1);
	
	/**
	 * Load init config.
	 */
	require __DIR__."/../config/init.php";
	
	/**
	 * @param string $class
	 * @return void
	 */
	function iceteaInternalClassAutoloader(string $class): void
	{
		$class = str_replace("\\", "/", $class);
		if (file_exists($class = BASEPATH."/src/classes/{$class}.php")) {
			require $class;
		}
	}

	/**
	 * Register autoload.
	 */
	spl_autoload_register("iceteaInternalClassAutoloader");

	/**
	 * Load composer autoload.
	 */
	require_once BASEPATH."/vendor/autoload.php";

	/**
	 * Load helper functions.
	 */
	require BASEPATH."/src/helpers.php";
}
