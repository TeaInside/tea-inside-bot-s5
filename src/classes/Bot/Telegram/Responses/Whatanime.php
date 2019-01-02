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
				$text = "<b>Result:</b>\n\n";

				foreach ($out as $key => &$v) {
					$key = ucwords(str_replace("_", " ", $key));
					if (is_bool($v)) {
						$v = $v ? "true" : "false";
					}
					$text .= "<b>".htmlspecialchars($key).": </b>".htmlspecialchars($v)."\n";
				}

				Exe::editMessageText(
					[
						"chat_id" => $this->d["chat_id"],
						"message_id" => $o["result"]["message_id"],
						"text" => $text,
						"parse_mode" => "HTML"
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
