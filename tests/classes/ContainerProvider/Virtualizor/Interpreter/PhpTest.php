<?php

namespace tests\ContainerProvider\Virtualizor\Interpreter;

use ContainerProvider\Isolate;
use PHPUnit\Framework\TestCase;
use ContainerProvider\Virtualizor\Interpreter\Php;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \tests\ContainerProvider
 */
class PhpTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testPhpGeneral(): void
	{
		$st = new Php("<?php echo \"Hello World!\";", "a");
		$this->assertEquals($st->run(), "Hello World!");
	}

	/**
	 * @return void
	 */
	public function testMemoryLeak(): void
	{
		$st = new Php("<?php \$a = \"\"; while(1) \$a .= str_repeat(rand(), 100000);", "a");
		$this->assertTrue((bool)preg_match("/Cannot allocate memory/", $st->run()));
	}
}
