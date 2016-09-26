-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 �?02 �?25 �?12:47
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
-- 表的结构 `mdl_org`
--

CREATE TABLE IF NOT EXISTS `mdl_org` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `mdl_org`
--

INSERT INTO `mdl_org` (`id`, `name`, `level`, `lft`, `rgt`, `rank`, `parent`) VALUES
(3, 'root', 1, 1, 10, 0, -1),
(4, '11', 2, 8, 9, 0, 3),
(5, '10', 2, 2, 7, 0, 3),
(6, '100', 3, 3, 4, 0, 5),
(7, '101', 3, 5, 6, 0, 5);

-- --------------------------------------------------------

--
-- 表的结构 `mdl_org_link_user`
--

CREATE TABLE IF NOT EXISTS `mdl_org_link_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
