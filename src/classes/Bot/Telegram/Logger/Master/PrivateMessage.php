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
class PrivateMessage implements MasterLoggerInterface
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
	 * @var string
	 */
	public $now;

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
		$this->now = date("Y-m-d H:i:s");
		$this->pdo = DB::pdo();
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
	private function createUser(): void
	{
		$this->pdo->prepare(
			"INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `is_bot`, `photo`, `private_message_count`, `group_message_count`, `created_at`, `updated_at`, `last_seen`) VALUES (:user_id, :username, :first_name, :last_name, :is_bot, :photo, 1, 0, :created_at, NULL, :last_seen);"
		)->execute(
			$data = [
				":user_id" => $this->d["user_id"],
				":username" => $this->d["username"],
				":first_name" => $this->d["first_name"],
				":last_name" => $this->d["last_name"],
				":is_bot" => (int)$this->d["is_bot"],
				":photo" => NULL,
				":created_at" => $this->now,
				":last_seen" => $this->now
			]
		);

		unset($data[":last_seen"], $data[":is_bot"]);

		$this->pdo->prepare(
			"INSERT INTO `users_history` (`user_id`, `username`, `first_name`, `last_name`, `photo`, `created_at`) VALUES (:user_id, :username, :first_name, :last_name, :photo, :created_at);"
		)->execute($data);

		unset($data);
	}

	/**
	 * @param array &$st
	 * @return void
	 */
	private function updateUser(array &$st): void
	{
		$data = [];

		$query = "UPDATE `users` SET `private_message_count`=`private_message_count`+1,`last_seen`=:last_seen";

		if ($this->d["username"] !== $st["username"]) {
			$query .= ",`username`=:username";
			$data[":username"] = $this->d["username"];
		}

		if ($this->d["first_name"] !== $st["first_name"]) {
			$query .= ",`first_name`=:first_name";
			$data[":first_name"] = $this->d["first_name"];
		}

		if ($this->d["last_name"] !== $st["last_name"]) {
			$query .= ",`last_name`=:last_name";
			$data[":last_name"] = $this->d["last_name"];
		}

		if (count($data)) {
			$this->pdo->prepare(
				"INSERT INTO `users_history` (`user_id`, `username`, `first_name`, `last_name`, `photo`, `created_at`) VALUES (:user_id, :username, :first_name, :last_name, :photo, :created_at);"
			)->execute(
				[
					":user_id" => $this->d["user_id"],
					":username" => $this->d["username"],
					":first_name" => $this->d["first_name"],
					":last_name" => $this->d["last_name"],
					":photo" => NULL,
					":created_at" => $this->now
				]
			);
		}

		$query .= " WHERE `id`=:user_id LIMIT 1;";
		$data[":user_id"] = $this->d["user_id"];
		$data[":last_seen"] = $this->now;

		unset($st);
		$this->pdo->prepare($query)->execute($data);
		unset($query, $data);
	}
}
