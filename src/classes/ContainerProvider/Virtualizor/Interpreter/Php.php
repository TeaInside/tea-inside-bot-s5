<?php

namespace ContainerProvider\Virtualizor\Interpreter;

use ContainerProvider\Isolate;
use ContainerProvider\Contracts\InterpreterInterface;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \ContainerProvider\Virtualizor\Interpreter
 * @version 5.0.1
 */
class Php implements InterpreterInterface
{
	/**
	 * @var string
	 */
	private $code = "";

	/**
	 * @var string
	 */
	private $key = "";

	/**
	 * @param string $code
	 * @param string $key
	 *
	 * Constructor.
	 */
	public function __construct(string $code, string $key)
	{
		$this->code = $code;
		$this->key  = $key;
	}

	/**
	 * @return string
	 */
	public function run(): string
	{
		$st = new Isolate($this->key);

		$uid = $st->getUid();
		$csd = "{$st->getContainerSupportDir()}/home/u{$uid}/scripts";

		$isDirCsd = is_dir($csd);
		((!$isDirCsd) && file_exists($csd)) and shell_exec("rm -rf {$csd}");
		$isDirCsd or mkdir($csd);

		$isDirCsd = is_dir("{$csd}/php");
		((!$isDirCsd) && file_exists("{$csd}/php")) and shell_exec("rm -rf {$csd}/php");
		$isDirCsd or mkdir("{$csd}/php");

		$file = substr(md5($this->code), 0, 5).".php";
		file_put_contents("{$csd}/php/{$file}", $this->code);
		
		$st->setCmd("php -d 'opcache.enable=0' /home/u{$uid}/scripts/php/{$file}");
		$st->setMemoryLimit(524288);
		$st->setMaxProcesses(5);
		$st->setMaxWallTime(60);
		$st->setMaxExecutionTime(15);
		$st->setErrToOut();
		$st->exec();
		return (string)($st->getStdout());
	}
}
