<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Me extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function me(): bool
	{

		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => "There is no data stored for this user.",
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);

		return true;
	}
}
