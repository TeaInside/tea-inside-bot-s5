<?php

namespace tests\Bot\Telegram\Lang\En;

use Bot\Telegram\Data;
use Bot\Telegram\Lang;
use Bot\Telegram\Lang\En\Start;
use PHPUnit\Framework\TestCase;

/**
 * Exceptions
 */
use Exceptions\InvalidArrayIndexException;
use Exceptions\InvalidJsonFormatException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \tests\Bot\Telegram
 */
class StartTest extends TestCase
{
	/**
	 * @var \Bot\Telegram\Lang
	 */
	private $lang;

	/**
	 * @return void
	 */
	public function setUp()
	{
		Lang::init("En", new Data(file_get_contents(SAMPLE_DIR."/sample_1.json")));
		$this->lang = Lang::getInstance();
	}

	/**
	 * @return void
	 */
	public function testStartInPrivate(): void
	{
		$this->assertEquals(
			$this->lang->get("Start", "private"),
			Start::$l["private"]
		);
	}

	/**
	 * @return void
	 */
	public function testStartInGroup(): void
	{
		$this->assertEquals(
			$this->lang->get("Start", "group"),
			Start::$l["group"]
		);
	}
}
