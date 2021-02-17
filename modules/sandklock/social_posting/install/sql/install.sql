CREATE TABLE IF NOT EXISTS `[db_prefix]handlers` (
  `id` int(11) NOT NULL auto_increment,
  `alert_unit` varchar(64) NOT NULL default '',
  `alert_action` varchar(64) NOT NULL default '',
  `module_uri` varchar(64) NOT NULL default '',
  `module_method` varchar(64) NOT NULL default '',
  `enable` tinyint(1) NOT NULL default '0',
  `default_post` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE `handler` (`alert_unit`, `alert_action`, `module_uri`, `module_method`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `[db_prefix]users` (
	`id` int(11) unsigned not null auto_increment,
	`profile_id` int(11) unsigned not null,
	`token` varchar(100) not null,
	`identity` varchar(200) not null,
	`network` varchar(50) not null,
	`username` varchar(100) NOT NULL DEFAULT '',
	`expired_time` varchar(200) NOT NULL,
	primary key(`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]actions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender_id` int(11) NOT NULL,
  `action_key` varchar(100) collate utf8_unicode_ci NOT NULL,
  `params` text collate utf8_unicode_ci NOT NULL,
  `link` text collate utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `[db_prefix]settings`(
  `id` int(11) NOT NULL auto_increment,
  `profile_id` int(11) NOT NULL,
  `plurk` text NOT NULL,
  `mailru` text NOT NULL,
  `lastfm` text NOT NULL,
  `tumblr` text NOT NULL,
  `twitter` text NOT NULL,
  `linkedin` text NOT NULL,
  `facebook` text NOT NULL,
  `auto_publish` text NOT NULL,
  `no_ask` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `profile_id` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]networks` (
  `name` varchar(10) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `status` enum('enabled','disabled') NOT NULL default 'enabled',
  `caption` varchar(255) NOT NULL,
  `profile_url` varchar(255) NOT NULL,
  `order` int(2) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `[db_prefix]networks` (`name`, `logo`, `caption`,`profile_url` , `order`) VALUES
('lastfm', 'lastfm.png', 'Last.fm','http://www.last.fm/user/', 2),
('linkedin', 'linkedin.png', 'LinkedIn','http://www.linkedin.com/', 3),
('twitter', 'twitter.png', 'Twitter','http://www.twitter.com/', 4),
('tumblr', 'tumblr.png', 'Tumblr','http://www.tumblr.com/', 5),
('plurk', 'plurk.png', 'Plurk','http://www.plurk.com/', 6),
('mailru', 'mailru.png', 'Mail.Ru','http://my.mail.ru/', 7),
('facebook', 'facebook.png', 'Facebook','http://www.facebook.com/', 8);

-- permalink
INSERT IGNORE INTO `sys_permalinks` VALUES 
(NULL, 'modules/?r=social_posting', 'm/social_posting', 'sk_social_posting_permalinks'),
(NULL, 'modules/?r=social_posting/', 'm/social_posting/', 'sk_social_posting_permalinks');

-- option permalinks
INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('sk_social_posting_permalinks', 'on', 26, 'Enable friendly social posting permalinks', 'checkbox', '', '', NULL);

--
-- Dumping data for table `sys_menu_member`
--

INSERT IGNORE INTO 
    `sys_menu_member` 
SET
    `Name`   = 'sk_social_posting', 
    `Eval`   = 'return BxDolService::call(''social_posting'', ''get_setting_link'');',
    `Type`   = 'linked_item', 
    `Parent` = 4;

-- Dumping data for table `sys_alerts_handlers`
INSERT IGNORE INTO 
	`sys_alerts_handlers` 
SET 
	`name` 	= 'sk_social_posting_content', 
	`class` = '',
	`file`  = '',
	`eval`  = 'BxDolService::call(\'social_posting\', \'response\', array($this));';

-- top menu	
SET @iTMOrder = (SELECT MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`=118);
INSERT IGNORE INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(118, 'Posting Setting', '_sk_social_posting_link_setting', 'modules/?r=social_posting/social_posting_setting', @iTMOrder+1, 'memb', '', '', '', 1, 1, 1, 'custom', '', 0, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'Sandklock Social posting', '_sk_social_posting_title_admin', '{siteUrl}modules/?r=social_posting/administration/', 'Social posting module by Sandklock Company','modules/sandklock/social_posting/|sp_logo.png', @iMax+1);
	
-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT IGNORE INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Sandklock Social posting', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`)  VALUES
('sk_posting_app_api_id', '', @iCategId, 'App Id', 'digit', '', '', 3, ''),
('sk_posting_app_api_secret', '', @iCategId, 'Secret Key', 'digit', '', '', 4, ''),
('sk_posting_app_api_login_url', 'https://api2.socialall.dev/login', @iCategId, 'Api Login Url', 'digit', '', '', 5, ''),
('sk_posting_app_api_service_url', 'https://api2.socialall.dev', @iCategId, 'Api Service Url', 'digit', '', '', 6, ''),
('sk_posting_subject_message_sign_up', 'Hello guys, I have just registered an account at XYZ.com . Come here and join with me', @iCategId, 'Subject Of Message When User Sign Up', 'text', '', '', 7, ''),
('sk_posting_content_message_sign_up', 'Amazing website', @iCategId, 'Content Of Message When User Sign Up', 'text', '', '', 8, '');

INSERT IGNORE INTO `sys_injections` (`id`, `name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES
('', 'social_posting_inject', 0, 'injection_footer', 'text', '<script type="text/javascript">$.getScript(site_url + ''modules/sandklock/social_posting/js/social_posting.js'');</script>', 0, 1);

-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES 
('sk_posting_setting', 'Social posting Settings', @iMaxOrder+2);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache`) VALUES 
('sk_posting_setting', '1140px', 'Social posting Settings', '_sk_social_posting_caption_settings', '1', '0', 'Settings', '', 11, 100, 'non,memb', 0, 0);

-- alerts handlers
INSERT IGNORE INTO `sys_alerts_handlers` (`name`, `class`, `file`) VALUES 
('sk_social_posting_profile_delete', 'SkSocialPostingDeleteProfileResponse', 'modules/sandklock/social_posting/classes/SkSocialPostingDeleteProfileResponse.php');
SET @iHandler := LAST_INSERT_ID();
INSERT IGNORE INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES ('profile', 'delete', @iHandler);