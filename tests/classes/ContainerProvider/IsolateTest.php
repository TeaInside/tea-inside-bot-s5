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
class IsolateTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testIsolate(): void
	{
		$st = new Isolate("a");
		$this->checkLink($st);
		$st->setErrToOut();
		$st->setCmd("echo Hello World!");
		$st->exec();
		$this->assertEquals($st->getStdout(), "Hello World!\n");
		unset($st);

		$st = new Isolate("a");
		$this->checkLink($st);
		$st->setErrToOut();
		$st->setCmd("echo Hello World!");
		$st->exec();
		$this->assertEquals($st->getStdout(), "Hello World!\n");
		unset($st);
	}

	/**
	 * @return void
	 */
	public function testIsolate2(): void
	{
		$st = new Isolate("b");
		$this->checkLink($st);
		$st->setErrToOut();
		$st->setCmd("echo Hello World!");
		$st->exec();
		$this->assertEquals($st->getStdout(), "Hello World!\n");
		unset($st);

		$st = new Isolate("b");
		$this->checkLink($st);
		$st->setErrToOut();
		$st->setCmd("echo Hello World!");
		$st->exec();
		$this->assertEquals($st->getStdout(), "Hello World!\n");
		unset($st);
	}

	/**
	 * @return void
	 */
	private function checkLink(Isolate $st): void
	{
		$containerEtcDir = "{$st->getContainerSupportDir()}/etc";

		$scan = scandir("/etc");
		unset(
			$scan[0], 
			$scan[1], 
			$scan[array_search("passwd", $scan)]
		);

		foreach ($scan as $file) {
			$this->assertEquals(
				readlink("{$containerEtcDir}/{$file}"),
				"/parent_etc/{$file}"
			);
		}
	}
}
