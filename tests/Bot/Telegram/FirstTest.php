<?php

namespace tests\Bot\Telegram;

use Bot\Telegram\Bot;
use Bot\Telegram\Data;
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
class FirstTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testValidJson(): void
	{
		$st = new Bot(file_get_contents(SAMPLE_DIR."/sample_1.json"));
		$this->assertTrue($st->d instanceof Data);
	}

	/**
	 * @throws \Exceptions\InvalidJsonFormatException
	 * @return void
	 */
	public function testInvalidJson(): void
	{
		$this->expectException(InvalidJsonFormatException::class);
		new Bot("... this is not a json ...");
	}
}
