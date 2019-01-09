<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class TextUtils extends ResponseFoundation
{
	/**
	 * @param string $text
	 * @return bool
	 */
	public function thtml(string $text): bool
	{
		$o = Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"],
				"text" => $text,
				"parse_mode" => "HTML"
			]
		);

		$o = json_decode($o["out"], true);

		if (!(isset($o["ok"]) && $o["ok"])) {
			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => Lang::bind(
						Lang::getInstance()->get("TextUtils", "error"),
						[
							":error" => "<code>".htmlspecialchars("{$o["error_code"]} {$o["description"]}", ENT_QUOTES, "UTF-8")."</code>"
						]
					),
					"parse_mode" => "HTML"
				]
			);
		}
	}
}
