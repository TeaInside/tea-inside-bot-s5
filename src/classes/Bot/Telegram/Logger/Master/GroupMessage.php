<?php

namespace Bot\Telegram\Logger\Master;

use DB;
use PDO;
use Bot\Telegram\Data;
use Bot\Telegram\Contracts\MasterLoggerInterface;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Logger\Master
 */
class GroupMessage implements MasterLoggerInterface
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	private $d;

	/**
	 * @var \PDO
	 */
	private $pdo;

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
	 * @return void
	 */
	public function __invoke(): void
	{
		$this->pdo = DB::pdo();

		$st = $this->pdo->prepare(
			"SELECT `name`,`username`,`link`,`photo`,`msg_count` FROM `groups` WHERE `id` = :group_id LIMIT 1;"
		);
		$st->execute([":group_id" => $this->d["chat_id"]]);
		if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
			$this->update($st);
		} else {
			$this->create();
		}

		$this->userLogger();
	}

	/**
	 * @return void
	 */
	private function create(): void
	{
		if ($this->d["event_type"] === "general_message") {
			$msgCount = 1;
		}

		$now = date("Y-m-d H:i:s");

		$st = $this->pdo->prepare(
			"INSERT INTO `groups` (`id`, `name`, `username`, `link`, `photo`, `msg_count`, `created_at`, `updated_at`, `last_seen`) VALUES (:group_id, :name, :username, :link, :photo, :msg_count, :created_at, NULL, :last_seen);"
		)->execute(
			$data = [
				":group_id" => $this->d["chat_id"],
				":name" => $this->d["chat_title"],
				":username" => $this->d["chat_username"],
				":link" => NULL,
				":photo" => NULL,
				":msg_count" => $msgCount,
				":created_at" => $now,
				":last_seen" => $now
			]
		);

		unset($data[":msg_count"], $data[":last_seen"]);

		$this->pdo->prepare(
			"INSERT INTO `groups_history` (`group_id`, `name`, `username`, `link`, `photo`, `created_at`) VALUES (:group_id, :name, :username, :link, :photo, :created_at);"
		)->execute($data);

		unset($data, $now);
	}

	/**
	 * @return void
	 */
	private function userLogger(): void
	{
		$st = $this->pdo->prepare(
			"SELECT `username`,`first_name`,`last_name`,`photo` FROM `users` WHERE `id` = :user_id LIMIT 1;"
		);
		$st->execute([":user_id" => $this->d["user_id"]]);
		if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
			$this->updateUser($st);
		} else {
			$this->createUser();
		}
	}

	/**
	 * @return void
	 */
	private function createUser()
	{
		if ($this->d["event_type"] === "general_message") {
			$msgCount = 1;
		}
		
		$now = date("Y-m-d H:i:s");

		$st = $this->pdo->prepare(
			"INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `photo`, `private_message_count`, `group_message_count`, `created_at`, `updated_at`, `last_seen`) VALUES (:user_id, :username, :first_name, :last_name, :photo, 0, 1, :created_at, NULL, :last_seen);"
		)->execute(
			$data = [
				":user_id" => $this->d["user_id"],
				":username" => $this->d["username"],
				":first_name" => $this->d["first_name"],
				":last_name" => $this->d["last_name"],
				":photo" => NULL,
				":created_at" => $now,
				":last_seen" => $now
			]
		);

		unset($data[":last_seen"]);

		$st = $this->pdo->prepare(
			"INSERT INTO `users_history` (`user_id`, `username`, `first_name`, `last_name`, `photo`, `created_at`) VALUES (:user_id, :username, :first_name, :last_name, :photo, :created_at);"
		);
		$st->execute($data);

		unset($data, $now);
	}
}
