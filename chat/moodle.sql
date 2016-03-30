-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 �?03 �?03 �?21:48
-- 服务器版本: 5.5.40
-- PHP 版本: 5.5.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `moodle`
--

-- --------------------------------------------------------

--
-- 表的结构 `ajax_chat_bans`
--

CREATE TABLE IF NOT EXISTS `ajax_chat_bans` (
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  PRIMARY KEY (`userID`),
  KEY `userName` (`userName`),
  KEY `dateTime` (`dateTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- 表的结构 `ajax_chat_channel`
--

CREATE TABLE IF NOT EXISTS `ajax_chat_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

--
-- 转存表中的数据 `ajax_chat_channel`
--

INSERT INTO `ajax_chat_channel` (`id`, `name`) VALUES
(0, 'public'),
(9, '3_5'),
(10, '[user]'),
(11, ''),
(12, '3_4'),
(13, '3_3'),
(14, 'Public'),
(15, '4_5'),
(16, '0_5');

-- --------------------------------------------------------

--
-- 表的结构 `ajax_chat_invitations`
--

CREATE TABLE IF NOT EXISTS `ajax_chat_invitations` (
  `userID` int(11) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  PRIMARY KEY (`userID`,`channel`),
  KEY `dateTime` (`dateTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- 表的结构 `ajax_chat_messages`
--

CREATE TABLE IF NOT EXISTS `ajax_chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `userRole` int(1) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `text` text COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  KEY `message_condition` (`id`,`channel`,`dateTime`),
  KEY `dateTime` (`dateTime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `ajax_chat_messages`
--

INSERT INTO `ajax_chat_messages` (`id`, `userID`, `userName`, `userRole`, `channel`, `dateTime`, `ip`, `text`) VALUES
(1, 5, '瀛︾敓1', 1, 16, '2016-03-03 21:23:52', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '10-5'),
(2, 5, '瀛︾敓1', 1, 16, '2016-03-03 21:23:55', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '0-5'),
(3, 5, '瀛︾敓1', 1, 16, '2016-03-03 21:24:03', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '6574566'),
(4, 2147483647, '绯荤粺娑堟伅', 4, 16, '2016-03-03 21:28:49', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '/logout 瀛︾敓1 Timeout'),
(5, 2147483647, '绯荤粺娑堟伅', 4, 0, '2016-03-03 21:28:55', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '/login 瀛︾敓1'),
(6, 5, '瀛︾敓1', 4, 0, '2016-03-03 21:29:04', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '/channelLeave 瀛︾敓1'),
(7, 5, '瀛︾敓1', 4, 15, '2016-03-03 21:29:04', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '/channelEnter 瀛︾敓1'),
(8, 5, '瀛︾敓1', 4, 15, '2016-03-03 21:29:15', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '/channelLeave 瀛︾敓1'),
(9, 5, '瀛︾敓1', 4, 16, '2016-03-03 21:29:15', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '/channelEnter 瀛︾敓1'),
(10, 5, '瀛︾敓1', 4, 16, '2016-03-03 21:31:12', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '/channelLeave 瀛︾敓1'),
(11, 5, '瀛︾敓1', 4, 15, '2016-03-03 21:31:12', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', '/channelEnter 瀛︾敓1');

-- --------------------------------------------------------

--
-- 表的结构 `ajax_chat_no_message`
--

CREATE TABLE IF NOT EXISTS `ajax_chat_no_message` (
  `userID` int(11) NOT NULL,
  `toUserID` int(11) NOT NULL,
  `userName` text COLLATE utf8_bin NOT NULL,
  `channelName` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`userID`,`toUserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='未读消息';

--
-- 转存表中的数据 `ajax_chat_no_message`
--

INSERT INTO `ajax_chat_no_message` (`userID`, `toUserID`, `userName`, `channelName`) VALUES
(6, 3, '瀛︾敓2 ', '3_3'),
(3, 4, 'user ', '3_4'),
(5, 0, '瀛︾敓1 ', '0_5'),
(5, 4, '瀛︾敓1 ', '4_5'),
(5, 3, '瀛︾敓1 ', '3_5');

-- --------------------------------------------------------

--
-- 表的结构 `ajax_chat_online`
--

CREATE TABLE IF NOT EXISTS `ajax_chat_online` (
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `userRole` int(1) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  PRIMARY KEY (`userID`),
  KEY `userName` (`userName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 转存表中的数据 `ajax_chat_online`
--

INSERT INTO `ajax_chat_online` (`userID`, `userName`, `userRole`, `channel`, `dateTime`, `ip`) VALUES
(5, '瀛︾敓1', 1, 15, '2016-03-03 21:46:45', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
