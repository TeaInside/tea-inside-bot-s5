<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Mpdf\Mpdf;
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
				"session" => []
			];
		} else {
			$this->state = json_decode(file_get_contents($this->stateFile), true);
			if (!isset($this->state["status"], $this->state["auto_inc"])) {
				$this->state = [
					"status" => "off",
					"auto_inc" => 0,
					"session" => []
				];
			}
		}
	}

	/**
	 * @param string $rcmd
	 * @return bool
	 */
	public function run(string $rcmd): bool
	{

		if ($this->d["chat_type"] === "private") {
			Exe::sendMessage(
				[
					"text" => Lang::getInstance()->get("Kulgram", "private_response"),
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"]
				]
			);
			return true;
		}

		$opt = [];
		$rcmd = str_replace("\xe2\x80\x94", "--", $rcmd);
		$cmd = explode(" ", trim($rcmd), 2);
		$cmd = trim($cmd[0]);
		if (preg_match_all("/\-{2}([^\\s\\n]+)(?:\\s+|\\n+|\=)((?:\\\"|\\')(.*[^\\\\])(?:\\\"|\\')|[^\\s\\n]+)(?:[\\s\\n]|$)/Usi", $rcmd, $m)) {
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
			case "start":
				return $this->start();
				break;
			case "cancel":
				return $this->cancel();
				break;
			case "stop":
				return $this->stop();
				break;
			default:
				return $this->unknown($cmd);
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
	private function cancel(): bool
	{
		$hit = function (string $text): bool {
			$o = Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => "<pre>".htmlspecialchars($text, ENT_QUOTES, "UTF-8")."</pre>",
					"parse_mode" => "HTML"
				]
			);
			return true;
		};

		if ($this->state["status"] === "idle") {

			$this->state["status"] = "off";
			$this->state["session"] = [];
			$this->writeState();
			
			$hit(Lang::getInstance()->get("Kulgram", "cancel.ok"));
			unset($hit);

			return true;
		} else {
			if ($this->state["status"] === "off") {
				return $hit(Lang::getInstance()->get("Kulgram", "cancel.on.off"));
			} else if ($this->state["status"] === "running") {
				return $hit(Lang::getInstance()->get("Kulgram", "cancel.on.running"));
			} else {
				return $hit(Lang::bind(
					Lang::getInstance()->get("Kulgram", "unknown_error"),
					[
						":fl" => (__FILE__.":".__LINE__)
					]
				));
			}
		}
	}

	/**
	 * @return bool
	 */
	private function start(): bool
	{
		$hit = function (string $text): bool {
			$o = Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => "<pre>".htmlspecialchars($text, ENT_QUOTES, "UTF-8")."</pre>",
					"parse_mode" => "HTML"
				]
			);
			return true;
		};

		if ($this->state["status"] === "idle") {

			$this->state["status"] = "running";
			$this->state["session"]["started_at"] = time();
			$this->writeState();

			$text = 
				"<b>Kulgram {$this->state["auto_inc"]}</b>\n\n<b>Title : </b>".
				htmlspecialchars($this->state["session"]["title"]).
				"\n<b>Author : </b>".
				htmlspecialchars($this->state["session"]["author"]).
				"\n<b>Init Date : </b> ".date("c");
			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"text" => $text,
					"parse_mode" => "HTML"
				]
			);
			
			$hit(Lang::getInstance()->get("Kulgram", "start.ok"));
			unset($hit);

			return true;
		} else {
			if ($this->state["status"] === "off") {
				return $hit(Lang::getInstance()->get("Kulgram", "start.on.off"));
			} else if ($this->state["status"] === "running") {
				return $hit(Lang::getInstance()->get("Kulgram", "start.on.running"));
			} else {
				return $hit(Lang::bind(
					Lang::getInstance()->get("Kulgram", "unknown_error"),
					[
						":fl" => (__FILE__.":".__LINE__)
					]
				));
			}
		}
	}

	/**
	 * @return bool
	 */
	private function stop(): bool
	{
		$hit = function (string $text): bool {
			$o = Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => "<pre>".htmlspecialchars($text, ENT_QUOTES, "UTF-8")."</pre>",
					"parse_mode" => "HTML"
				]
			);
			return true;
		};

		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => htmlspecialchars(Lang::getInstance()->get("Kulgram", "stop.p1"), ENT_QUOTES, "UTF-8"),
				"reply_to_message_id" => $this->d["msg_id"],
				"parse_mode" => "HTML"
			]
		);
		if ($this->state["status"] === "running") {

			$this->fp = fopen("/tmp/__telegram_lock_".str_replace("-", "_", $this->d["chat_id"]).".lock", "r");
			if (is_resource($this->fp)) {
				while ((!flock($this->fp, LOCK_EX | LOCK_NB, $eWouldBlock)) || $eWouldBlock) {
					usleep(100000);
				}
			}

			sleep(5);

			$pdo = DB::pdo();
			$st = $pdo->prepare(
"SELECT
 `b`.`id` AS `user_id`,`b`.`first_name`,`b`.`last_name`,`b`.`username`,
 `a`.`id`,`a`.`tmsg_id`,`a`.`reply_to_tmsg_id`,`a`.`msg_type`,
 `a`.`text`,`a`.`text_entities`,`is_edited_message`,`a`.`created_at`,
 `c`.`telegram_file_id`,`c`.`md5_sum`,`c`.`sha1_sum`,`c`.`file_type`,
 `c`.`extension`,`c`.`absolute_hash`
FROM `group_messages` AS `a` 
INNER JOIN `users` AS `b` ON `b`.`id` = `a`.`user_id`
LEFT JOIN `files` AS `c` ON `a`.`file` = `c`.`id`
WHERE `a`.`group_id` = :group_id AND `a`.`created_at` >= :_start AND `a`.`created_at` <= :_end
ORDER BY `a`.`tmsg_id` ASC;"
			);
			$st->execute(
				[
					":_start" => date("Y-m-d H:i:s", $this->state["session"]["started_at"]),
					":_end" => date("Y-m-d H:i:s"),
					":group_id" => $this->d["chat_id"]
				]
			);

			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"text" => htmlspecialchars(Lang::getInstance()->get("Kulgram", "stop.p2"), ENT_QUOTES, "UTF-8"),
					"reply_to_message_id" => $this->d["msg_id"],
					"parse_mode" => "HTML"
				]
			);

			is_dir(STORAGE_PATH."/tmp_dir") or mkdir(STORAGE_PATH."/tmp_dir");
			if (!file_exists(STORAGE_PATH."/tmp_dir/.gitignore")) {
				file_put_contents(STORAGE_PATH."/tmp_dir/.gitignore", "*");
			}

			$mpdf = new Mpdf(
				[
					"tempDir" => STORAGE_PATH."/tmp_dir",
					"mode" => "utf-8"
				]
			);


			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"text" => htmlspecialchars(Lang::getInstance()->get("Kulgram", "stop.p3"), ENT_QUOTES, "UTF-8"),
					"reply_to_message_id" => $this->d["msg_id"],
					"parse_mode" => "HTML"
				]
			);

			$handle = fopen("{$this->stateDir}/archives/{$this->state["auto_inc"]}.json", "w");
			flock($handle, LOCK_EX);

			$mpdf->WriteHTML(
				"<h1>".htmlspecialchars(
					"{$this->state["session"]["title"]} by {$this->state["session"]["author"]}"
				)."</h1><br>"
			);
			fwrite($handle, 
				"{\"group_id\":".json_encode($this->d["chat_id"], 64).
				",\"created_at\":".((int)$this->state["session"]["started_at"]).
				",\"author\":".json_encode($this->state["session"]["author"], 64).
				",\"title\":".json_encode($this->state["session"]["title"], 64).",\"data\":["
			);
			while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
				
				$fd = [
					"date" => $r["created_at"],
					"user_id" => $r["user_id"],
					"msg_type" => $r["msg_type"],
					"first_name" => $r["first_name"],
					"last_name" => $r["last_name"],
					"username" => $r["username"],
					"text" => $r["text"],
					"files" => []
				];

				$name = htmlspecialchars(
					$r["first_name"].(isset($r["last_name"]) ? " ".$r["last_name"] : "").
					(isset($r["username"]) ? " (@{$r["username"]})" : ""), ENT_QUOTES, "UTF-8"
				);
				$text = str_replace("\n", "<br>", htmlspecialchars($r["text"]));
				if ($r["msg_type"] == "photo") {
					$mpdf->WriteHTML("<b>{$name}</b><br>{$text}<br>");
					$mpdf->WriteHTML(
						"<img src=\"data:image/jpg;base64,".(
							$b64 = base64_encode(
								file_get_contents(
									STORAGE_PATH."/files/telegram/{$r["absolute_hash"]}.{$r["extension"]}"
								)
							)
						)."\">"
					);
					$mpdf->WriteHTML("<br><br>");

					$fd["files"][] = [
						"md5" => $r["md5_sum"],
						"sha1" => $r["sha1_sum"],
						"ext" => $r["extension"],
						"data" => $b64
					];

				} elseif ($r["msg_type"] === "text") {
					$mpdf->WriteHTML("<b>{$name}</b><br>{$text}<br>");
				}


				fwrite($handle, json_encode($fd, 64).",");
			}
			fwrite($handle, ",{}]}");
			fclose($handle);
			ob_start();
			$mpdf->Output();
			$content = ob_get_clean();
			file_put_contents(
				"{$this->stateDir}/archives/{$this->state["auto_inc"]}.pdf",
				$content
			);
			$groupIdd = str_replace("-", "_", $this->d["chat_id"]);
			$num = $this->state["auto_inc"]++;
			$this->state["status"] = "off";
			$this->state["session"] = [];
			unset($content, $mpdf);
			$this->writeState();
			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"text" => htmlspecialchars(Lang::bind(
						Lang::getInstance()->get("Kulgram", "stop.ok"),
						[
							":pdf_link" => PUBLIC_URL."/kulgram/{$groupIdd}/archives/{$num}.pdf",
							":json_link" => PUBLIC_URL."/kulgram/{$groupIdd}/archives/{$num}.json"
						]
					), ENT_QUOTES, "UTF-8"),
					"reply_to_message_id" => $this->d["msg_id"],
					"parse_mode" => "HTML"
				]
			);
			fclose($this->fp);
			$this->fp = null;
			return true;
		} else {
			if ($this->state["status"] === "off") {
				return $hit(Lang::getInstance()->get("Kulgram", "stop.on.off"));
			} else if ($this->state["status"] === "idle") {
				return $hit(Lang::getInstance()->get("Kulgram", "stop.on.idle"));
			} else {
				return $hit(Lang::bind(
					Lang::getInstance()->get("Kulgram", "unknown_error"),
					[
						":fl" => (__FILE__.":".__LINE__)
					]
				));
			}
		}
	}

	/**
	 * @param array &$opt
	 * @return bool
	 */
	private function init(array &$opt): bool
	{
		$hit = function (string $text): bool {
			$o = Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => "<pre>".htmlspecialchars($text, ENT_QUOTES, "UTF-8")."</pre>",
					"parse_mode" => "HTML"
				]
			);
			return true;
		};

		if ($this->state["status"] === "off") {
			
			if (count($opt) === 0) {
				return $hit(Lang::getInstance()->get("Kulgram", "init.usage"));
			}

			if (!isset($opt["title"])) {
				return $hit(Lang::getInstance()->get("Kulgram", "init.error_no_title"));
			}

			if (!isset($opt["author"])) {
				return $hit(Lang::getInstance()->get("Kulgram", "init.error_no_author"));
			}

			$this->state["status"] = "idle";
			$this->state["session"] = [
				"title" => $opt["title"],
				"author" => $opt["author"],
				"initialized_at" => time(),
				"started_at" => null
			];
			$this->writeState();

			$hit(Lang::getInstance()->get("Kulgram", "init.ok"));
			unset($hit);

			$text = 
				"<b>Kulgram {$this->state["auto_inc"]}</b>\n\n<b>Title : </b>".
				htmlspecialchars($opt["title"]).
				"\n<b>Author : </b>".
				htmlspecialchars($opt["author"]).
				"\n<b>Init Date : </b> ".date("c");
			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"text" => $text,
					"parse_mode" => "HTML"
				]
			);

			return true;
		} else {
			if ($this->state["status"] === "running") {
				return $hit(Lang::getInstance()->get("Kulgram", "init.on.running"));
			} else if ($this->state["status"] === "idle") {
				return $hit(Lang::getInstance()->get("Kulgram", "init.on.idle"));
			} else {
				return $hit(Lang::bind(
					Lang::getInstance()->get("Kulgram", "unknown_error"),
					[
						":fl" => (__FILE__.":".__LINE__)
					]
				));
			}
		}
	}

	/**
	 * @return void
	 */
	private function writeState(): void
	{
		file_put_contents($this->stateFile, json_encode($this->state, JSON_UNESCAPED_SLASHES));
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
