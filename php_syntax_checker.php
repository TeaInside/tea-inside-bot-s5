<?php


/**
 * @param string $pDir
 * @param callable $callback
 * @param array $exceptions
 * @return void
 */
function rscanr_callback(string $pDir = ".", callable $callback, array $exceptions = []): void
{
	foreach ($exceptions as &$exception) {
		$exception = realpath($exception);
	}
	unset($exception);

	$s = scandir($pDir);
	unset($s[0], $s[1]);
	foreach ($s as $v) {
		$v = realpath($pDir."/".$v);

		if (!in_array($v, $exceptions)) {
			$callback($v);
			if (is_dir($v)) {
				rscanr_callback($v, $callback);
			}
		}
	}
}

rscanr_callback(__DIR__, function (string $f) {
	if (strtolower(substr($f, -4)) === ".php") {
		$time = time();
		$sh = shell_exec("php -l ".escapeshellarg($f)." || echo std_error_{$time}");
		if (preg_match("/std_error_{$time}/", $sh)) {
			print "\n\nSyntax Error Detected\n";
			exit(1);
		} else {
			print $sh;
		}
	}
}, 
	[
		__DIR__."/vendor"
	]
);
