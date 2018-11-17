<?php

$input = escapeshellarg(file_get_contents("php://input"));

$dir = __DIR__;

shell_exec(
	"nohup php {$dir}/webhook_worker.php {$input} >> {$dir}/../../logs/telegram/webhook_worker.log 2>&1 &"
);

shell_exec(
	"nohup php {$dir}/logger.php {$input} >> {$dir}/../../logs/telegram/logger.log 2>&1 &"
);
