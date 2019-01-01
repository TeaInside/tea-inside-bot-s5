<?php

namespace Bot\Telegram\Responses;

use CurlFile;
use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Fb extends ResponseFoundation
{
	/**
	 * @param string $username
	 * @return bool
	 */
	public function fb(string $username): bool
	{
		$cr = function ($url, $opt = []) {
			$ch = curl_init($url);
			$optf = [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_USERAGENT => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:61.0) Gecko/20100101 Firefox/61.0"
			];
			foreach ($opt as $key => $value) {
				$optf[$key] = $value;
			}
			curl_setopt_array($ch, $optf);
			$out = curl_exec($ch);
			$info = curl_getinfo($ch);
			$error = curl_error($ch);
			$errno = curl_errno($ch);

			return [
				"out" => $out,
				"info" => $info,
				"error" => $error,
				"errno" => $errno
			];
		};

		Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"reply_to_message_id" => $this->d["msg_id"],
				"text" => "Scraping facebook data..."
			]
		);

		$username = urlencode($username);
		$o = $cr("https://m.facebook.com/{$username}");
		if (preg_match("/(?:<meta property=\"og:image\" content=\")(.*)(?:\")/Usi", $o["out"], $m)) {
			$photoUrl = html_entity_decode($m[1], ENT_QUOTES, "UTF-8");
			unset($o, $m);
			$o = $cr($photoUrl);
			$fn = rand().time().rand();
			file_put_contents("/tmp/{$fn}.jpg", $o["out"]);
			$cr("https://api.telegram.org/bot".BOT_TOKEN."/sendPhoto",
				[
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => [
						"chat_id" => $this->d["chat_id"],
						"reply_to_message_id" => $this->d["msg_id"],
						"photo" => new CurlFile("/tmp/{$fn}.jpg")
					]
				]
			);
			unset($cr);
		} else {
			Exe::sendMessage(
				[
					"chat_id" => $this->d["chat_id"],
					"reply_to_message_id" => $this->d["msg_id"],
					"text" => "Couldn't fetch {$username}'s facebook photo."
				]
			);
		}

		return true;
	}
}
