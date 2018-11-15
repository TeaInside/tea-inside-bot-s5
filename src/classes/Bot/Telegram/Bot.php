<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
final class Bot
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	public $d;

	/**
	 * @param string $json
	 *
	 * Constructor.
	 */
	public function __construct(string $json)
	{
		$this->d = new Data($json);
	}
}
