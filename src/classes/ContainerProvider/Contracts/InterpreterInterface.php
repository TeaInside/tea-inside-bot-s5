<?php

namespace ContainerProvider\Contracts;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \ContainerProvider\Contracts
 * @version 5.0.1
 */
interface InterpreterInterface
{
	/**
	 * @param string $code
	 * @param string $key
	 *
	 * Constructor.
	 */
	public function __construct(string $code, string $key);

	/**
	 * @return string
	 */
	public function run(): string;
}
