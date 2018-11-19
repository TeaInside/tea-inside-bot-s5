<?php

namespace ContainerProvider;

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
	 * @var int
	 */
	private $boxId;

	/**
	 * @var string
	 */
	private $containerDir;

	/**
	 * @var string
	 */
	private $containerSupportDir;

	/**
	 * @var string
	 */
	private $userInfoDir;

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
	 * @var bool
	 */
	public $sharenet = true;

	/**
	 * @var string
	 */
	private $userKey;

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
			shell_exec("mkdir -p {$this->boxDir}");
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

		return $g;
	}
}
