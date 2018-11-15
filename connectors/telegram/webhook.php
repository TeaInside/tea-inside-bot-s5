<?php

$input = escapeshellarg(file_get_contents("php://input"));

shell_exec(
	"nohup php ".__DIR__."/webhook_worker.php {$input} >> ".__DIR__."/../logs/telegram/webhook_worker.log 2>&1 &"
);

shell_exec(
	"nohup php ".__DIR__."/logger.php {$input} >> ".__DIR__."/../logs/telegram/logger.log 2>&1 &"
);
