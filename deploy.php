<?php

$ch = curl_init("https://webhook.teainside.org/circleci/tea-inside-bot-s5.php");
curl_setopt_array($ch, 
	[
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_POST => 1
	]
);
$out = curl_exec($ch);
curl_close($ch);
print $out;
