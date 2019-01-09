<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
trait ResponseRoutes
{
	/**
	 * @return void
	 */
	public function text(): void
	{
		if ("/start" === $this->d["text"]) {
			if ($this->exec("Start", "start")) {
				return;
			}
		}

		if ("/help" === $this->d["text"]) {
			if ($this->exec("Help", "help")) {
				return;
			}
		}

		if (preg_match("/^(?:[\.\/\!\~\,]?ping[\s\n\t]+)(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Ping", "pingHost", [$m[1]])) {
				return;
			}
		}

		if (preg_match("/^(?:[\.\/\!\~\,]kulgram)(.*)$/", $this->d["text"], $m)) {
			if ($this->exec("Kulgram", "run", [$m[1]])) {
				return;
			}
		}

		if (preg_match("/^(\!|\/|\~|\.)?t(l|r)($|[\s\n])/Usi", $this->d["text"])) {
			if ($this->exec("Translate", "googleTranslate")) {
				return;
			}
		}

		if (preg_match("/^(\!|\/|\~|\.)?(tlr|trl)($|[\s\n])/Usi", $this->d["text"])) {
			if ($this->exec("Translate", "googleTranslatetoRepliedMessage")) {
				return;
			}
		}

		if (preg_match("/^(?:\!|\/|\~|\.)?(?:fb[\s\n]+)([^\s\n]+)$/Usi", $this->d["text"], $m)) {

			if ($this->exec("Fb", "fb", [trim($m[1])])) {
				return;
			}
		}

		if (preg_match("/^(?:\!|\/|\~|\.)(?:welcome[\s\n]+)(.+)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Welcome", "setWelcome", [trim($m[1])])) {
				return;
			}
		}

		if (preg_match("/^(?:\!|\/|\~|\.)(?:del(ete)?_welcome)/Usi", $this->d["text"])) {
			if ($this->exec("Welcome", "deleteWelcome")) {
				return;
			}
		}

		if (preg_match("/^(?:\!|\/|\~|\.)?promote/Usi", $this->d["text"])) {
			if ($this->exec("Promote", "promote")) {
				return;
			}
		}

		if (preg_match("/^(?:\!|\/|\~|\.)?debug/Usi", $this->d["text"])) {
			if ($this->exec("Debug", "debug")) {
				return;
			}
		}

		if (preg_match("/^[\.\/\!\~\,]?whatanime/Usi", $this->d["text"])) {
			if ($this->exec("Whatanime", "whatanime")) {
				return;
			}
		}

		if (preg_match("/^(?:\!|\/|\~|\.)(?:t?html[\s\n]+)(.+)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("TextUtils", "thtml", [trim($m[1])])) {
				return;
			}
		}

		if (preg_match("/^(?:\!|\/|\~|\.)(report(?:[\s\n]+)?)(.+)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Admin", "report", [trim($m[1])])) {
				return;
			}
		}


		/**
		 * Virtualizor
		 */

		if (preg_match("/^<\?php[\s\n\t].*$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "php", [$m[0]])) {
				return;
			}
		}

		if (preg_match("/^(?:<\?java[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "java", [$m[1]])) {
				return;
			}
		}

		if (preg_match("/^(?:<\?n?asm[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "asm", [$m[1]])) {
				return;
			}
		}

		if (preg_match("/^(?:<\?py(?:thon)?2[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "python2", [$m[1]])) {
				return;
			}
		}

		if (preg_match("/^(?:<\?py(?:thon)?3?[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "python3", [$m[1]])) {
				return;
			}
		}

		if (preg_match("/^(?:(?:\/|\.|\!|\~|\,)?sh[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "bash", [$m[1]])) {
				return;
			}
		}
		
		if (preg_match("/^(?:<\?(?:c|gcc)[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "c", [$m[1]])) {
				return;
			}
		}
		if (preg_match("/^(?:<\?(?:cpp|g\+\+|c\+\+|gpp)[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {			
			if ($this->exec("Virtualizor", "cpp", [$m[1]])) {
				return;
			}
		}

		if (preg_match("/^[\.\/\!\~\,]?me/Usi", $this->d["text"])) {
			if ($this->exec("Me", "me")) {
				return;
			}
		}

		$groupId = str_replace("-", "_", $this->d["chat_id"]);
		if (!file_exists(STORAGE_PATH."/groupcache/chitchat/{$groupId}.state")) {
			$st = trim(tea_ai_chat($txt, $name, $name));
			if ($st !== "") {
				Exe::sendMessage(
					[
						"text" => $st,
						"chat_id" => $this->d["chat_id"],
						"reply_to_message_id" => $this->d["msg_id"]
					]
				);
			}
		}
	}

	/**
	 * @return void
	 */
	public function newChatMembers(): void
	{
		$this->exec("NewChatMembers", "newChatMembers");
	}

	/**
	 * @return bool
	 */
	public function any(): bool
	{
		return false;
	}
}
