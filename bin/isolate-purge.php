<?php

require __DIR__."/../config/init.php";
require __DIR__."/../config/isolate.php";

if (defined("ISOLATE_BASE_DIR")) {

	function rmRecursive($dir)
	{
		if (is_dir($dir)) {			
			$scan = scandir($dir);
			unset($scan[0], $scan[1]);
			foreach ($scan as $v) {
				if (is_dir("{$dir}/{$v}")) {
					rmRecursive("{$dir}/{$v}");
					is_dir("{$dir}/{$v}") and
					print "Removing {$dir}/{$v}...\n" and
					rmdir("{$dir}/{$v}");
				} else {
					file_exists("{$dir}/{$v}") and 
					print "Removing {$dir}/{$v}...\n" and
					unlink("{$dir}/{$v}");
				}
			}
			is_dir($dir) and
			print "Removing {$dir}...\n" and
			rmdir($dir);
		} else {
			if (!file_exists($dir)) {
				print "No action!\n";
			} else {
				unlink($dir);
			}
		}
	}

	rmRecursive(ISOLATE_BASE_DIR);
	if (is_dir("/var/local/lib/isolate")) {
		rmRecursive("/var/local/lib/isolate");
		mkdir("/var/local/lib/isolate");
	}
}
