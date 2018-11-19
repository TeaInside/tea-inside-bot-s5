<?php

namespace tests\ContainerProvider;

use ContainerProvider\Isolate;
use PHPUnit\Framework\TestCase;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \tests\Bot\Telegram
 */
class FirstTest extends TestCase
{
	public function testIsolate()
	{
		$st = new Isolate("qweqwe");
		$this->assertTrue(true);
	}
}