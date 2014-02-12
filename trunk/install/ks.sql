SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `ks_sessions` (
  `session_id` char(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `session_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `session_start` int(11) unsigned NOT NULL,
  `session_last_time` int(11) unsigned NOT NULL DEFAULT '0',
  `session_ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `session_page` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `session_user_id` (`session_user_id`,`session_last_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `ks_sessions` (`session_id`, `session_user_id`, `session_start`, `session_last_time`, `session_ip`, `session_page`) VALUES
('20EE9E77-26F7-4C4F-6295-B15D6EB9979A', 1, 1392183859, 1392183897, '127.0.0.1', '/Kotomi-Simplify/index.php');

CREATE TABLE IF NOT EXISTS `ks_sessions_keys` (
  `key_id` char(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `add_time` int(11) unsigned NOT NULL DEFAULT '0',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  `last_ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`key_id`,`user_id`),
  KEY `last_login` (`last_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `ks_users` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_enable` enum('N','Y') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'N',
  `user_level` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_password` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_regdate` int(11) unsigned NOT NULL DEFAULT '0',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `ks_users` (`user_id`, `user_enable`, `user_level`, `user_name`, `user_password`, `user_regdate`, `last_login`) VALUES
(1, 'Y', 0, 'anonymous', '', 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
