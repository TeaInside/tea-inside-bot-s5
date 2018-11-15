<?php

namespace Bot\Telegram;

use ArrayAccess;
use JsonSerializable;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
final class Data implements ArrayAccess, JsonSerializable
{
	/**
	 * @var mixed
	 */
	private $in;

	/**
	 * @param string $json
	 *
	 * Constructor.
	 */
	public function __construct(string $json)
	{
		$this->in = $json;
	}

	/**
	 * @throws \Exceptions\InvalidJsonFormatException
	 * @return void
	 */
	public function build(): void
	{
		$this->in = json_decode($this->in, true);
		if (!is_array($this->in)) {
			throw new InvalidJsonFormatException("JSON must be a valid array.");
		}
	}
}
