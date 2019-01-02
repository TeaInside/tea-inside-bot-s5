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
<b>Similarity ".(100 - $out["diff"])."%</b>

[Anime Info]
<b>Title Native :</b> {$out["title_native"]}
<b>Title Chinese :</b> {$out["title_chinese"]}
<b>Title English :</b> {$out["title_english"]}
<b>Title Romanji :</b> {$out["title_romaji"]}

<b>Episode: </b> {$out["episode"]}
<b>Tokenthumb: </b> {$out["tokenthumb"]}

<b>Found in file :</b> /var/app/tea_anime/std_index/{$out["file"]}

<b>Is it a 18+ anime? ".($out["is_adult"] ? "Yes, it is!" : "No")."</b>e
";

				Exe::editMessageText(
					[
						"chat_id" => $this->d["chat_id"],
						"message_id" => $o["result"]["message_id"],
						"text" => $text,
						"parse_mode" => "HTML"
					]
				);

				Exe::sendVideo(
					[
						"chat_id" => $this->d["chat_id"],
						"video" => $st->getVideo()."?std=me",
						"caption" => $out["file"],
						"reply_to_message_id" => $this->d["reply_to_message"]["message_id"],
					]
				);
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
		}

		return true;
	}
}
