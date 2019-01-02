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
class NewChatMembers extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function newChatMembers(): bool
	{
		$g = GroupSetting::get($this->d["chat_id"]);

		if (isset($g["welcome_message"]) && $g["welcome_message"]) {
			foreach ($this->d["new_chat_members"] as $key => $u) {

				$rd = [
					"{first_name}" => htmlspecialchars($u["first_name"]),
					"{last_name}" => htmlspecialchars((isset($u["last_name"]) ? $u["last_name"] : "")),
					"{full_name}" => htmlspecialchars($u["first_name"].(isset($u["last_name"]) ? " {$u["last_name"]}" : "")),
					"{username}" => htmlspecialchars((isset($u["username"]) ? $u["username"] : "")),
					"{user_link}" => "<a href=\"tg://user?id={$u["id"]}\">".htmlspecialchars($u["first_name"].(isset($u["last_name"]) ? " {$u["last_name"]}" : ""))."</a>",
					"{chat_title}" => htmlspecialchars($this->d["chat_title"])
				];

				Exe::sendMessage(
					[
						"chat_id" => $this->d["chat_id"],
						"text" => Lang::bind($g["welcome_message"], $rd),
						"reply_to_message_id" => $this->d["msg_id"],
						"parse_mode" => "HTML"
					]
				);
			}
		}

		return true;
	}
}
