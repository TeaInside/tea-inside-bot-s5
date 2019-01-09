<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Me extends ResponseFoundation
{
	/**
	 * @param string $reason
	 * @return bool
	 */
	public function userReport(string $reason): bool
	{
			
		return true;
	}
}
