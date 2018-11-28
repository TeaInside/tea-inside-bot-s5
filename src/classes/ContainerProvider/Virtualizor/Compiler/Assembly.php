<?php

namespace ContainerProvider\Virtualizor\Compiler;

use ContainerProvider\Isolate;
use ContainerProvider\Contracts\CompilerInterface;

defined("LD_BINARY") or die("LD_BINARY is not defined yet!\n");
defined("NASM_BINARY") or die("NASM_BINARY is not defined yet!\n");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \ContainerProvider\Virtualizor\Compiler
 * @version 5.0.1
 */
class Assembly implements CompilerInterface
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

		$isDirCsd = is_dir("{$csd}/asm");
		((!$isDirCsd) && file_exists("{$csd}/asm")) and shell_exec("rm -rf {$csd}/asm");
		$isDirCsd or mkdir("{$csd}/asm");

		$isDirCsd = is_dir("{$csd}/asm/bin");
		((!$isDirCsd) && file_exists("{$csd}/asm/bin")) and shell_exec("rm -rf {$csd}/asm/bin");
		$isDirCsd or mkdir("{$csd}/asm/bin");

		$hash = substr(md5($this->code), 0, 5);
		$file = "{$hash}.asm";
		file_put_contents("{$csd}/asm/{$file}", $this->code);

		shell_exec("chmod 777 {$csd}/asm {$csd}/asm/bin");

		$this->executableFile = "/home/u{$uid}/scripts/asm/bin/{$hash}";
		file_exists("{$csd}/asm/bin/{$hash}") and unlink("{$csd}/asm/bin/{$hash}");

		$st->setCmd(NASM_BINARY." -felf64 /home/u{$uid}/scripts/asm/{$file} -o {$this->executableFile}.o");
		$st->setMemoryLimit(1048576);
		$st->setMaxProcesses(10);
		$st->setMaxWallTime(100);
		$st->setMaxExecutionTime(100);
		$st->setErrToOut();
		$st->exec();
		$this->compileOutput = (string)$st->getStdout();

		$st->setCmd(LD_BINARY." {$this->executableFile}.o -o {$this->executableFile}");
		$st->exec();
		$this->compileOutput .= (string)$st->getStdout();
		
		unset($st);

		return file_exists("{$csd}/asm/bin/{$hash}");
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
		$st->setMaxProcesses(2);
		$st->setMaxWallTime(17);
		$st->setMaxExecutionTime(17);
		$st->setErrToOut();
		$st->exec();
		
		return (string)$st->getStdout();
	}
}
