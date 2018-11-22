<?php

namespace tests\ContainerProvider;

use ContainerProvider\Isolate;
use PHPUnit\Framework\TestCase;

/*
Regex replace:

find:		public function \w+\(\): void\n\t{
replace:	$0\n\t\tif (defined("ISOLATE_INSIDE_DOCKER") && ISOLATE_INSIDE_DOCKER) {\n\t\t\t$this->assertTrue(true);\n\t\t\treturn;\n\t\t}\n
*/

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
		if (defined("ISOLATE_INSIDE_DOCKER") && ISOLATE_INSIDE_DOCKER) {
			$this->assertTrue(true);
			return;
		}

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
		if (defined("ISOLATE_INSIDE_DOCKER") && ISOLATE_INSIDE_DOCKER) {
			$this->assertTrue(true);
			return;
		}

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
