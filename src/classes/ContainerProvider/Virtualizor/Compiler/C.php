<?php

namespace ContainerProvider\Virtualizor\Compiler;

use ContainerProvider\Isolate;
use ContainerProvider\Contracts\CompilerInterface;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \ContainerProvider\Virtualizor\Compiler
 * @version 5.0.1
 */
class C implements CompilerInterface
{
	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string
	 */
	private $compileOutput = "";

	/**
	 * @var string
	 */
	private $executableFile = "";

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
	 * @return bool
	 */
	public function compile(): bool
	{
		$st = new Isolate($this->key);

		$uid = $st->getUid();
		$csd = "{$st->getContainerSupportDir()}/home/u{$uid}/scripts";
		
		$isDirCsd = is_dir($csd);
		((!$isDirCsd) && file_exists($csd)) and shell_exec("rm -rf {$csd}");
		$isDirCsd or mkdir($csd);

		$isDirCsd = is_dir("{$csd}/c");
		((!$isDirCsd) && file_exists("{$csd}/c")) and shell_exec("rm -rf {$csd}/c");
		$isDirCsd or mkdir("{$csd}/c");

		$isDirCsd = is_dir("{$csd}/c/bin");
		((!$isDirCsd) && file_exists("{$csd}/c/bin")) and shell_exec("rm -rf {$csd}/c/bin");
		$isDirCsd or mkdir("{$csd}/c/bin");

		$hash = substr(md5($this->code), 0, 5);
		$file = "{$hash}.c";
		file_put_contents("{$csd}/c/{$file}", $this->code);

		shell_exec("chmod 777 {$csd}/c {$csd}/c/bin");

		$this->executableFile = "/home/u{$uid}/scripts/c/bin/{$hash}";
		file_exists("{$csd}/c/bin/{$hash}") and unlink("{$csd}/c/bin/{$hash}");

		$st->setCmd("gcc -fno-stack-protector /home/u{$uid}/scripts/c/{$file} -o {$this->executableFile}");
		$st->setMemoryLimit(1048576);
		$st->setMaxProcesses(10);
		$st->setMaxWallTime(100);
		$st->setMaxExecutionTime(100);
		$st->setErrToOut();
		$st->exec();
		$this->compileOutput = (string)$st->getStdout();
		
		unset($st);		

		return file_exists("{$csd}/c/bin/{$hash}");
	}

	/**
	 * @return string
	 */
	public function compileOutput(): string
	{
		return $this->compileOutput;
	}

	/**
	 * @return string
	 */
	public function run(): string
	{
		$st = new Isolate($this->key);
		$st->setCmd($this->executableFile);
		$st->setMemoryLimit(524288);
		$st->setMaxProcesses(5);
		$st->setMaxWallTime(17);
		$st->setMaxExecutionTime(17);
		$st->setErrToOut();
		$st->exec();

		return (string)$st->getStdout();
	}
}
