<?php

namespace Bot\Telegram\Contracts;

use Bot\Telegram\Data;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Contracts
 */
interface LoggerInterface
{
	/**
	 * @param \Bot\Telegram\Data $d
	 *
	 * Constructor.
	 */
	public function __construct(Data $d);

	/**
	 * @return void
	 */
	public function __invoke(): void;
}
