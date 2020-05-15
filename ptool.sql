/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : ptool

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-05-15 14:41:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `account`
-- ----------------------------
DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `salt` varchar(32) DEFAULT NULL COMMENT 'token局部随机盐，当账号被黑时改此值强制登出',
  `scope` varchar(32) DEFAULT NULL COMMENT '权限值',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of account
-- ----------------------------
INSERT INTO `account` VALUES ('1', 'admin', '6ccc438bbf1dbf0782e0094aa3920c6c', 'carolsail', 'carolsail2013@gmail.com', '123', '8ptuTU', '32', null, null);

-- ----------------------------
-- Table structure for `task_category`
-- ----------------------------
DROP TABLE IF EXISTS `task_category`;
CREATE TABLE `task_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `is_active` int(2) DEFAULT '1',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of task_category
-- ----------------------------

-- ----------------------------
-- Table structure for `task_item`
-- ----------------------------
DROP TABLE IF EXISTS `task_item`;
CREATE TABLE `task_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `is_urgent` int(2) NOT NULL DEFAULT '0' COMMENT '是否加急，1是0否',
  `status` int(2) DEFAULT '1' COMMENT '1 待处理，2 处理中，3 暂停，4 超时中，5 完成',
  `status_text` varchar(10) DEFAULT 'undo' COMMENT '1：undo，2：pending，3：paused，4：overing，5：done',
  `is_top` int(11) DEFAULT '1' COMMENT 'done：0，undo：1，pause：2，pending/overing：3',
  `deadline` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `deadline_check` int(2) DEFAULT '0' COMMENT '标识deadline已经check',
  `is_deadline` int(2) DEFAULT '0' COMMENT '当任务完成，待完成时间超过deadline的时候便置为deadline=1',
  `deadline_daily` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of task_item
-- ----------------------------

-- ----------------------------
-- Table structure for `task_timer`
-- ----------------------------
DROP TABLE IF EXISTS `task_timer`;
CREATE TABLE `task_timer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL COMMENT 'start, pause, over, done',
  `start` int(11) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT '负数表示超时秒数',
  `remark` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of task_timer
-- ----------------------------
