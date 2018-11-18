<?php

namespace Bot\Telegram\Utils;

use DB;
use PDO;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @package \Bot\Telegram\Utils
 */
final class GroupSetting
{
	/**
	 * @var \Bot\Telegram\Utils
	 */
	private static $self;

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @var array
	 */
	private $container = [];

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->pdo = DB::pdo();
	}

	/**
	 * @param int $groupId
	 * @return &array
	 */
	public static function &get(int $groupId): array
	{
		$ins = self::getInstance();
		if (!isset($ins->container[$groupId])) {
			$st = $ins->pdo->prepare("SELECT * FROM `group_settings` WHERE `group_id` = :group_id LIMIT 1;");
			$st->execute([":group_id" => $groupId]);
			if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
				$ins->container[$groupId] = $st;
			} else {
				$ins->container[$groupId] = [];
			}
		}
		return $ins->container[$groupId];
	}

	/**
	 * @return \Bot\Telegram\Utils
	 */
	public static function getInstance(): GroupSetting
	{
		if (!(self::$self instanceof GroupSetting)) {
			self::$self = new self;
		}

		return self::$self;
	}
}
