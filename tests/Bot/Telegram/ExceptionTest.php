<?php

namespace Bot\Telegram;

use PHPUnit\Framework\TestCase;

/**
 * Exceptions
 */
use Exceptions\InvalidJsonFormatException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
class ExceptionTest extends TestCase
{
	public function testInvalidJson()
	{
		$this->expectException(InvalidJsonFormatException::class);

		new Bot("... this is not a json ...");
	}
}
