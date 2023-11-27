-- Adminer 4.8.1 MySQL 8.0.32 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `earthquake_notify`;

CREATE TABLE `eq_hash` (
  `id` int NOT NULL AUTO_INCREMENT,
  `eq_id` varchar(256) COLLATE utf8mb3_bin NOT NULL,
  `message` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

CREATE TABLE `errors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message_id` int unsigned NOT NULL,
  `message` varchar(256) COLLATE utf8mb3_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;


CREATE TABLE `telegram_msg` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message_id` int NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `text` mediumtext COLLATE utf8mb3_bin NOT NULL,
  `status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `error` mediumtext COLLATE utf8mb3_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=435 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- 2023-03-01 17:33:02
