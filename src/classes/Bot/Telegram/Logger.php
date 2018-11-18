<?php

namespace Bot\Telegram;

use Bot\Telegram\Logger\Text;

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
			switch ($this->d["msg_type"]) {
				case "text":
					$st = new Text();
					break;
				
				default:
					break;
			}
			$this->invokeLogger($st);
		}
	}

	/**
	 *
	 */
	private function invokeLogger(LoggerInterface $st): void
	{

	}
}
