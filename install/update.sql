-- LittlePan_v2 增量更新脚本
-- 此脚本仅添加新表和新配置项，不会修改或删除已有数据
-- 所有 CREATE TABLE 使用 IF NOT EXISTS，所有 INSERT 使用 IGNORE 避免重复插入

-- ========== v1.9.0 新增：访问统计表 ==========
CREATE TABLE IF NOT EXISTS `pre_views` (
  `date` date NOT NULL,
  `pv` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ========== v1.9.0 新增：第三方统计配置项 ==========
INSERT IGNORE INTO `pre_config` VALUES ('tongji_open', '0');
INSERT IGNORE INTO `pre_config` VALUES ('tongji', '');

-- ========== v1.9.0 新增：首页公告配置项 ==========
INSERT IGNORE INTO `pre_config` VALUES ('notice_open', '0');
INSERT IGNORE INTO `pre_config` VALUES ('notice_title', '');
INSERT IGNORE INTO `pre_config` VALUES ('notice_content', '');
INSERT IGNORE INTO `pre_config` VALUES ('notice_time', '');

-- ========== v1.9.0 新增：pre_file 表 block 字段（兼容旧版本） ==========
-- 如果 block 字段不存在则添加，已存在则跳过
-- 注意：MySQL 不支持 ADD COLUMN IF NOT EXISTS，需在 update.php 中用 PHP 判断
