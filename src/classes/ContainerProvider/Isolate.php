<?php

namespace ContainerProvider;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \ContainerProvider
 */

require_once BASEPATH."/config/isolate.php";

defined("ISOLATE_BASE_DIR") or die("ISOLATE_BASE_DIR is not defined yet!\n");
defined("ISOLATE_INSIDE_DOCKER") or define("ISOLATE_INSIDE_DOCKER", false);

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
	 * @var int
	 */
	private $maxStack = 3145728;

	/**
	 * @var int
	 */
	private $maxFsize = 314572800;

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
	private $isolateOut = "";

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

		is_dir("{$this->containerDir}/media") or (
			$g = $g && mkdir("{$this->containerDir}/media")
		);

		is_dir("{$this->containerDir}/lost+found") or (
			$g = $g && mkdir("{$this->containerDir}/lost+found")
		);

		is_dir("{$this->containerDir}/srv") or (
			$g = $g && mkdir("{$this->containerDir}/srv")
		);

		is_dir("{$this->containerDir}/mnt") or (
			$g = $g && mkdir("{$this->containerDir}/mnt")
		);

		is_dir("{$this->containerDir}/mnt") or (
			$g = $g && mkdir("{$this->containerDir}/sys")
		);

		// @readlink("{$this->containerDir}/initrd.img") or
		// 	shell_exec("ln -sf boot/initrd.img-4.15.0-23-generic {$this->containerDir}/initrd.img");

		// @readlink("{$this->containerDir}/initrd.img.old") or
		// 	shell_exec("ln -sf boot/initrd.img-4.15.0-22-generic {$this->containerDir}/initrd.img");

		// @readlink("{$this->containerDir}/vmlinuz") or
		// 	shell_exec("ln -sf boot/vmlinuz-4.15.0-23-generic {$this->containerDir}/vmlinuz");

		// @readlink("{$this->containerDir}/initrd.img.old") or
		// 	shell_exec("ln -sf boot/vmlinuz-4.15.0-22-generic {$this->containerDir}/initrd.img");

		$scan = scandir("/etc");

		unset(
			$scan[0], 
			$scan[1], 
			$scan[array_search("passwd", $scan)]
		);

		$i = 0;

		foreach ($scan as $file) {
			if (!@readlink($f = "{$this->containerSupportDir}/etc/{$file}")) {
				$f = escapeshellarg($f);
				shell_exec("ln -sf /parent_etc/{$file} {$f}");
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
		is_dir("{$this->containerSupportDir}/home/u{$this->uid}/scripts") or (
			$g = $g && mkdir("{$this->containerSupportDir}/home/u{$this->uid}/scripts")
		);

		$this->stdoutFile = "/isolated_proc/stdout";
		$this->stderrFile = "/isolated_proc/stderr";

		$this->stdoutRealFile = "{$this->userInfoDir}/stdout";
		$this->stderrRealFile = "{$this->userInfoDir}/stderr";

		file_put_contents($this->stdoutRealFile, "");
		file_put_contents($this->stderrRealFile, "");

		chmod($this->userInfoDir, 0755);
		chmod($this->stdoutRealFile, 0755);
		chmod($this->stderrRealFile, 0755);
		chmod("{$this->containerSupportDir}/home/u{$this->uid}", 0755);

		chown($this->userInfoDir, $this->uid);
		chown($this->stdoutRealFile, $this->uid);
		chown($this->stderrRealFile, $this->uid);
		
		shell_exec("chown -R {$this->uid}:{$this->uid} {$this->containerSupportDir}/home/u{$this->uid}");

		file_put_contents("{$this->containerSupportDir}/etc/passwd",
"root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
sync:x:4:65534:sync:/bin:/bin/sync
games:x:5:60:games:/usr/games:/usr/sbin/nologin
man:x:6:12:man:/var/cache/man:/usr/sbin/nologin
lp:x:7:7:lp:/var/spool/lpd:/usr/sbin/nologin
mail:x:8:8:mail:/var/mail:/usr/sbin/nologin
news:x:9:9:news:/var/spool/news:/usr/sbin/nologin
uucp:x:10:10:uucp:/var/spool/uucp:/usr/sbin/nologin
proxy:x:13:13:proxy:/bin:/usr/sbin/nologin
www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin
backup:x:34:34:backup:/var/backups:/usr/sbin/nologin
list:x:38:38:Mailing List Manager:/var/list:/usr/sbin/nologin
irc:x:39:39:ircd:/var/run/ircd:/usr/sbin/nologin
gnats:x:41:41:Gnats Bug-Reporting System (admin):/var/lib/gnats:/usr/sbin/nologin
systemd-timesync:x:100:102:systemd Time Synchronization,,,:/run/systemd:/bin/false
systemd-network:x:101:103:systemd Network Management,,,:/run/systemd/netif:/bin/false
systemd-resolve:x:102:104:systemd Resolver,,,:/run/systemd/resolve:/bin/false
geoclue:x:103:105::/var/lib/geoclue:/usr/sbin/nologin
syslog:x:104:108::/home/syslog:/bin/false
_apt:x:105:65534::/nonexistent:/bin/false
messagebus:x:106:110::/var/run/dbus:/bin/false
uuidd:x:107:111::/run/uuidd:/bin/false
u{$this->uid}:x:{$this->uid}:{$this->uid}:u{$this->uid},,,:/home/u{$this->uid}:/bin/bash
nobody:x:65534:65534:nobody:/nonexistent:/usr/sbin/nologin");

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
	 * @param int $n
	 * @return void
	 */
	public function setMaxStack(int $n): void
	{
		$this->maxStack = $n;
	}

	/**
	 * @param int $n
	 * @return void
	 */
	public function setMaxFsize(int $n): void
	{
		$this->maxFsize = $n;
	}

	/**
	 * @param int $n
	 * @return void
	 */
	public function setMaxProcesses(int $n): void
	{
		$this->maxProcesses = $n;
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
				$p .= escapeshellarg("--dir=/home={$this->containerSupportDir}/home:rw")." ";
				$p .= escapeshellarg("--dir=/opt={$this->containerSupportDir}/opt:rw")." ";
				$p .= escapeshellarg("--dir=/etc={$this->containerSupportDir}/etc:rw")." ";
				$p .= escapeshellarg("--dir=/parent_dockerd={$this->containerSupportDir}/dockerd:noexec")." ";
				$p .= escapeshellarg("--dir=/isolated_proc={$this->userInfoDir}:rw");
				$p .= " --dir=/boot=/boot:noexec";
				$p .= " --dir=/sbin=/sbin:rw";
				$p .= " --dir=/lib32=/lib32:rw";
				$p .= " --dir=/lib64=/lib64:rw";
				$p .= " --dir=/usr=/usr";
				$p .= " --dir=/parent_etc=/etc:rw";
				break;
			case "maxProcesses":
				$p .= "--processes={$this->maxProcesses}";
				break;
			case "env":
				$p .= "--env=HOME=/home/u{$this->uid} --env=TMPDIR=/tmp --env=LC_ADDRESS=id_ID.UTF-8 --env=LC_NUMERIC=id_ID.UTF-8 --env=LC_MEASUREMENT=id_ID.UTF-8 --env=LC_PAPER=id_ID.UTF-8 --env=LC_MONETARY=id_ID.UTF-8 --env=LANG=en_US.UTF-8 --env=PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games --env=LOGNAME=u{$this->uid} --env=USER=u{$this->uid} --env=/home/u{$this->uid}";
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
			case "maxExecutionTime":
				$p .= "--time={$this->maxExecutionTime}";
				break;
			case "extraTime":
				$p .= "--extra-time={$this->extraTime}";
				break;
			case "sharenet":
				$p .= $this->sharenet ? "--share-net" : "";
				break;
			case "fsize":
				$p .= "--fsize={$this->maxFsize}";
				break;
			case "maxStack":
				$p .= "--stack={$this->maxStack}";
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
		if (ISOLATE_INSIDE_DOCKER) {
			$this->sharenet = false;
		}
		$cmd = escapeshellarg($this->cmd);
		$this->isolateCmd = "nice -n30 /usr/local/bin/isolate --box-id={$this->boxId} {$this->param("dir")} {$this->param("maxProcesses")} {$this->param("env")} {$this->param("chdir")} {$this->param("stdout")} {$this->param("stderr")} {$this->param("memoryLimit")} {$this->param("maxWallTime")} {$this->param("maxExecutionTime")} {$this->param("extraTime")} {$this->param("sharenet")} {$this->param("fsize")} {$this->param("maxStack")} --run -- /usr/bin/env bash -c {$cmd} 2>&1";
	}

	/**
	 * @return void
	 */
	public function exec(): void
	{
		$this->buildIsolateCmd();
		$this->isolateOut = shell_exec($this->isolateCmd);
		// var_dump($this->isolateOut);
		// print "\n\n";
		// var_dump($this->isolateOut, $this->isolateCmd);
		// print "\n\n";
	}

	/**
	 * @return int
	 */
	public function getStdoutSize(): int
	{
		return filesize($this->stdoutRealFile);
	}

	/**
	 * @param int $size
	 * @return string
	 */
	public function getStdout($n = 2048): string
	{
		if ($n === -1) {
			return (string)(@file_get_contents($this->stdoutRealFile));	
		} else {
			$handle = fopen($this->stdoutRealFile, "r");
			$r = fread($handle, $n);
			fclose($handle);
			return $r;
		}
	}

	/**
	 * @return string
	 */
	public function getStderr(): string
	{
		return (string)(@file_get_contents($this->stderrRealFile));
	}

	/**
	 * @return string
	 */
	public function getIsolateOut(): string
	{
		return $this->isolateOut;
	}

	/**
	 * @return string
	 */
	public function getContainerSupportDir(): string
	{
		return $this->containerSupportDir;
	}

	/**
	 * @return int
	 */
	public function getUid(): int
	{
		return $this->uid;
	}
}
