<?php

namespace Bot\Telegram\Logger\Master;

use DB;
use PDO;
use Bot\Telegram\Exe;
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
	 * @var string
	 */
	private $now;

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
		/**
		 * [__invoke flows]
		 *
		 * {fetch group info} -> (the group is already been exists on database) and (jmp a) or (jmp b)
		 *
		 * a: 
		 * 	call update() -> jmp c
		 *
		 * b:
		 *	call create() -> jmp c
		 *
		 * c:
		 *	call userLogger() -> (the user is already been exists on database) and (jmp d) or (jmp e)
		 *
		 * d:
		 *	call createUser() -> {end of __invoke}
		 *
		 * e:
		 * 	call updateUser() -> {end of __invoke}
		 */

		$this->pdo = DB::pdo();
		$this->now = date("Y-m-d H:i:s");

		$st = $this->pdo->prepare(
			"SELECT `id`,`name`,`username`,`link`,`photo`,`msg_count` FROM `groups` WHERE `id` = :group_id LIMIT 1;"
		);
		$st->execute([":group_id" => $this->d["chat_id"]]);
		if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
			$this->update($st);
			unset($st);
		} else {
			$this->create();
		}

		$this->userLogger();

		return;
	}

	/**
	 * @param array &$st
	 * @return void
	 */
	private function update(array &$st): void
	{
		$data = [];

		$query = "UPDATE `groups` SET `msg_count` = `msg_count` + 1,`last_seen`=:last_seen";

		if ($this->d["chat_title"] !== $st["name"]) {
			$query .= ",`name`=:name";
			$data[":name"] = $this->d["chat_title"];
		}

		if ($this->d["chat_username"] !== $st["username"]) {
			$query .= ",`username`=:username";
			$data[":username"] = $this->d["chat_username"];
		}

		if (count($data)) {
			$this->pdo->prepare(
				"INSERT INTO `groups_history` (`group_id`, `name`, `username`, `link`, `photo`, `created_at`) VALUES (:group_id, :name, :username, :link, :photo, :created_at);"
			)->execute(
				[
					":group_id" => $this->d["chat_id"],
					":name" => $this->d["chat_title"],
					":username" => $this->d["chat_username"],
					":link" => NULL,
					":photo" => NULL,
					":created_at" => $this->now
				]
			);
		}

		$query .= " WHERE `id`=:group_id LIMIT 1;";
		$data[":group_id"] = $st["id"];
		$data[":last_seen"] = $this->now;

		$this->pdo->prepare($query)->execute($data);
		if (($st["msg_count"] % 10) === 0) {
			$this->adminFetcher(true);
		}
		unset($st, $query, $data);
	}

	/**
	 * @param array &$st
	 * @return void
	 */
	private function updateUser(array &$st): void
	{
		$data = [];

		$query = "UPDATE `users` SET `group_message_count`=`group_message_count`+1";

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

		unset($st);
		$this->pdo->prepare($query)->execute($data);
		unset($query, $data);
	}

	/**
	 * @return void
	 */
	private function create(): void
	{
		if ($this->d["event_type"] === "general_message") {
			$msgCount = 1;
		}

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
				":created_at" => $this->now,
				":last_seen" => $this->now
			]
		);

		unset($data[":msg_count"], $data[":last_seen"]);

		$this->pdo->prepare(
			"INSERT INTO `groups_history` (`group_id`, `name`, `username`, `link`, `photo`, `created_at`) VALUES (:group_id, :name, :username, :link, :photo, :created_at);"
		)->execute($data);

		$this->pdo->prepare(
			"INSERT INTO `group_settings` (`group_id`, `max_warns`, `welcome_message`, `created_at`, `updated_at`) VALUES (:group_id, '3', NULL, :created_at, NULL);"
		)->execute(
			[
				":group_id" => $this->d["chat_id"],
				":created_at" => $this->now
			]
		);

		$this->adminFetcher();

		unset($data);
	}

	/**
	 * @param bool $reset
	 * @return void
	 */
	private function adminFetcher($reset = false): void
	{
		$exe = json_decode(Exe::getChatAdministrators(["chat_id" => $this->d["chat_id"]])["out"], true);

		$query = "INSERT INTO `group_admin` (`group_id`, `user_id`, `can_change_info`, `can_delete_messages`, `can_invite_users`, `can_restrict_members`, `can_pin_messages`, `can_promote_members`, `created_at`) VALUES ";
		
		$key = NULL;

		$data = [
			":group_id" => $this->d["chat_id"],
			":created_at" => $this->now
		];


		foreach ($exe["result"] as $key => $v) {
			$query .= "(:group_id, :user_id{$key}, :can_change_info{$key}, :can_delete_messages{$key}, :can_invite_users{$key}, :can_restrict_members{$key}, :can_pin_messages{$key}, :can_promote_members{$key}, :created_at),";
			$data = array_merge($data,
				[
					":user_id{$key}" => $v["user"]["id"],
					":can_change_info{$key}" => (int)$v["can_change_info"],
					":can_delete_messages{$key}" => (int)$v["can_delete_messages"],
					":can_invite_users{$key}" => (int)$v["can_invite_users"],
					":can_restrict_members{$key}" => (int)$v["can_restrict_members"],
					":can_pin_messages{$key}" => (int)$v["can_pin_messages"],
					":can_promote_members{$key}" => (int)$v["can_promote_members"]
				]
			);

			$st = $this->pdo->prepare(
				"SELECT `username`,`first_name`,`last_name`,`photo` FROM `users` WHERE `id` = :user_id LIMIT 1;"
			);
			$st->execute([":user_id" => $v["user"]["id"]]);
			if (!$st->fetch(PDO::FETCH_ASSOC)) {
				$this->pdo->prepare(
					"INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `is_bot`, `photo`, `private_message_count`, `group_message_count`, `created_at`, `updated_at`, `last_seen`) VALUES (:user_id, :username, :first_name, :last_name, :is_bot, :photo, 0, 0, :created_at, NULL, :last_seen);"
				)->execute(
					$data2 = [
						":user_id" => $v["user"]["id"],
						":username" => (isset($v["user"]["username"]) ? $v["user"]["username"] : NULL),
						":first_name" => $v["user"]["first_name"],
						":last_name" => (isset($v["user"]["last_name"]) ? $v["user"]["last_name"] : NULL),
						":is_bot" => (int)$v["user"]["is_bot"],
						":photo" => NULL,
						":created_at" => $this->now,
						":last_seen" => NULL
					]
				);

				unset($data2[":last_seen"], $data2[":is_bot"]);

				$this->pdo->prepare(
					"INSERT INTO `users_history` (`user_id`, `username`, `first_name`, `last_name`, `photo`, `created_at`) VALUES (:user_id, :username, :first_name, :last_name, :photo, :created_at);"
				)->execute($data2);
			}
		}

		unset($exe, $data2, $st, $v);

		if (isset($key)) {
			if ($reset) {
				$this->pdo->prepare(
					"DELETE FROM `group_admin` WHERE `group_id` = :group_id;"
				)->execute([":group_id" => $this->d["chat_id"]]);
			}
			$this->pdo->prepare(rtrim($query, ",").";")->execute($data);
		}
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

		$this->pdo->prepare(
			"INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `is_bot`, `photo`, `private_message_count`, `group_message_count`, `created_at`, `updated_at`, `last_seen`) VALUES (:user_id, :username, :first_name, :last_name, :is_bot, :photo, 0, 1, :created_at, NULL, :last_seen);"
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
}
