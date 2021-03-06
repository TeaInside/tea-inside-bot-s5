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
class Start extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function start(): bool
	{
		$lang = Lang::getInstance();

		if ($this->d["chat_type"] === "private") {
			$r = $lang->get("Start", "private");
		} else {

			$me = GroupSetting::get($this->d["chat_id"]);
			if ((!isset($me["cmd_start"])) || ($me["cmd_start"] == 0)) {
				return false;
			}

			$r = $lang->get("Start", "group");
		}

		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => $r,
				"reply_to_message_id" => $this->d["msg_id"]				
			]
		);

		return true;
	}
}
