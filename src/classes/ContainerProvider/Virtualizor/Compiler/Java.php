<?php

namespace ContainerProvider\Virtualizor\Compiler;

use ContainerProvider\Isolate;
use ContainerProvider\Contracts\CompilerInterface;

defined("JAVA_BINARY") or die("JAVA_BINARY is not defined yet!\n");
defined("JAVAC_BINARY") or die("JAVAC_BINARY is not defined yet!\n");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \ContainerProvider\Virtualizor\Compiler
 * @version 5.0.1
 */
class Java implements CompilerInterface
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
	 * @var string
	 */
	private $classname = "";

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
	 * @param string $name
	 * @return void
	 */
	public function setClassName(string $name): void
	{
		$this->classname = $name;
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

		$isDirCsd = is_dir("{$csd}/java");
		((!$isDirCsd) && file_exists("{$csd}/java")) and shell_exec("rm -rf {$csd}/java");
		$isDirCsd or mkdir("{$csd}/java");

		$isDirCsd = is_dir("{$csd}/java/bin");
		((!$isDirCsd) && file_exists("{$csd}/java/bin")) and shell_exec("rm -rf {$csd}/java/bin");
		$isDirCsd or mkdir("{$csd}/java/bin");

		$file = "{$this->classname}.java";
		file_put_contents("{$csd}/java/{$file}", $this->code);

		shell_exec("chmod 777 {$csd}/java {$csd}/java/bin");

		$this->executableFile = "{$this->classname}";
		file_exists("{$csd}/java/classes/{$this->classname}.class") and unlink("{$csd}/java/classes/{$this->classname}.class");

		$st->setCmd(JAVAC_BINARY." /home/u{$uid}/scripts/java/{$file} -d /home/u{$uid}/scripts/java/classes");
		$st->setMemoryLimit(2147483648);
		$st->setMaxProcesses(30);
		$st->setMaxWallTime(100);
		$st->setMaxExecutionTime(100);
		$st->setErrToOut();
		$st->exec();
		$this->compileOutput = (string)$st->getStdout();
		
		unset($st);		

		return file_exists("{$csd}/java/classes/{$this->classname}.class");
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
		$uid = $st->getUid();
		$st->setCmd(JAVA_BINARY." -cp /home/u{$uid}/scripts/java/classes {$this->classname}");
		$st->setMemoryLimit(2147483648);
		$st->setMaxProcesses(15);
		$st->setMaxWallTime(17);
		$st->setMaxExecutionTime(17);
		$st->setErrToOut();
		$st->exec();
		
		return (string)$st->getStdout();
	}
}
