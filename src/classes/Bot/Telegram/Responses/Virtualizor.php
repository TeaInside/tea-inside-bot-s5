<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Utils\GroupSetting;
use ContainerProvider\Virtualizor\Interpreter\Php;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Virtualizor extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function php(): bool
	{
		$st = new Php($this->d["text"], $this->d["user_id"]);
		$st = $st->run();
		if ($st === "") {
			$st = "~";
		}

		Exe::sendMessage(
			[
				"text" => $st,
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);

		return true;
	}
}
