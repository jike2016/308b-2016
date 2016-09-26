/*
Navicat MySQL Data Transfer

Source Server         : zzzzwu
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : oauth

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-05-25 10:36:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for mdl_auth_codes
-- ----------------------------
DROP TABLE IF EXISTS `mdl_auth_codes`;
CREATE TABLE `mdl_auth_codes` (
  `code` varchar(40) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `redirect_uri` varchar(200) NOT NULL,
  `expires` int(11) NOT NULL,
  `scope` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for mdl_clients
-- ----------------------------
DROP TABLE IF EXISTS `mdl_clients`;
CREATE TABLE `mdl_clients` (
  `client_id` varchar(20) NOT NULL,
  `client_secret` varchar(20) NOT NULL,
  `redirect_uri` varchar(200) NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for mdl_tokens
-- ----------------------------
DROP TABLE IF EXISTS `mdl_tokens`;
CREATE TABLE `mdl_tokens` (
  `oauth_token` varchar(40) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `expires` int(11) NOT NULL,
  `scope` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`oauth_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
