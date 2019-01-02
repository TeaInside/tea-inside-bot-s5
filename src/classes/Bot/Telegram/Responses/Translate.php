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

		if (preg_match("/^(?:\!|\/|\~|\.)?(?:t)(?:r|l)(?:[\s\n]+)([a-z]{2}|auto|zh-cn|zh-tw)(?:[\s\n]+)([a-z]{2}|zh-cn|zh-tw)(?:[\s\n]+)?(.*)?$/Usi", $this->d["text"], $m)) {
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
						"chat_id" => $this->d["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->d["msg_id"]
					]
				);
				return true;
			} elseif (isset($this->d["reply_to_message"]["message_id"], $this->d["reply_to_message"]["text"])) {
			
				try {
					$st = new GoogleTranslate($this->d["reply_to_message"]["text"], $m[1], $m[2]);
					$st = trim($st->exec());
					$st = $st === "" ? "~" : $st;
				} catch (Exception $e) {
					$st = $e->getMessage()."\nSee available languages at: https://github.com/ammarfaizi2/GoogleTranslate/blob/master/README.md";
				}

				Exe::sendMessage(
					[
						"chat_id" => $this->d["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->d["reply_to_message"]["message_id"]
					]
				);
				return true;
			}
		}

		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => Lang::getInstance()->get("Translate", "usage"),
				"reply_to_message_id" => $this->d["msg_id"],
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
		if (isset($this->d["reply_to_message"]["message_id"], $this->d["reply_to_message"]["text"])) {
			if (preg_match("/^(?:\!|\/|\~|\.)?(?:tlr|trl)(?:[\s\n]+)?([a-z]{2}|auto|zh-cn|zh-tw)(?:[\s\n]+)([a-z]{2}|zh-cn|zh-tw)/Usi", $this->d["text"], $m)) {
				try {
					$st = new GoogleTranslate($this->d["reply_to_message"]["text"], $m[1], $m[2]);
					$st = trim($st->exec());
					$st = $st === "" ? "~" : $st;
				} catch (Exception $e) {
					$st = $e->getMessage()."\nSee available languages at: https://github.com/ammarfaizi2/GoogleTranslate/blob/master/README.md";
				}

				Exe::sendMessage(
					[
						"chat_id" => $this->d["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->d["reply_to_message"]["message_id"]
					]
				);
				return true;
			} else {
				$st = new GoogleTranslate($this->d["reply_to_message"]["text"], "auto", "id");
				$st = trim($st->exec());
				$st = $st === "" ? "~" : $st;
				Exe::sendMessage(
					[
						"chat_id" => $this->d["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->d["reply_to_message"]["message_id"]
					]
				);
				return true;
			}
		}
		return false;
	}
}
