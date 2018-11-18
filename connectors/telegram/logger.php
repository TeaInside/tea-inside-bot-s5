<?php

require __DIR__."/../../bootstrap/init.php";
require BASEPATH."/config/telegram/config.php";

if (isset($argv[1])) {
	(new \Bot\Telegram\Logger(
		new \Bot\Telegram\Data($argv[1])
	))->run();
}
