<?php

namespace ContainerProvider\Virtualizor\Interpreter;

use ContainerProvider\Isolate;
use ContainerProvider\Contracts\InterpreterInterface;

defined("PYTHON2_BINARY") or die("PYTHON2_BINARY is not defined yet!\n");
defined("PYTHON3_BINARY") or die("PYTHON3_BINARY is not defined yet!\n");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \ContainerProvider\Virtualizor\Interpreter
 * @version 5.0.1
 */
class Python implements InterpreterInterface
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
	 * @var string
	 */
	private $version = "3";

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
	 * @param string $v
	 * @return void
	 */
	public function setVersion($v = "3"): void
	{
		$this->version = $v;
	}

	/**
	 * @return string
	 */
	private function getPythonBinary(): string
	{
		return $this->version == "3" ? PYTHON3_BINARY : PYTHON2_BINARY;
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

		$isDirCsd = is_dir("{$csd}/python{$this->version}");
		((!$isDirCsd) && file_exists("{$csd}/python{$this->version}")) and shell_exec("rm -rf {$csd}/python{$this->version}");
		$isDirCsd or mkdir("{$csd}/python{$this->version}");

		$file = substr(md5($this->code), 0, 5).".py";
		file_put_contents("{$csd}/python{$this->version}/{$file}", $this->code);
		
		$st->setCmd("{$this->getPythonBinary()} /home/u{$uid}/scripts/python{$this->version}/{$file}");
		$st->setMemoryLimit(524288);
		$st->setMaxProcesses(3);
		$st->setMaxWallTime(20);
		$st->setMaxExecutionTime(20);
		$st->setErrToOut();
		$st->exec();
		return (string)($st->getStdout());
	}
}
