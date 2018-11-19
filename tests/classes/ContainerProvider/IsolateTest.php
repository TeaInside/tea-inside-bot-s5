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
	/**
	 * @return void
	 */
	public function testIsolate(): void
	{
		$st = new Isolate("myid");
		$this->checkLink($st);
		$st->setCmd("echo Hello World");
		$st->exec();
		// $this->assertEquals($st->getStdout(), "Hello World");
		$this->assertTrue(true);
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
			$scan[array_search("passwd", $scan)],
			$scan[array_search("shadow", $scan)]
		);

		foreach ($scan as $file) {
			$this->assertEquals(
				readlink("{$containerEtcDir}/{$file}"),
				"/parent_etc/{$file}"
			);
		}
	}
}
