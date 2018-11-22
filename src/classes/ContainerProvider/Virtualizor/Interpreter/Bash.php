<?php

namespace ContainerProvider\Virtualizor\Interpreter;

use ContainerProvider\Isolate;
use ContainerProvider\Contracts\InterpreterInterface;

defined("BASH_BINARY") or die("BASH_BINARY is not defined yet!\n");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \ContainerProvider\Virtualizor\Interpreter
 * @version 5.0.1
 */
class Bash implements InterpreterInterface
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
		$code = escapeshellarg($this->code);
		$st->setCmd(BASH_BINARY." -c {$code}");
		$st->setMemoryLimit(524288);
		$st->setMaxProcesses(3);
		$st->setMaxWallTime(20);
		$st->setMaxExecutionTime(20);
		$st->setErrToOut();
		$st->exec();
		return (string)($st->getStdout());
	}
}
