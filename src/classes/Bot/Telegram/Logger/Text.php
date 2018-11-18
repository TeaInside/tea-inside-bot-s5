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
class Text implements ContentLoggerInterface
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
		if ($this->m instanceof GroupMessage) {
			$this->groupMessageAction();
		} else if ($this->m instanceof PrivateMessage) {
			$this->privateMessageAction();
		}
	}

	/**
	 * @return void
	 */
	public function groupMessageAction(): void
	{

	}
}
