<?php

namespace Bot\Telegram;

use Exception\LangException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
final class Lang
{
	/**
	 * @var self
	 */
	private static $self;

	/**
	 * @var \Bot\Telegram\Data
	 */
	private $d;

	/**
	 * @var string
	 */
	private $lang;

	/**
	 * @param string $lang
	 *
	 * Constructor.
	 */
	private function __construct(string $lang, Data $d)
	{
		$this->lang = $lang;
	}

	/**
	 * @throws \Exception\LangException
	 * @param string $str
	 * @param array  $r1
	 * @param array  $r2
	 * @return string
	 */
	public static function bind(string $str, $r1 = [], $r2 = []): string
	{
		$ins = self::getInstance();
		return str_replace(
			$r1,
			$r2,
			str_replace(
			[
				"{user_id}",
				"{chat_id}",
				"{chat_title}",
				
				"{username}",
				
				"{first_name}",
				"{last_name}",
			],
			[
				$ins->d["user_id"],
				$ins->d["chat_id"],
				$ins->d["chat_title"],

				$ins->d["username"],

				$ins->d["first_name"],
				$ins->d["last_name"]
			],
			$str
		));
	}

	/**
	 * @param string $class
	 * @param string $key
	 * @return string
	 */
	public function get(string $class, string $key): string
	{
		$class = "\\Bot\\Telegram\\Lang\\{$this->lang}\\{$class}";
		if (isset($class::$l[$key])) {
			return $class::$l[$key];
		}
		throw new LangException("Entry for {$class}::{$key} does not exist");
	}

	/**
	 * @return void
	 */
	public static function init(string $lang, Data $d): void
	{
		if (!(self::$self instanceof Lang)) {
			self::$self = new self($lang, $d);
		}
	}

	/**
	 * @return self
	 */
	public static function getInstance(): object
	{
		return self::$self;
	}
}
