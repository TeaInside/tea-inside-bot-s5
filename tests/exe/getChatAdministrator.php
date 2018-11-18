<?php

require __DIR__."/../../bootstrap/init.php";
require BASEPATH."/config/telegram/config.php";

$st = \Bot\Telegram\Exe::getChatAdministrators(
	[
		"chat_id" => -1001134152012
	]
);

print json_encode(json_decode($st["out"]), 128);
print "\n";