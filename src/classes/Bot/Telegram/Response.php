<?php

namespace Bot\Telegram;

use Exceptions\InvalidRouteException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram
 */
final class Response
{
	use ResponseRoutes;

	/**
	 * @var \Bot\Telegram\Data
	 */
	private $d;

	/**
	 * @param \Bot\Telegram\Data $d
	 *
	 * Constructor.
	 */
	public function __construct(Data $d)
	{
		Lang::init("Id", $d);
		$this->d = $d;
	}

	/**
	 * @param string $class
	 * @param string $method
	 * @param array  $parameters
	 * @return bool
	 */
	public function exec($class, string $method, array $parameters = []): bool
	{
		if (is_null($class)) {
			if (is_callable($method)) {
				return (bool)call_user_func_array($method, $parameters);
			} else {
				throw new InvalidRouteException("{$method} is not callable");
			}
		} else {
			if (is_string($class)) {

				$class = "\\Bot\\Telegram\\Responses\\{$class}";

				if (!class_exists($class)) {
					throw new InvalidRouteException("Class {$class} does not exist");
				}

				$class = new $class($this->d);
				if (!is_callable([$class, $method])) {
					throw new InvalidRouteException("{$class}::{$method} is not callable");
				}

				return (bool)call_user_func_array([$class, $method], $parameters);
			} else {
				throw new InvalidRouteException("Class name must be a string!");
			}
		}

		return false;
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		if ($this->d["event_type"] === "general_message") {
			$this->generalMessageHandler();
			return;
		}
	}

	/**
	 * @return void
	 */
	private function generalMessageHandler(): void
	{

		if ($this->any()) {
			return;
		}
		
		switch ($this->d["msg_type"]) {
			case "text":
				$this->text();		
				break;
			
			default:
				break;
		}
	}
}
