-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `telegram_file_id` varchar(255) CHARACTER SET latin1 NOT NULL,
  `md5_sum` varchar(32) CHARACTER SET latin1 NOT NULL,
  `sha1_sum` varchar(40) CHARACTER SET latin1 NOT NULL,
  `absolute_hash` varchar(74) CHARACTER SET latin1 NOT NULL,
  `hit_count` bigint(20) NOT NULL DEFAULT '0',
  `file_type` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT 'unknown',
  `extension` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `description` text CHARACTER SET utf8,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absolute_hash` (`absolute_hash`),
  KEY `md5_sum` (`md5_sum`),
  KEY `sha1_sum` (`sha1_sum`),
  KEY `telegram_file_id` (`telegram_file_id`),
  KEY `file_type` (`file_type`),
  FULLTEXT KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `link` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `photo` bigint(20) DEFAULT NULL,
  `msg_count` bigint(20) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_seen` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `photo` (`photo`),
  KEY `name` (`name`),
  KEY `username` (`username`),
  KEY `link` (`link`),
  CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`photo`) REFERENCES `file` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `groups_history`;
CREATE TABLE `groups_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `link` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `photo` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `photo` (`photo`),
  CONSTRAINT `groups_history_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `groups_history_ibfk_4` FOREIGN KEY (`photo`) REFERENCES `file` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `group_admin`;
CREATE TABLE `group_admin` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('creator','administrator') CHARACTER SET latin1 NOT NULL DEFAULT 'administrator',
  `can_change_info` tinyint(1) NOT NULL,
  `can_delete_messages` tinyint(1) NOT NULL,
  `can_invite_users` tinyint(1) NOT NULL,
  `can_restrict_members` tinyint(1) NOT NULL,
  `can_pin_messages` tinyint(1) NOT NULL,
  `can_promote_members` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `group_admin_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_admin_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `group_messages`;
CREATE TABLE `group_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tmsg_id` bigint(20) NOT NULL,
  `reply_to_tmsg_id` bigint(20) DEFAULT NULL,
  `msg_type` varchar(32) CHARACTER SET latin1 NOT NULL,
  `text` text,
  `text_entities` text,
  `file` bigint(20) DEFAULT NULL,
  `is_edited_message` tinyint(1) NOT NULL DEFAULT '0',
  `tmsg_datetime` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `file` (`file`),
  KEY `user_id` (`user_id`),
  KEY `tmsg_id` (`tmsg_id`),
  FULLTEXT KEY `text` (`text`),
  CONSTRAINT `group_messages_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `group_messages_ibfk_5` FOREIGN KEY (`file`) REFERENCES `file` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `group_messages_ibfk_6` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `group_settings`;
CREATE TABLE `group_settings` (
  `group_id` bigint(20) NOT NULL,
  `max_warns` int(11) NOT NULL DEFAULT '3',
  `welcome_message` text CHARACTER SET latin1,
  `cmd_global` tinyint(1) DEFAULT '0',
  `cmd_start` tinyint(1) DEFAULT '0',
  `cmd_help` tinyint(1) DEFAULT '0',
  `cmd_debug` tinyint(1) DEFAULT '0',
  `cmd_shell` tinyint(1) DEFAULT '0',
  `ai_chat` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY `group_id` (`group_id`),
  CONSTRAINT `group_settings_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `private_messages`;
CREATE TABLE `private_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `tmsg_id` bigint(20) NOT NULL,
  `reply_to_tmsg_id` bigint(20) DEFAULT NULL,
  `msg_type` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT 'unknown',
  `text` text,
  `text_entities` text,
  `file` bigint(20) DEFAULT NULL,
  `is_edited_message` tinyint(1) NOT NULL DEFAULT '0',
  `tmsg_datetime` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `tmsg_id` (`tmsg_id`),
  KEY `file` (`file`),
  FULLTEXT KEY `text` (`text`),
  CONSTRAINT `private_messages_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `private_messages_ibfk_4` FOREIGN KEY (`file`) REFERENCES `file` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `sudoers`;
CREATE TABLE `sudoers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sudoers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `is_bot` tinyint(1) DEFAULT '0',
  `photo` bigint(20) DEFAULT NULL,
  `private_message_count` bigint(20) DEFAULT '0',
  `group_message_count` bigint(20) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `photo` (`photo`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`photo`) REFERENCES `file` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `users_history`;
CREATE TABLE `users_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `photo` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `photo` (`photo`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `username` (`username`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_history_ibfk_4` FOREIGN KEY (`photo`) REFERENCES `file` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `users_history_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `user_warning`;
CREATE TABLE `user_warning` (
  `id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `warned_by` int(11) DEFAULT NULL,
  `reason` text CHARACTER SET utf8,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  KEY `group_id` (`group_id`),
  KEY `warned_by` (`warned_by`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_warning_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_warning_ibfk_5` FOREIGN KEY (`warned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_warning_ibfk_6` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2018-12-29 06:41:10
