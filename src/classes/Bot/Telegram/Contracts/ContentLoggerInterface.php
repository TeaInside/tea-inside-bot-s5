<?php

namespace Bot\Telegram\Contracts;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Contracts
 */
interface ContentLoggerInterface extends LoggerInterface
{
	/**
	 * @param \Bot\Telegram\Contracts\MasterLoggerInterface $m
	 * @return void
	 */
	public function setMasterLogger(MasterLoggerInterface $m): void;
}
