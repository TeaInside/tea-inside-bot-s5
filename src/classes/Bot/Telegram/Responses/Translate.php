<?php

namespace Bot\Telegram\Responses;

use Exception;
use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;
use GoogleTranslate\GoogleTranslate;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Translate extends ResponseFoundation
{
	/**
	 * @return void
	 */
	public function googleTranslate(): bool
	{
		defined("data") or define("data", STORAGE_PATH);

		if (preg_match("/^(?:\!|\/|\~|\.)?(?:t)(?:r|l)(?:[\s\n]+)([a-z]{2}|auto|zh-cn|zh-tw)(?:[\s\n]+)([a-z]{2}|zh-cn|zh-tw)(?:[\s\n]+)?(.*)?$/Usi", $this->data["text"], $m)) {
			if (isset($m[3])) {

				try {
					$st = new GoogleTranslate(trim($m[3]), $m[1], $m[2]);
					$st = trim($st->exec());
					$st = $st === "" ? "~" : $st;
				} catch (Exception $e) {
					$st = $e->getMessage()."\nSee available languages at: https://github.com/ammarfaizi2/GoogleTranslate/blob/master/README.md";
				}
				
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
				return true;
			} elseif (isset($this->data["reply_to"]["message_id"], $this->data["reply_to"]["text"])) {
			
				try {
					$st = new GoogleTranslate($this->data["reply_to"]["text"], $m[1], $m[2]);
					$st = trim($st->exec());
					$st = $st === "" ? "~" : $st;
				} catch (Exception $e) {
					$st = $e->getMessage()."\nSee available languages at: https://github.com/ammarfaizi2/GoogleTranslate/blob/master/README.md";
				}

				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->data["reply_to"]["message_id"]
					]
				);
				return true;
			}
		}

		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => Lang::getInstance()->get("Translate.usage"),
				"reply_to_message_id" => $this->data["msg_id"],
				"parse_mode" => "HTML"
			]
		);

		return true;
	}

	/**
	 * @return void
	 */
	public function googleTranslatetoRepliedMessage(): bool
	{
		if (isset($this->data["reply_to"]["message_id"], $this->data["reply_to"]["text"])) {
			if (preg_match("/^(?:\!|\/|\~|\.)?(?:tlr|trl)(?:[\s\n]+)?([a-z]{2}|auto|zh-cn|zh-tw)(?:[\s\n]+)([a-z]{2}|zh-cn|zh-tw)/Usi", $this->data["text"], $m)) {
				try {
					$st = new GoogleTranslate($this->data["reply_to"]["text"], $m[1], $m[2]);
					$st = trim($st->exec());
					$st = $st === "" ? "~" : $st;
				} catch (Exception $e) {
					$st = $e->getMessage()."\nSee available languages at: https://github.com/ammarfaizi2/GoogleTranslate/blob/master/README.md";
				}

				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->data["reply_to"]["message_id"]
					]
				);
				return true;
			} else {
				$st = new GoogleTranslate($this->data["reply_to"]["text"], "auto", "id");
				$st = trim($st->exec());
				$st = $st === "" ? "~" : $st;
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->data["reply_to"]["message_id"]
					]
				);
				return true;
			}
		}
		return false;
	}
}