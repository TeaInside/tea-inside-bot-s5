<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Utils\GroupSetting;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Ping extends ResponseFoundation
{
	/**
	 * @param string $host
	 * @return bool
	 */
	public function pingHost(string $host): bool
	{
		$host = escapeshellarg($host);
		$r = htmlspecialchars(shell_exec("nice -n30 ping -c 5 {$host} 2>&1"), ENT_QUOTES, "UTF-8");

		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => "<b>Ping result:</b>\n\n<pre>{$r}</pre>",
				"reply_to_message_id" => $this->d["msg_id"],
				"parse_mode" => "HTML"
			]
		);

		return true;
	}
}
