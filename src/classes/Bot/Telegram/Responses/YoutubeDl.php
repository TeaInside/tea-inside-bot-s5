<?php

namespace Bot\Telegram\Responses;

use CurlFile;
use Exception;
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
	 * @var string
	 */
	private $lock_file;

	/**
	 * @var Bot\Telegram\Lang
	 */
	private $lang;

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
		is_dir(STORAGE_PATH."/youtube-dl/lock") or mkdir(STORAGE_PATH."/youtube-dl/lock");
		$this->lock_file = STORAGE_PATH."/youtube-dl/lock/{$this->d["user_id"]}";
		$this->lang = Lang::getInstance();
	}

	/**
	 * @throws \Exception
	 * @return void
	 */
	private function lockd(): void
	{
		if (file_exists($this->lock_file)) {
			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => $this->lang->get("YoutubeDl", "locked")
				]
			);
			throw new Exception("youtube-dl is locked for this user {$this->d["user_id"]}");
		} else {
			file_put_contents($this->lock_file, time());
		}
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		file_exists($this->lock_file) and unlink($this->lock_file);
	}

	/**
	 * @return void
	 */
	private function sanitize(string &$input): void
	{
		if (preg_match("/(?:v\=|youtu\.be\/|youtube\.com\/)([a-zA-Z0-9\_\.\-]+?)(\&|$)/", $input, $m)) {
			$input = $m[1];
			return;
		}

		// Not a youtube URL which has `v` parameter.
		if (filter_var($input, FILTER_VALIDATE_URL)) {
			$input = "~~~";
		}
	}

	/**
	 * @param string $ytid
	 * @return bool
	 */
	public function mp3(string $ytid): bool
	{
		$this->lockd();
		$this->sanitize($ytid);
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
				"text" => $this->lang->get("YoutubeDl", "processing"),
				"reply_to_message_id" => $this->d["msg_id"],
				"chat_id" => $this->d["chat_id"]
			]
		);

		$me = proc_open(
			"exec {$python} {$ytdl} -f 18 --extract-audio --audio-format mp3 {$ytid} --cache-dir /var/cache/youtube-dl",
			$fd,
			$pipes,
			STORAGE_PATH."/youtube-dl/mp3",
			[
				"LC_ALL" => "en_US.UTF-8",
				"LC_CTYPE" => "en_US.UTF-8",
				"LANG" => "en_US.UTF-8"
			]
		);

		if (preg_match("/\[ffmpeg\] Destination: (.*.mp3)/Usi", stream_get_contents($pipes[1]), $m)) {
			proc_close($me);
			$o = json_decode($o["out"], true);
			Exe::editMessageText(
				[
					"chat_id" => $this->d["chat_id"],
					"message_id" => $o["result"]["message_id"],
					"text" => $this->lang->get("YoutubeDl", "download_success")
				]
			);

			Exe::editMessageText(
				[
					"chat_id" => $this->d["chat_id"],
					"message_id" => $o["result"]["message_id"],
					"text" => $this->lang->get("YoutubeDl", "uploading")
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
					"text" => $this->lang->get("YoutubeDl", "error_download"),
					"reply_to_message_id" => $this->d["msg_id"],
					"chat_id" => $this->d["chat_id"]
				]
			);
		}

		return true;
	}
}
