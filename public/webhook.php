<?php

require __DIR__."/../config/telegram/api_key.php";

if (isset($_GET["key"]) && $_GET["key"] === API_KEY) {
	require __DIR__."/../connectors/telegram/webhook.php";
	exit;
}
http_response_code(403);
