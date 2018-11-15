<?php

namespace Bot\Telegram;

use ArrayAccess;
use JsonSerializable;
use Exceptions\InvalidArrayIndexException;
use Exceptions\InvalidJsonFormatException;

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
	public $in;

	/**
	 * The container.
	 *
	 * @var array
	 */
	private $c = [];

	/**
	 * @param string $json
	 *
	 * Constructor.
	 */
	public function __construct(string $json)
	{
		$this->in = $json;
		$this->build();
	}

	/**
	 * @throws \Exceptions\InvalidJsonFormatException
	 * @return void
	 */
	private function build(): void
	{
		$this->in = json_decode($this->in, true);
		if (!is_array($this->in)) {
			throw new InvalidJsonFormatException("JSON must be a valid array.");
		}

		var_dump($this->in);
	}

	/**
	 * @throws \Exceptions\InvalidArrayIndexException
	 * @param mixed $offset
	 * @return mixed
	 */
	public function &offsetGet($offset)
	{	
		if (array_key_exists($offset, $this->c)) {
			return $this->c[$offset];
		}
		throw new InvalidArrayIndexException("Invalid offset {$offset}");
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return isset($this->c[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value): void
	{
		$this->c[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset): void
	{
		unset($this->c[$offset]);
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return is_array($this->c) ? $this->c : [];
	}
}
