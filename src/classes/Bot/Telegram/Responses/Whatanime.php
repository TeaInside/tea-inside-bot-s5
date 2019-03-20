<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Whatanime\Whatanime as WA;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Whatanime extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function whatanime(): bool
	{

		if (isset($this->d["reply_to_message"]["photo"])) {
			$photo = $this->d["reply_to_message"]["photo"][count($this->d["reply_to_message"]["photo"]) - 1];

			$o = json_decode(Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["reply_to_message"]["message_id"],
					"text" => "Downloading image..."
				]
			)["out"], true);

			$o2 = Exe::getFile(["file_id" => $photo["file_id"]]);
			$o2 = json_decode($o2["out"], true);
			$ch = curl_init("https://api.telegram.org/file/bot".BOT_TOKEN."/{$o2["result"]["file_path"]}");
			curl_setopt_array($ch, 
				[
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_SSL_VERIFYHOST => false
				]
			);
			$bin = curl_exec($ch);
			curl_close($ch);

			Exe::editMessageText(
				[
					"chat_id" => $this->d["chat_id"],
					"message_id" => $o["result"]["message_id"],
					"text" => "Processing image..."
				]
			);

			$st = new WA($bin);

			if ($out = $st->getFirst()) {
$file = $out["file"];
foreach ($out as &$q) {
	if (is_string($q)) {
		$q = htmlspecialchars($q, ENT_QUOTES, "UTF-8");

		if (empty($q)) {
			$q = "unknown";
		}
	}
}
unset($q);
$text = 
"[Query Result]
<b>Similarity ".(100 - $out["diff"])." %</b>

[Anime Info]
<b>Anilist ID: </b> <code>{$out["anilist_id"]}</code>
<b>Title Native :</b> <code>{$out["title_native"]}</code>
<b>Title Chinese :</b> <code>{$out["title_chinese"]}</code>
<b>Title English :</b> <code>{$out["title_english"]}</code>
<b>Title Romanji :</b> <code>{$out["title_romaji"]}</code>

<b>Episode :</b> <code>{$out["episode"]}</code>
<b>Token :</b> <code>{$out["token"]}</code>
<b>Tokenthumb :</b> <code>{$out["tokenthumb"]}</code>

<b>Found in file :</b> <code>/var/app/tea_anime/std_index/{$out["file"]}</code>

<b>Is this an anime for adult only? ".($out["is_adult"] ? "Yes, it is!" : "No")."</b>";

				Exe::editMessageText(
					[
						"chat_id" => $this->d["chat_id"],
						"message_id" => $o["result"]["message_id"],
						"text" => $text,
						"parse_mode" => "HTML"
					]
				);
$out["start"] = date("H:i:s", 1546275600+($s = (int)floor($out["start"])));
$out["end"] = date("H:i:s", 1546275600+($e = (int)floor($out["end"])));

$total = abs($e - $s);

				
				$pid = pcntl_fork();

				if (!$pid) {

					$o = Exe::sendVideo(
						[
							"chat_id" => $this->d["chat_id"],
							"video" => "{$st->getVideo()}?std=me",
							"caption" => 
"{$file}

Start pos: {$out["start"]}
End pos: {$out["end"]}
Total duration: {$total} seconds
",
							"reply_to_message_id" => $this->d["reply_to_message"]["message_id"],
						]
					);

					exit(0);
				} else {
					$status = null;
					while (pcntl_waitpid($pid, $status, WNOHANG) !== -1) {
						Exe::sendChatAction(
							[
								"chat_id" => $this->d["chat_id"],
								"action" => "upload_video"
							]
						);
						sleep(1);
					}
				}
				
			} else {
				Exe::editMessageText(
					[
						"chat_id" => $this->d["chat_id"],
						"message_id" => $o["result"]["message_id"],
						"text" => "Not Found",
						"parse_mode" => "HTML"
					]
				);
			}			
		} else {
			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => "Please reply to an image"
				]
			);
		}

		return true;
	}
}
