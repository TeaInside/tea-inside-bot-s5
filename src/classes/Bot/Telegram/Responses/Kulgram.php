<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Utils\GroupSetting;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Kulgram extends ResponseFoundation
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	protected $d;

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
	 * @param string $cmd
	 * @return bool
	 */
	public function run(string $cmd): bool
	{
		
	}

	/**
	 * @return bool
	 */
	public function init(): bool
	{

		return true;
	}
}
