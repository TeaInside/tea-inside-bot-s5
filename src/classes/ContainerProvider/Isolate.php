<?php

namespace ContainerProvider;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \ContainerProvider
 */

require BASEPATH."/config/isolate.php";

defined("ISOLATE_BASE_DIR") or die("ISOLATE_BASE_DIR is not defined yet!\n");

is_dir(ISOLATE_BASE_DIR) or mkdir(ISOLATE_BASE_DIR);
is_dir(ISOLATE_BASE_DIR."/info") or mkdir(ISOLATE_BASE_DIR."/info");
is_dir(ISOLATE_BASE_DIR."/users") or mkdir(ISOLATE_BASE_DIR."/users");

if ((!is_dir($r = ISOLATE_BASE_DIR."/users")) || (!is_dir($r = ISOLATE_BASE_DIR."/info"))) {
	print "Cannot create directory {$r}\n";
	exit(1);
}

unset($r);

final class Isolate
{
	/**
	 * @var string
	 */
	private $cmd;

	/**
	 * @var string
	 */
	private $isolateCmd;

	/**
	 * @var int
	 */
	private $boxId;

	/**
	 * @var string
	 */
	private $containerDir = "";

	/**
	 * @var string
	 */
	private $containerSupportDir = "";

	/**
	 * @var string
	 */
	private $userInfoDir = "";

	/**
	 * @var int
	 */
	private $uid;

	/**
	 * @var int
	 */
	private $maxProcesses = 5;

	/**
	 * @var int
	 */
	private $memoryLimit = 524288;

	/**
	 * @var int
	 */
	private $maxWallTime = 60;

	/**
	 * @var int
	 */
	private $maxExecutionTime = 30;

	/**
	 * @var int
	 */
	private $extraTime = 15;

	/**
	 * @var bool
	 */
	private $sharenet = true;

	/**
	 * @var string
	 */
	private $chdir = "/";

	/**
	 * @var string
	 */
	private $userKey;

	/**
	 * @var string
	 */
	private $stdoutFile;

	/**
	 * @var string
	 */
	private $stderrFile;

	/**
	 * @var string
	 */
	private $stdoutRealFile;

	/**
	 * @var string
	 */
	private $stderrRealFile;

	/**
	 * @var bool
	 */
	private $errToOut = false;

	/**
	 * @param string $userKey
	 *
	 * Constructor.
	 */
	public function __construct(string $userKey)
	{
		$this->userKey = $userKey;
		$this->keyCheck();
		$this->buildContainer();
	}

	/**
	 * @return void
	 */
	private function keyCheck(): void
	{
		$hf = ISOLATE_BASE_DIR."/uhash_tb.json";

		$hfc = [];

		if (!file_exists($hf)) {
			$this->boxId = 0;
			$this->uid = 60000;
			$hfc["data"] = [
				$this->userKey => $this->boxId
			];
			$hfc["lptr"] = 1;
		} else {
			$hfc = json_decode(file_get_contents($hf), true);
			if (is_array($hfc) && isset($hfc["lptr"]) && is_array($hfc["data"])) {
				
				if (isset($hfc["data"][$this->userKey])) {
					$this->boxId = $hfc["data"][$this->userKey];
					$this->uid = 60000 + $this->boxId;
				} else {
					$this->boxId = $hfc["lptr"]++;
					$this->uid = 60000 + $this->boxId;
					$hfc["data"][$this->userKey] = $this->boxId;
				}

			} else {
				$this->boxId = 0;
				$this->uid = 60000;
				$hfc["data"] = [
					$this->userKey => $this->boxId
				];
				$hfc["lptr"] = 1;
			}
		}

		file_put_contents($hf, json_encode($hfc));
	}

	/**
	 * @return bool
	 */
	private function buildContainer(): bool
	{

		if (!is_dir($this->containerDir = "/var/local/lib/isolate/{$this->boxId}")) {
			shell_exec("/usr/local/bin/isolate --box-id={$this->boxId} --init");
			shell_exec("mkdir -p {$this->containerDir}");
		}

		if (!is_dir($this->containerSupportDir = ISOLATE_BASE_DIR."/users/{$this->boxId}")) {
			shell_exec("mkdir -p {$this->containerSupportDir}");
		}

		if (!is_dir($this->userInfoDir = ISOLATE_BASE_DIR."/info/{$this->boxId}")) {
			shell_exec("mkdir -p {$this->userInfoDir}");
		}

		$g = is_dir($this->containerDir) && is_dir($this->containerSupportDir) && is_dir($this->userInfoDir);
		
		is_dir("{$this->containerSupportDir}/home") or (
			$g = $g && mkdir("{$this->containerSupportDir}/home")
		);

		is_dir("{$this->containerSupportDir}/etc") or (
			$g = $g && mkdir("{$this->containerSupportDir}/etc")
		);

		is_dir("{$this->containerSupportDir}/opt") or (
			$g = $g && mkdir("{$this->containerSupportDir}/opt")
		);

		is_dir("{$this->containerSupportDir}/dockerd") or (
			$g = $g && mkdir("{$this->containerSupportDir}/dockerd")
		);

		$scan = scandir("/etc");

		unset(
			$scan[0], 
			$scan[1], 
			$scan[array_search("passwd", $scan)],
			$scan[array_search("shadow", $scan)]
		);

		$i = 0;

		foreach ($scan as $file) {
			if (!@readlink($f = "{$this->containerSupportDir}/etc/{$file}")) {
				$f = escapeshellarg($f);
				shell_exec("sudo ln -sf /parent_etc/{$file} {$f}");
			}
		}

		is_dir("{$this->containerSupportDir}/home/ubuntu") or (
			shell_exec("cp -rf /etc/skel {$this->containerSupportDir}/home/ubuntu") xor
			($g = $g && is_dir("{$this->containerSupportDir}/home/ubuntu"))
		);
		is_dir("{$this->containerSupportDir}/home/u{$this->uid}") or (
			shell_exec("cp -rf /etc/skel {$this->containerSupportDir}/home/u{$this->uid}") xor
			($g = $g && is_dir("{$this->containerSupportDir}/home/ubuntu"))
		);

		$this->stdoutFile = "/isolated_proc/stdout";
		$this->stderrFile = "/isolated_proc/stderr";

		$this->stdoutRealFile = "{$this->userInfoDir}/stdout";
		$this->stderrRealFile = "{$this->userInfoDir}/stderr";

		file_put_contents($this->stdoutRealFile, "");
		file_put_contents($this->stderrRealFile, "");

		chmod($this->stdoutRealFile, 0755);
		chmod($this->stderrRealFile, 0755);
		chmod("{$this->containerSupportDir}/home/u{$this->uid}", 755);

		chown($this->stdoutRealFile, 60000);
		chown($this->stderrRealFile, 60000);
		chown("{$this->containerSupportDir}/home/u{$this->uid}", 60000);

		return $g;
	}

	/**
	 * @param string $cmd
	 * @return void
	 */
	public function setCmd(string $cmd): void
	{
		$this->cmd = $cmd;
	}

	/**
	 * @param int $n
	 * @return void
	 */
	public function setMaxWallTime(int $n): void
	{
		$this->maxWallTime = $n;
	}

	/**
	 * @param int $n
	 * @return void
	 */
	public function setMaxExecutionTime(int $n): void
	{
		$this->maxExecutionTime = $n;
	}

	/**
	 * @param int $n
	 * @return void
	 */
	public function setMemoryLimit(int $n): void
	{
		$this->memoryLimit = $n;
	}

	/**
	 * @param int $n
	 * @return void
	 */
	public function setExtraTime(int $n): void
	{
		$this->extraTime = $n;
	}

	/**
	 * @param bool $b
	 * @return void
	 */
	public function setErrToOut(bool $b = true): void
	{
		$this->errToOut = $b;
	}

	/**
	 * @param string $str
	 * @return string
	 */
	private function param(string $str): string
	{
		$p = "";
		switch ($str) {
			case "dir":
				$p .= escapeshellarg("--dir=/opt={$this->containerSupportDir}/opt:rw")." ";
				$p .= escapeshellarg("--dir=/etc={$this->containerSupportDir}/etc:rw")." ";
				$p .= escapeshellarg("--dir=/parent_dockerd={$this->containerSupportDir}/dockerd:noexec")." ";
				$p .= escapeshellarg("--dir=/isolated_proc={$this->userInfoDir}:rw");
				$p .= " --dir=/boot=/boot:noexec";
				$p .= " --dir=/sbin=/sbin:rw";
				$p .= " --dir=/parent_etc=/etc:rw";				
				break;
			case "env":
				$p .= "--env=HOME=/home/u{$this->uid} --env=TMPDIR=/tmp --env=LC_ADDRESS=id_ID.UTF-8 --env=LC_NUMERIC=id_ID.UTF-8 --env=LC_MEASUREMENT=id_ID.UTF-8 --env=LC_PAPER=id_ID.UTF-8 --env=LC_MONETARY=id_ID.UTF-8 --env=LANG=en_US.UTF-8 --env=PATH --env=LOGNAME=u{$this->uid} --env=USER=u{$this->uid} --env=/home/u{$this->uid}";
				break;
			case "chdir":
				$p .= "--chdir=".escapeshellarg($this->chdir);
				break;
			case "stdout":
				$p .= "--stdout={$this->stdoutFile}";
				break;
			case "stderr":
				$p .= $this->errToOut ? "--stderr-to-stdout" : "--stderr={$this->stderrFile}";
				break;
			case "memoryLimit":
				$p .= "--mem={$this->memoryLimit}";
				break;
			case "maxWallTime":
				$p .= "--wall-time={$this->maxWallTime}";
				break;
			case "extraTime":
				$p = "--extra-time={$this->extraTime}";
				break;
			case "sharenet":
				$p = $this->sharenet ? "--share-net" : "";
				break;
			default:
				break;
		}

		return "{$p}";
	}

	/**
	 * @return void
	 */
	private function buildIsolateCmd(): void
	{
		$cmd = escapeshellarg($this->cmd);
		$this->isolateCmd = "/usr/local/bin/isolate {$this->param("dir")} {$this->param("env")} {$this->param("chdir")} {$this->param("stdout")} {$this->param("stderr")} {$this->param("memoryLimit")} {$this->param("maxWallTime")} {$this->param("extraTime")} {$this->param("sharenet")} --run -- /usr/bin/env bash -c {$cmd} 2>&1";
	}

	/**
	 * @return void
	 */
	public function exec(): void
	{
		$this->buildIsolateCmd();
		var_dump($this->isolateCmd);
		$this->isolateOut = shell_exec($this->isolateCmd);
		// var_dump($this->isolateOut);
		// print "\n\n";
		// var_dump($this->isolateOut, $this->isolateCmd);
		// print "\n\n";
	}

	/**
	 * @return string
	 */
	public function getStdout(): string
	{
		return (string)(@file_get_contents($this->stdoutRealFile));
	}

	/**
	 * @return string
	 */
	public function getContainerSupportDir(): string
	{
		return $this->containerSupportDir;
	}
}
