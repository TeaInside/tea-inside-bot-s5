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
class Promote extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function promote(): bool
	{

		if (isset($this->d["reply_to_message"]["from"]["id"])) {
			$o = Exe::promoteChatMember(
				[
					"chat_id" => $this->d["chat_id"],
					"user_id" => $this->d["reply_to_message"]["from"]["id"],
					"can_change_info" => 1,
					"can_delete_messages" => 1,
					"can_invite_users" => 1,
					"can_restrict_members" => 1,
					"can_pin_messages" => 1,
					"can_promote_members" => 1
				]
			);

			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"text" => $o["out"],
					"reply_to_message_id" => $this->d["msg_id"]
				]
			);
		}

		return true;
	}
}
