-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- 主機: 127.0.0.1
-- 建立日期: 2014 �?02 ??11 ??13:01
-- 伺服器版本: 5.6.14
-- PHP 版本: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫: `ks`
--

-- --------------------------------------------------------

--
-- 資料表結構 `ks_sessions`
--

CREATE TABLE IF NOT EXISTS `ks_sessions` (
  `session_id` char(36) COLLATE utf8_bin NOT NULL,
  `session_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `session_start` int(11) unsigned NOT NULL,
  `session_last_time` int(11) unsigned NOT NULL DEFAULT '0',
  `session_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  `session_page` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `session_user_id` (`session_user_id`,`session_last_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 資料表的匯出資料 `ks_sessions`
--

INSERT INTO `ks_sessions` (`session_id`, `session_user_id`, `session_start`, `session_last_time`, `session_ip`, `session_page`) VALUES
('D7D1DDF3-B363-43CA-8C44-F1FA4DCF35E1', 1, 1392112943, 1392112946, '127.0.0.1', '/Kotomi-Simplify/index.php');

-- --------------------------------------------------------

--
-- 資料表結構 `ks_sessions_keys`
--

CREATE TABLE IF NOT EXISTS `ks_sessions_keys` (
  `key_id` char(36) COLLATE utf8_bin NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `add_time` int(11) unsigned NOT NULL DEFAULT '0',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`key_id`,`user_id`),
  KEY `last_login` (`last_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `ks_users`
--

CREATE TABLE IF NOT EXISTS `ks_users` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_enable` enum('N','Y') COLLATE utf8_bin NOT NULL DEFAULT 'N',
  `user_is_admin` enum('N','Y') COLLATE utf8_bin NOT NULL DEFAULT 'N',
  `user_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `user_password` varchar(32) COLLATE utf8_bin NOT NULL,
  `user_email` varchar(100) COLLATE utf8_bin NOT NULL,
  `user_regdate` int(11) unsigned NOT NULL DEFAULT '0',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- 資料表的匯出資料 `ks_users`
--

INSERT INTO `ks_users` (`user_id`, `user_enable`, `user_is_admin`, `user_name`, `user_password`, `user_email`, `user_regdate`, `last_login`) VALUES
(1, 'Y', 'N', 'anonymous', '', '', 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
