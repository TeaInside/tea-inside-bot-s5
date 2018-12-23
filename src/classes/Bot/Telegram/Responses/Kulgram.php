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
	 * @var string
	 */
	private $stateDir;

	/**
	 * @var string
	 */
	private $stateFile;

	/**
	 * @var array
	 */
	private $state = [];

	/**
	 * @param \Bot\Telegram\Data $d
	 *
	 * Constructor.
	 */
	public function __construct(Data $d)
	{
		$this->d = $d;
		$this->stateDir  = STORAGE_PATH."/kulgram/".str_replace("-", "_", $this->d["chat_id"]);
		$this->stateFile = "{$this->stateDir}/mem.json";

		is_dir($this->stateDir) or mkdir($this->stateDir);
		is_dir("{$this->stateDir}/files") or mkdir("{$this->stateDir}/files");
		is_dir("{$this->stateDir}/archives") or mkdir("{$this->stateDir}/archives");

		if (!file_exists($this->stateFile)) {
			$this->state = [
				"status" => "off",
				"auto_inc" => 0,
				"sessions" => []
			];
		}
	}

	/**
	 * @param string $rcmd
	 * @return bool
	 */
	public function run(string $rcmd): bool
	{
		$opt = [];
		$cmd = explode(" ", trim($rcmd), 2);
		$cmd = trim($cmd[0]);

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
			case "init":
				return $this->init($opt);
				break;
			default:
				$this->unknown($cmd);
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
	 * @param array &$opt
	 * @return bool
	 */
	private function init(array &$opt): bool
	{
		if ($this->state["status"] === "off") {
			

			var_dump($opt);die;

			return true;
		}
	}

	/**
	 * @param string $cmd
	 * @return bool
	 */
	private function unknown(string $cmd): bool
	{
		$text = htmlspecialchars(
			Lang::bind(
				Lang::getInstance()->get("Kulgram", "unknown"),
				[":cmd" => $cmd]
			),
			ENT_QUOTES,
			"UTF-8"
		);
		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => "<pre>{$text}</pre>",
				"parse_mode" => "HTML",
				"reply_to_message_id" => $this->d["msg_id"]
			]
		);
		unset($text, $cmd);
		return true;
	}
}
