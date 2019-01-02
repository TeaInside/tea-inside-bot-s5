<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Lang;;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Utils\GroupSetting;
use Bot\Telegram\Logger\Master\GroupMessage;

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
		$isAdmin = true;
		if (!in_array($this->d["user_id"], SUDOERS)) {
			$admins = GroupSetting::getAdmin($this->d["chat_id"]);
			$isAdmin = false;
			foreach ($admins as &$admin) {
				if ($admin["user_id"] == $this->d["user_id"]) {
					$isAdmin = (bool)$admin["can_promote_members"];
					break;
				}
			}
			unset($admins, $admin);
		}
		
		if (!$isAdmin) {
			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => Lang::getInstance()->get("Welcome", "reject")
				]
			);
		}

		if ($isAdmin && isset($this->d["reply_to_message"]["from"]["id"])) {
			$o = json_decode(Exe::promoteChatMember(
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
			)["out"], true);

			if ($o["ok"] && $o["result"]) {
				Exe::sendMessage(
					[
						"chat_id" => $this->d["chat_id"],
						"text" => Lang::bind(
							Lang::getInstance()->get("Promote", "promote.ok"),
							[
								":ulink" => 
									"<a href=\"tg://user?id={$this->d["reply_to_message"]["from"]["id"]}\">".
									htmlspecialchars(
										$this->d["reply_to_message"]["from"]["first_name"].
										(isset($this->d["reply_to_message"]["from"]["last_name"])?" {$this->d["reply_to_message"]["from"]["id"]}":""),
										ENT_QUOTES,
										"UTF-8"
									)."</a>"
							]
						),
						"reply_to_message_id" => $this->d["msg_id"],
						"parse_mode" => "HTML"
					]
				);	
			} else {

				if (isset($o["error_code"], $o["description"])) {
					Exe::sendMessage(
						[
							"chat_id" => $this->d["chat_id"],
							"text" => Lang::bind(
								Lang::getInstance()->get("Promote", "promote.error"),
								[
									":error_message" => "<pre>".
									htmlspecialchars("{$o["error_code"]} {$o["description"]}", ENT_QUOTES, "UTF-8")
									."</pre>"
								]
							),
							"reply_to_message_id" => $this->d["msg_id"],
							"parse_mode" => "HTML"
						]
					);	
				} else {
					Exe::sendMessage(
						[
							"chat_id" => $this->d["chat_id"],
							"text" => Lang::bind(
								Lang::getInstance()->get("Promote", "promote.error"),
								[
									":error_message" => "<b>Unknown Error</b>"
								]
							),
							"reply_to_message_id" => $this->d["msg_id"],
							"parse_mode" => "HTML"
						]
					);	
				}
			}
		}

		$st = new GroupMessage($this->d);
		$st->adminFetcher(true);
		unset($st);

		return true;
	}
}
