<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
trait ResponseRoutes
{
	/**
	 * @return void
	 */
	public function text(): void
	{
		if ("/start" === $this->d["text"]) {
			if ($this->exec("Start", "start")) {
				return;
			}
		}

		if ("/help" === $this->d["text"]) {
			if ($this->exec("Help", "help")) {
				return;
			}
		}


		/**
		 * Virtualizor
		 */

		if (preg_match("/^<\?php[\s\n\t].*$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "php", [$m[0]])) {
				return;
			}
		}

		if (preg_match("/^(?:<\?py(?:thon)?2[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "python2", [$m[1]])) {
				return;
			}
		}

		if (preg_match("/^(?:<\?py(?:thon)?3[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "python2", [$m[1]])) {
				return;
			}
		}

		if (preg_match("/^(?:(?:\/|\.|\!|\~|\,)?sh[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "bash", [$m[1]])) {
				return;
			}
		}
		
		if (preg_match("/^(?:<\?(?:c|gcc)[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {
			if ($this->exec("Virtualizor", "c", [$m[1]])) {
				return;
			}
		}
		if (preg_match("/^(?:<\?(?:cpp|g\+\+|c\+\+|gpp)[\s\n\t])(.*)$/Usi", $this->d["text"], $m)) {			
			if ($this->exec("Virtualizor", "cpp", [$m[1]])) {
				return;
			}
		}
	}

	/**
	 * @return bool
	 */
	public function any(): bool
	{
		

		return false;
	}
}
