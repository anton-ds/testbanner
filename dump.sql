CREATE TABLE `visitors` (
	`id` int(18) NOT NULL AUTO_INCREMENT,
	`ip_address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`user_agent` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`view_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`page_url` text COLLATE utf8_unicode_ci NOT NULL,
	`views_count` int(18) DEFAULT '1',
	`hash` char(32) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`id`),
	KEY `IX_visitors_hash` (`hash`)
);