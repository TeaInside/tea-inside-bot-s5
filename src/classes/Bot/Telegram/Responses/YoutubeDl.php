<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Data;
use Bot\Telegram\Lang;
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
		is_dir(STORAGE_PATH."/youtube-dl") or mkdir(STORAGE_PATH."/youtube-dl");
		is_dir(STORAGE_PATH."/youtube-dl/mp3") or mkdir(STORAGE_PATH."/youtube-dl/mp3");
	}


	/**
	 * @param string $ytid
	 * @return bool
	 */
	public function mp3(string $ytid): bool
	{

		$lang = Lang::getInstance();
		$ytid = escapeshellarg($ytid);
		$ytdl = escapeshellarg(trim(shell_exec("which youtube-dl")));
		$python = escapeshellarg(trim(shell_exec("which python")));

		$fd = [
			["pipe", "r"],
			["pipe", "w"],
			["file", "php://stdout", "w"]
		];

		$pipes = null;

		$o = Exe::sendMessage(
			[
				"text" => $lang->get("YoutubeDl", "processing"),
				"reply_to_message_id" => $this->d["msg_id"],
				"chat_id" => $this->d["chat_id"]
			]
		);

		$me = proc_open(
			"exec {$python} {$ytdl} -f 18 --extract-audio --audio-format mp3 {$ytid} --cache-dir /var/cache/youtube-dl",
			$fd,
			$pipes,
			STORAGE_PATH."/youtube-dl/mp3"
		);

		if (preg_match("/\[ffmpeg\] Destination: (.*.mp3)/Usi", stream_get_contents($pipes[1]), $m)) {
			proc_close($me);
			$o = json_decode($o["out"], true);
			Exe::editMessageText(
				[
					"chat_id" => $this->d["chat_id"],
					"message_id" => $o["result"]["message_id"],
					"text" => $lang->get("YoutubeDl", "download_success")
				]
			);

			Exe::editMessageText(
				[
					"chat_id" => $this->d["chat_id"],
					"message_id" => $o["result"]["message_id"],
					"text" => $lang->get("YoutubeDl", "uploading")
				]
			);

			$ch = curl_init("https://api.telegram.org/bot".BOT_TOKEN."/sendAudio");
			curl_setopt_array($ch, 
				[
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => [
						"chat_id" => $this->d["chat_id"],
						"reply_to_message_id" => $this->d["msg_id"],
						"caption" => $m[1],
						"audio" => new CurlFile(STORAGE_PATH."/youtube-dl/mp3/{$m[1]}")
					],
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_SSL_VERIFYPEER => false
				]
			);
			$st = curl_exec($ch);
			curl_close($ch);
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
