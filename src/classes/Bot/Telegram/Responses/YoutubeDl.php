<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Data;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class YoutubeDl extends ResponseFoundation
{

	/**
	 * @param \Bot\Telegram\Data $d
	 *
	 * Constructor.
	 */
	public function __construct(Data $d)
	{
		parent::__construct($d);
		is_dir("/var/cache/youtube-dl") or mkdir("/var/cache/youtube-dl");
	}


	/**
	 * @param string $ytid
	 * @return bool
	 */
	public function mp3(string $ytid): bool
	{

		$ytid = escapeshellarg($ytid);
		$ytdl = escapeshellarg(shell_exec("which youtube-dl"));
		$python = escapeshellarg(shell_exec("which python"));

		$me = proc_open(
			"exec {$python} {$ytdl} -f 18 --extract-audio --audio-format mp3 {$ytid} --cache-dir /var/cache/youtube-dl",
			$fd,
			$pipes,
			$this->chdir
		);

		$lang = Lang::getInstance();

		if (preg_match("/\[ffmpeg\] Destination: (.*.mp3)/Usi", stream_get_contents($pipes[1]), $m)) {
			proc_close($me);
			Exe::sendMessage(
				[
					"text" => $m[1],
					"reply_to_message_id" => $this->d["msg_id"],
					"chat_id" => $this->d["chat_id"]
				]
			);
		} else {
			proc_close($me);
			Exe::sendMessage(
				[
					"text" => $lang->get("YoutubeDl", "error_download"),
					"reply_to_message_id" => $this->d["msg_id"],
					"chat_id" => $this->d["chat_id"]
				]
			);
		}

		return true;
	}
}
