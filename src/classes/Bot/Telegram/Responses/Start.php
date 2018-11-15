<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Responses\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Start extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function start(): bool
	{
		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => "test"
			]
		);
	}
}
