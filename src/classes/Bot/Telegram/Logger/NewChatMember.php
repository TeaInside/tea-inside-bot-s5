<?php

namespace Bot\Telegram\Logger;

use DB;
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
class NewChatMember implements ContentLoggerInterface
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
				":_text" => json_encode($this->d->in["message"], JSON_UNESCAPED_SLASHES),
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
		return;
	}
}
