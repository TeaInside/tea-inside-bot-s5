<?php

namespace Bot\Telegram;

use Bot\Telegram\Logger\Text;
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
	 * @param \Bot\Telegram\Data $data
	 *
	 * Constructor.
	 */
	public function __construct(Data $data)
	{
		$this->d = $data;
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
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
				
				default:
					break;
			}

			$se = $this->invokeLogger($se);
			$st->setMasterLogger($se);
			$st = $this->invokeLogger($st);
			unset($st, $se);
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