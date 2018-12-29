<?php

namespace ContainerProvider\Virtualizor\Compiler;

use ContainerProvider\Isolate;
use ContainerProvider\Contracts\CompilerInterface;

defined("CPP_BINARY") or die("CPP_BINARY is not defined yet!\n");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \ContainerProvider\Virtualizor\Compiler
 * @version 5.0.1
 */
class Cpp implements CompilerInterface
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
		$spt = $st->getContainerSupportDir();
		$csd = "{$spt}/home/u{$uid}/scripts";
		
		$isDirCsd = is_dir($csd);
		((!$isDirCsd) && file_exists($csd)) and shell_exec("rm -rf {$csd}");
		$isDirCsd or mkdir($csd);

		$isDirCsd = is_dir("{$csd}/cpp");
		((!$isDirCsd) && file_exists("{$csd}/cpp")) and shell_exec("rm -rf {$csd}/cpp");
		$isDirCsd or mkdir("{$csd}/cpp");

		$isDirCsd = is_dir("{$csd}/cpp/bin");
		((!$isDirCsd) && file_exists("{$csd}/cpp/bin")) and shell_exec("rm -rf {$csd}/cpp/bin");
		$isDirCsd or mkdir("{$csd}/cpp/bin");

		$hash = substr(md5($this->code), 0, 5);
		$file = "{$hash}.cpp";
		file_put_contents("{$csd}/cpp/{$file}", $this->code);

		shell_exec("chmod 777 {$csd}/cpp {$csd}/cpp/bin");

		$this->executableFile = "/home/u{$uid}/scripts/cpp/bin/{$hash}";
		file_exists("{$csd}/cpp/bin/{$hash}") and unlink("{$csd}/cpp/bin/{$hash}");

		// $st->setCmd(CPP_BINARY." -fno-stack-protector /home/u{$uid}/scripts/cpp/{$file} -o {$this->executableFile}");
		// $st->setMemoryLimit(1048576);
		// $st->setMaxProcesses(10);
		// $st->setMaxWallTime(100);
		// $st->setMaxExecutionTime(100);
		// $st->setErrToOut();
		// $st->exec();
		// $this->compileOutput = (string)$st->getStdout();

		$this->compileOutput = shell_exec(
			$cmd = CPP_BINARY." -fno-stack-protector {$csd}/cpp/{$file} -o {$spt}{$this->executableFile} 2>&1"
		);

		var_dump($cmd);
		
		unset($st);

		return file_exists("{$csd}/cpp/bin/{$hash}");
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
