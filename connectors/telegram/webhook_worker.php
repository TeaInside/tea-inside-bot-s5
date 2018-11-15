<?php

require __DIR__."/../../bootstrap/init.php";
require BASEPATH."/config/telegram/config.php";

if (isset($argv[1])) {
	$st = new Bot\Telegram\Bot($argv[1]);
	$st->run();
} else {
	print "You need to provide \$argv[1] !\n";
	exit(1);
}
