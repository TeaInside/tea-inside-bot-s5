<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
final class Response
{
	use ResponseRoutes;

	/**
	 * @var \Bot\Telegram\Data
	 */
	private $d;

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
	public function run(): void
	{
		if ($this->d["event_type"] === "general_message") {
			$this->generalMessageHandler();
			return;
		}
	}

	/**
	 * @return void
	 */
	private function generalMessageHandler(): void
	{

		if ($this->any()) {
			return;
		}

		switch ($this->d["msg_type"]) {
			case "text":
				
				break;
			
			default:
				break;
		}
	}
}
