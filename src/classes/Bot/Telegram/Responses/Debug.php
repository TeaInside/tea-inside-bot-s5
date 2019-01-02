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
class Debug extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function debug(): bool
	{

		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => "<pre>".htmlspecialchars(json_encode($this->d->in, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), ENT_QUOTES, "UTF-8")."</pre>",
				"reply_to_message_id" => $this->d["msg_id"],
				"parse_mode" => "HTML"
			]
		);

		return true;
	}
}
