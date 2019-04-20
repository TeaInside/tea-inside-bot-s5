<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\Lang;
use Bot\Telegram\ResponseFoundation;
use Bot\Telegram\Utils\GroupSetting;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Responses
 */
class Pilpres extends ResponseFoundation
{
	/**
	 * @param string $host
	 * @return bool
	 */
	public function check(): bool
	{
		$ch = curl_init("https://pemilu2019.kpu.go.id:8443/static/json/hhcw/ppwp.json");
		curl_setopt_array($ch, 
			[
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_FOLLOWLOCATION => true,
			]
		);
		$out = curl_exec($ch);
		curl_close($ch);
		$out = json_decode($out);

		if ((!isset($out->chart->{"21"}))) {
			$reply = "Internal Server Error";
		} else {
			$total = $out->chart->{"21"} + $out->chart->{"22"};
			$a01 = str_replace(".", ",", $out->chart->{"21"} / $total * 100);
			$a02 = str_replace(".", ",", $out->chart->{"22"} / $total * 100);
			$bacTotal = $out->progress->total;
			$bac = $out->progress->proses;
			$bacPercent = str_replace(".", ",", (string)$bac / $bacTotal * 100);


			$out->chart->{"21"} = number_format($out->chart->{"21"}, 0, ",", ".");
			$out->chart->{"22"} = number_format($out->chart->{"22"}, 0, ",", ".");
			$total = number_format($total, 0, ",", ".");
			$bacTotal = number_format($bacTotal, 0, ",", ".");
			$bac = number_format($bac , 0, ",", ".");

			$reply = "<b>STD Time:</b> <code>".date("r", strtotime($out->ts))."</code>\n\n<b>[VST]</b>\n<b>Jokowi-Amin:</b> <code>{$out->chart->{"21"}}</code>\n<b>Prabowo-Sandi:</b> <code>{$out->chart->{"22"}}</code>\n<b>Total VST:</b> <code>{$total}</code>\n\n<b>[VST Percent]</b>\n<b>Jokowi-Amin:</b> <code>{$a01}</code> %\n<b>Prabowo-Sandi:</b> <code>{$a02}</code> %\n\n<b>[BAC]</b>\n<b>BAC:</b> <code>{$bac}</code>\n<b>BAC Percent:</b> <code>{$bacPercent}</code> %\n<b>Total BAC:</b> <code>{$bacTotal}</code>";
		}

		print Exe::sendMessage(
			[
				"chat_id" => $this->d["chat_id"],
				"text" => $reply,
				"reply_to_message_id" => $this->d["msg_id"],
				"parse_mode" => "HTML"
			]
		)["out"];

		return true;
	}
}
