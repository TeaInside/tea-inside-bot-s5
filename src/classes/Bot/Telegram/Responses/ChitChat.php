<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class ChitChat extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function init(): bool
	{
		$groupId = str_replace("-", "_", $this->d["chat_id"]);
		file_put_contents(STORAGE_PATH."/groupcache/chitchat/{$groupId}.state", "");
		Exe::sendMessage(
			[
				"text" => Lang::getInstance()->get("ChitChat", "init"),
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);
		return true;
	}

	/**
	 * @return bool
	 */
	public function stop(): bool
	{
		$groupId = str_replace("-", "_", $this->d["chat_id"]);
		file_exists(STORAGE_PATH."/groupcache/chitchat/{$groupId}.state") and 
		unlink(STORAGE_PATH."/groupcache/chitchat/{$groupId}.state");
		Exe::sendMessage(
			[
				"text" => Lang::getInstance()->get("ChitChat", "stop"),
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);
		return true;
	}
}
