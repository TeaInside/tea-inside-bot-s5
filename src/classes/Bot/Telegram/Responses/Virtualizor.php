<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Utils\GroupSetting;

use ContainerProvider\Virtualizor\Compiler\C;
use ContainerProvider\Virtualizor\Compiler\Cpp;

use ContainerProvider\Virtualizor\Interpreter\Php;
use ContainerProvider\Virtualizor\Interpreter\Bash;
use ContainerProvider\Virtualizor\Interpreter\Python;

require_once BASEPATH."/config/isolate.php";

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Virtualizor extends ResponseFoundation
{
	/**
	 * @param string $code
	 * @return bool
	 */
	public function bash(string $code): bool
	{
		$st = new Bash(substr($this->d["text"], 3), $this->d["user_id"]);
		$st = $st->run();
		if ($st === "") {
			$st = "~";
		}

		Exe::sendMessage(
			[
				"text" => $st,
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);

		return true;
	}

	/**
	 * @param string $code
	 * @return bool
	 */
	public function php(string $code): bool
	{
		$st = new Php($code, $this->d["user_id"]);
		$st = $st->run();
		if ($st === "") {
			$st = "~";
		}

		Exe::sendMessage(
			[
				"text" => $st,
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);

		return true;
	}

	/**
	 * @param string $code
	 * @return bool
	 */
	public function python2(string $code): bool
	{
		$st = new Python($code, $this->d["user_id"]);
		$st->setVersion("2");
		$st = $st->run();
		if ($st === "") {
			$st = "~";
		}

		Exe::sendMessage(
			[
				"text" => $st,
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);

		return true;
	}

	/**
	 * @param string $code
	 * @return bool
	 */
	public function python3(string $code): bool
	{
		$st = new Python($code, $this->d["user_id"]);
		$st->setVersion("3");
		$st = $st->run();
		if ($st === "") {
			$st = "~";
		}

		Exe::sendMessage(
			[
				"text" => $st,
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);

		return true;
	}

	/**
	 * @param string $code
	 * @return bool
	 */
	public function c(string $code): bool
	{
		$error = false;

		$st = new C($code, $this->d["user_id"]);
		if ($st->compile()) {
			$st = $st->run();
			if ($st === "") {
				$st = "~";
			}

			if (strlen($st) >= 3000) {
				shell_exec("kill -9 ".getmypid());
			}
			
		} else {
			$error = true;
			$st = "<b>An error occured during compile time!</b>\n\n<pre>".htmlspecialchars(
					substr($st->compileOutput(), 0, 1000), ENT_QUOTES, "UTF-8"
			)."</pre>";
		}

		$d = [
			"text" => $st,
			"chat_id" => $this->d["chat_id"],
			"reply_to_message_id" => $this->d["msg_id"]
		];

		$error and $d["parse_mode"] = "HTML";

		Exe::sendMessage($d);

		return true;
	}

	/**
	 * @param string $code
	 * @return bool
	 */
	public function cpp(string $code): bool
	{
		$error = false;

		$st = new Cpp($code, $this->d["user_id"]);
		if ($st->compile()) {
			$st = $st->run();
			if ($st === "") {
				$st = "~";
			}

			if (strlen($st) >= 3000) {
				shell_exec("kill -9 ".getmypid());
			}
			
		} else {
			$error = true;
			$st = "<b>An error occured during compile time!</b>\n\n<pre>".htmlspecialchars(
					substr($st->compileOutput(), 0, 1000), ENT_QUOTES, "UTF-8"
			)."</pre>";
		}

		$d = [
			"text" => $st,
			"chat_id" => $this->d["chat_id"],
			"reply_to_message_id" => $this->d["msg_id"]
		];

		$error and $d["parse_mode"] = "HTML";

		Exe::sendMessage($d);

		return true;
	}
}
