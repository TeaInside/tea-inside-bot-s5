<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Welcome extends ResponseFoundation
{
	/**
	 * @param string $welcomeMessage
	 * @return bool
	 */
	public function setWelcome(string $welcomeMessage): bool
	{
		$out = Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"],
				"text" => $welcomeMessage,
				"parse_mode" => "HTML"
			]
		);


		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"],
				"text" => $out["out"],
				"parse_mode" => "HTML"
			]
		);

		return true;
	}
}
