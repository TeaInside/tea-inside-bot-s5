<?php

namespace Bot\Telegram\Logger;

use DB;
use PDO;
use Bot\Telegram\Exe;
use Bot\Telegram\Data;
use Bot\Telegram\Logger\Master\GroupMessage;
use Bot\Telegram\Logger\Master\PrivateMessage;
use Bot\Telegram\Contracts\MasterLoggerInterface;
use Bot\Telegram\Contracts\ContentLoggerInterface;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Logger
 */
class Image implements ContentLoggerInterface
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	private $d;

	/**
	 * @var \Bot\Telegram\Contracts\MasterLoggerInterface
	 */
	private $m;

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
	 * @param \Bot\Telegram\Contracts\MasterLoggerInterface $m
	 * @return void
	 */
	public function setMasterLogger(MasterLoggerInterface $m): void
	{
		$this->m = $m;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void
	{
		$this->pdo = DB::pdo();
		if ($this->m instanceof GroupMessage) {
			$this->groupMessageAction();
		} else if ($this->m instanceof PrivateMessage) {
			$this->privateMessageAction();
		}
	}

	/**
	 * @return void
	 */
	private function groupMessageAction(): void
	{

		$photo = $this->d["photo"][count($this->d["photo"]) - 1];
		

		$st = $this->pdo->prepare("SELECT `id` FROM `files` WHERE `telegram_file_id` = :telegram_file_id LIMIT 1;");
		$st->execute([":telegram_file_id" => $photo["file_id"]]);
		if ($r = $st->fetch(PDO::FETCH_NUM)) {
			$this->pdo
				->prepare("UPDATE `files` SET `hit_count`=`hit_count`+1 WHERE `id`=:id LIMIT 1;")
				->execute([":id" => $r[0]]);
		} else {
			$o = Exe::getFile(["file_id" => $photo["file_id"]]);
			$o = json_decode($o["out"], true);

			if (!isset($o["result"]["file_path"])) {
				return;
			}

			$ext = explode(".", $o["result"]["file_path"]);
			$ext = $ext[count($ext) - 1];
			$ch = curl_init("https://api.telegram.org/file/bot".BOT_TOKEN."/{$o["result"]["file_path"]}");
			curl_setopt_array($ch, 
				[
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_SSL_VERIFYHOST => false
				]
			);
			$bin = curl_exec($ch);
			curl_close($ch);
			$sha1 = sha1($bin); $md5 = md5($bin);
			file_put_contents(STORAGE_PATH."/files/telegram/{$md5}_{$sha1}.{$ext}", $bin);
			$bin = null;
			$this->pdo->prepare(
				"INSERT INTO `files` (`telegram_file_id`, `md5_sum`, `sha1_sum`, `absolute_hash`, `hit_count`, `file_type`, `extension`, `description`, `created_at`, `updated_at`) VALUES (:telegram_file_id, :md5_sum, :sha1_sum, :absolute_hash, :hit_count, :file_type, :extension, :description, :created_at, NULL);"
			)->execute(
				[
					":telegram_file_id" => $photo["file_id"],
					":md5_sum" => $md5,
					":sha1_sum" => $sha1,
					":absolute_hash" => "{$md5}_{$sha1}",
					":hit_count" => 1,
					":file_type" => "image",
					":extension" => $ext,
					":description" => NULL,
					":created_at" => date("Y-m-d H:i:s")
				]
			);
			unset($bin, $ch, $o, $photo, $ext, $sha1, $md5);
		}

		$this->pdo->prepare(
			"INSERT INTO `group_messages` (`group_id`, `user_id`, `tmsg_id`, `reply_to_tmsg_id`, `msg_type`, `text`, `text_entities`, `file`, `is_edited_message`, `tmsg_datetime`, `created_at`) VALUES (:group_id, :user_id, :tmsg_id, :reply_to_tmsg_id, :msg_type, :_text, :text_entities, :file, :is_edited_message, :tmsg_datetime, :created_at);"
		)->execute(
			[
				":group_id" => $this->d["chat_id"],
				":user_id" => $this->d["user_id"],
				":tmsg_id" => $this->d["msg_id"],
				":reply_to_tmsg_id" => (isset($this->d["reply_to_message"]["message_id"]) ?
					$this->d["reply_to_message"]["message_id"] : NULL),
				":msg_type" => $this->d["msg_type"],
				":_text" => $this->d["text"],
				":text_entities" => (isset($this->d["entities"]) ?
					json_encode($this->d["entities"]) : NULL
				),
				":file" => NULL,
				":is_edited_message" => 0,
				":tmsg_datetime" => date("Y-m-d H:i:s", $this->d["date"]),
				":created_at" => $this->m->now
			]
		);
	}

	/**
	 * @return void
	 */
	private function privateMessageAction(): void
	{
		$this->pdo->prepare(
			"INSERT INTO `private_messages` (`user_id`, `tmsg_id`, `reply_to_tmsg_id`, `msg_type`, `text`, `text_entities`, `file`, `is_edited_message`, `tmsg_datetime`, `created_at`) VALUES (:user_id, :tmsg_id, :reply_to_tmsg_id, :msg_type, :_text, :text_entities, :file, :is_edited_message, :tmsg_datetime, :created_at);"
		)->execute(
			[
				":user_id" => $this->d["user_id"],
				":tmsg_id" => $this->d["msg_id"],
				":reply_to_tmsg_id" => (isset($this->d["reply_to_message"]["message_id"]) ?
					$this->d["reply_to_message"]["message_id"] : NULL),
				":msg_type" => $this->d["msg_type"],
				":_text" => $this->d["text"],
				":text_entities" => (isset($this->d["entities"]) ?
					json_encode($this->d["entities"]) : NULL
				),
				":file" => NULL,
				":is_edited_message" => 0,
				":tmsg_datetime" => date("Y-m-d H:i:s", $this->d["date"]),
				":created_at" => $this->m->now
			]
		);
	}
}
