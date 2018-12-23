<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\Data;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Utils\GroupSetting;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Kulgram extends ResponseFoundation
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	protected $d;

	/**
	 * @param \Bot\Telegram\Data $d
	 *
	 * Constructor.
	 */
	public function __construct(Data $d)
	{
		$this->d = $d;

	}

	/**
	 * @param string $rcmd
	 * @return bool
	 */
	public function run(string $rcmd): bool
	{
		var_dump($rcmd);
		$opt = [];
		$cmd = "";
		if (preg_match("/^(?:[\s\n])(\S*)(?:[\s\n])/Usi", $rcmd, $m)) {
			$cmd = $m[1];
		}
		$cmd = trim($cmd);

		if (preg_match_all("/(?:[\s\n]--)([a-z0-9]*)(?:[\s\n]+)(([\"\'](.*[^\\\\])[\"\'])|([a-z0-9\_\-]+)(?:[\s\n]|$))/Usi", $rcmd, $m)) {
			foreach ($m[2] as $key => $v) {
				$c = strlen($v) - 1;
				$v = (
					($v[0] === "\"" && $v[$c] === "\"") ||
					($v[0] === "'"  && $v[$c] === "'" )
				) ? str_replace(["\\\"", "\\\\"], ["\"", "\\"], substr($v, 1, -1)) : $v;
				$v = trim($v);
				$opt[trim($m[1][$key])] = $v;
			}
			unset($m, $key, $v, $c);
		}

		switch ($cmd) {
			case "":
			case "help":
				return $this->intro();
				break;
			case "unknown":
				return $this->unknown();
				break;
			default:
				$this->unknown();
				break;
		}
	}

	/**
	 * @return bool
	 */
	private function intro(): bool
	{
		$text = htmlspecialchars(Lang::getInstance()->get("Kulgram", "intro"), ENT_QUOTES, "UTF-8");
		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => "<pre>{$text}</pre>",
				"parse_mode" => "HTML",
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);
		unset($text);
		return true;
	}

	/**
	 * @return bool
	 */
	private function unknown(): bool
	{
		$text = htmlspecialchars(Lang::getInstance()->get("Kulgram", "unknown"), ENT_QUOTES, "UTF-8");
		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => "<pre>{$text}</pre>",
				"parse_mode" => "HTML",
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);
		unset($text);
		return true;
	}

	/**
	 * @return bool
	 */
	public function init(): bool
	{

		return true;
	}
}
