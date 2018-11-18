<?php

namespace Bot\Telegram\Logger\Master;

use DB;
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
	}
}