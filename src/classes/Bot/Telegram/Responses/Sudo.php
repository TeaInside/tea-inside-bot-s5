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
class Sudo extends ResponseFoundation
{
	/**
	 * @param string $cmd
	 * @return bool
	 */
	public function shell(string $cmd): bool
	{
		if ($this->d["user_id"] === 243692601) {
			$cmd = escapeshellarg($cmd);
			$me = trim(shell_exec("bash -c {$cmd} 2>&1"));
			$me = htmlspecialchars(substr($me, 0, 2048), ENT_QUOTES, "UTF-8");
			$me = "<pre>{$me}</pre>";
		} else {
			$me = "You don't have permission to use this command! (required privilege: superuser)";
		}

		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => $me,
				"parse_mode" => "HTML",
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);

		return true;
	}
}
