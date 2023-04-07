SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `test`;
USE `test`;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
                        `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
                        `email` VARCHAR(255) NOT NULL DEFAULT '',
                        `password` VARCHAR(255) NOT NULL DEFAULT '',
                        `created_at` DATETIME NOT NULL,
                        `updated_at` DATETIME NOT NULL,
                        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
