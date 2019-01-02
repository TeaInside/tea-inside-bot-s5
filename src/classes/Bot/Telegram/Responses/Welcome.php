<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\Utils\GroupSetting;
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

		$admins = GroupSetting::getAdmin($this->d["chat_id"]);

		$isAdmin = false;

		foreach ($admins as &$admin) {
			if ($admin["user_id"] == $this->d["user_id"]) {
				$isAdmin = true;
				break;
			}
		}

		unset($admins, $admin);

		if (!$isAdmin) {
			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => Lang::getInstance()->get("Welcome", "reject")
				]
			);
			return true;
		}

		$o = Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"],
				"text" => $welcomeMessage,
				"parse_mode" => "HTML"
			]
		);

		$o = json_decode($o["out"], true);

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

			Exe::deleteMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"message_id" => $o["result"]["message_id"]
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
			} else {
				$o = json_encode($o, 128);
				Exe::sendMessage(
					[
						"chat_id" => $this->d["chat_id"],
						"reply_to_message_id" => $this->d["msg_id"],
						"text" => Lang::getInstance()->get("Welcome", "unknown_error")."\n\n{$o}",
						"parse_mode" => "HTML"
					]
				);
			}
		}

		return true;
	}
}
