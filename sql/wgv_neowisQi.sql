-- ---------------------------
-- Table structure for wgv_neowisQi
-- ----------------------------
DROP TABLE IF EXISTS `wgv_neowisQi`;
CREATE TABLE `wgv_neowisQi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group` int DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `value` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `create_datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `active` int DEFAULT '1',
  `edit_datetime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `edit_user_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `container_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_group_key` (`group`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
