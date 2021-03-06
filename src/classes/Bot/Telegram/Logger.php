<?php

namespace Bot\Telegram;

use Bot\Telegram\Logger\Text;
use Bot\Telegram\Logger\Image;
use Bot\Telegram\Logger\NewChatMember;
use Bot\Telegram\Contracts\LoggerInterface;
use Bot\Telegram\Logger\Master\GroupMessage;
use Bot\Telegram\Logger\Master\PrivateMessage;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
final class Logger
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	private $d;

	/**
	 * @var resources
	 */
	private $fp;

	/**
	 * @param string $json
	 *
	 * Constructor.
	 */
	public function __construct(string $json)
	{
		$this->d = new Data($json);

		if (isset($this->d["chat_id"])) {
			$this->fp = fopen("/tmp/__telegram_lock_".str_replace("-", "_", $this->d["chat_id"]).".lock", "w");
			if (is_resource($this->fp)) {
				while ((!flock($this->fp, LOCK_EX | LOCK_NB, $eWouldBlock)) || $eWouldBlock) {
					usleep(80000);
				}
			}
		}
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		if (is_resource($this->fp)) {
			fclose($this->fp);
		}
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		if (!isset($this->d["event_type"])) {
			return;
		}

		if ($this->d["event_type"] === "general_message") {
			
			switch ($this->d["chat_type"]) {     
				case "private":         
					$se = new PrivateMessage($this->d);
					break;
				
				default:
					$se = new GroupMessage($this->d);
					break;
			}

			switch ($this->d["msg_type"]) {
				case "text":
					$st = new Text($this->d, $se);
					break;
				case "photo":
					$st = new Image($this->d, $se);
					break;
				case "new_chat_member":
					$st = new NewChatMember($this->d, $se);
					break;
				default:
					break;
			}

			if (isset($se, $st)) {
				$se = $this->invokeLogger($se);
				$st->setMasterLogger($se);
				$st = $this->invokeLogger($st);
				unset($st, $se);
			}
		}
	}

	/**
	 * @param \Bot\Telegram\Contracts\LoggerInterface $st
	 * @return \Bot\Telegram\Contracts\LoggerInterface
	 */
	private function invokeLogger(LoggerInterface $st): LoggerInterface
	{
		$st();
		return $st;
	}
}
