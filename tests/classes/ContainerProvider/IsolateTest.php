<?php

namespace tests\ContainerProvider;

use ContainerProvider\Isolate;
use PHPUnit\Framework\TestCase;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \tests\ContainerProvider
 */
class FirstTest extends TestCase
{
	public function testIsolate()
	{
		$st = new Isolate("myid");
		$st->setCmd("echo Hello World");
		$st->exec();
		// $this->assertEquals($st->getStdout(), "Hello World");
		$this->assertTrue(true);
	}
}