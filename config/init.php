<?php

if (!defined("INIT_CONFIG")) {
	define("INIT_CONFIG", 1);
	
	define("BASEPATH", realpath(__DIR__."/.."));
	define("PUBLIC_PATH", BASEPATH."/public");
	define("STORAGE_PATH", BASEPATH."/storage");
	define("PUBLIC_BASEURL", "https://icetea.teainside.org");
}
