<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 */
final class DB
{
	/**
	 * @var \DB
	 */
	private static $self;

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->pdo = new PDO(
			"mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME,
			DB_USER,
			DB_PASS,
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			]
		);
	}

	/**
	 * @return \PDO
	 */
	public static function pdo(): PDO
	{
		return self::getInstance()->pdo;
	}

	/**
	 * @return \DB
	 */
	public function getInstance(): DB
	{
		if (!(self::$self instanceof DB)) {
			self::$self = new self;
		}

		return self::$self;
	}
}
