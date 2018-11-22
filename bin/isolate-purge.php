<?php

require __DIR__."/../config/init.php";
require __DIR__."/../config/isolate.php";

if (defined("ISOLATE_BASE_DIR")) {
	print shell_exec("rm -rfv ".escapeshellarg(ISOLATE_BASE_DIR)." 2>&1");
	print shell_exec("rm -rfv /var/local/lib/isolate 2>&1");
	print shell_exec("mkdir -pv /var/local/lib/isolate");
	print shell_exec("mkdir -pv ".escapeshellarg(ISOLATE_BASE_DIR));
}
