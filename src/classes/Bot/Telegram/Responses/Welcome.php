<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
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
		$o = Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"],
				"text" => $welcomeMessage,
				"parse_mode" => "HTML"
			]
		);

		$o = json_decode($o, true);

		if (isset($o["ok"]) && $o["ok"]) {

			DB::pdo()
				->prepare("UPDATE `group_settings` SET `welcome_message` = :welcome_message WHERE `group_id` = :group_id LIMIT 1")
				->execute(
					[
						":welcome_message" => $welcomeMessage,
						":group_id" => $this->d["chat_id"]
					]
				);

			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => Lang::getInstance()->get("Welcome", "ok"),
					"parse_mode" => "HTML"
				]
			);

		} else {
			if (isset($o["error_code"], $o["description"])) {
				Exe::sendMessage(
					[
						"chat_id" => $this->d["chat_id"],
						"reply_to_message_id" => $this->d["msg_id"],
						"text" => Lang::bind(
							Lang::getInstance()->get("Welcome", "error"),
							[
								":error" => "<code>".htmlspecialchars("{$o["error_code"]} {$o["description"]}", ENT_QUOTES, "UTF-8")."</code>"
							]
						),
						"parse_mode" => "HTML"
					]
				);
			}

			Exe::sendMessage(
					[
						"chat_id" => $this->d["chat_id"],
						"reply_to_message_id" => $this->d["msg_id"],
						"text" => Lang::getInstance()->get("Welcome", "unknown_error"),
						"parse_mode" => "HTML"
					]
				);
		}

		return true;
	}
}
