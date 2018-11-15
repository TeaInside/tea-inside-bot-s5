<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
trait ResponseRoutes
{
	/**
	 * @return void
	 */
	public function text(): void
	{
		if ("/start" === $this->d["text"]) {
			if ($this->exec()) {
				return;
			}
		}
	}

	/**
	 * @return bool
	 */
	public function any(): bool
	{


		return false;
	}
}
