CREATE TABLE IF NOT EXISTS `[db_prefix]users` (
	`id` int(11) unsigned not null auto_increment,
	`sk_token` varchar(100) not null,
	`identity` varchar(200) not null,
	`network` varchar(50) not null,
	`username` varchar(100) NOT NULL DEFAULT '',
	`profile_id` int(11) unsigned not null,
	`expired_time` varchar(200) NOT NULL,
	primary key(`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]networks` (
  `name` varchar(10) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `status` enum('enabled','disabled') NOT NULL default 'enabled',
  `description` varchar(255) NOT NULL,
  `profile_url` varchar(255) NOT NULL,
  `order` int(2) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `[db_prefix]networks` (`name`, `logo`, `description`,`profile_url` , `order`) VALUES
('facebook', 'facebook.png', 'facebook logo','http://www.facebook.com/', 1),
('lastfm', 'lastfm.png', 'lastfm logo','http://www.last.fm/user/', 2),
('linkedin', 'linkedin.png', 'linkedin logo','http://www.linkedin.com/', 3),
('twitter', 'twitter.png', 'twitter logo','http://www.twitter.com/', 4),
('tumblr', 'tumblr.png', 'tumblr logo','http://www.tumblr.com/', 5),
('plurk', 'plurk.png', 'plurk logo','http://www.plurk.com/', 6),
('mailru', 'mailru.png', 'mailru logo','http://my.mail.ru/', 7),
('google', 'google.png', 'google logo','http://plus.google.com/', 8),
('reddit', 'reddit.png', 'reddit logo','http://www.reddit.com/user/', 9),
('vkontakte', 'vkontakte.png', 'vkontakte logo','http://vk.com/', 10),
('live', 'live.png', 'live logo','http://profile.live.com/', 11),
('github', 'github.png', 'github logo','http://github.com/', 12),
('disqus', 'disqus.png', 'disqus logo','http://disqus.com/', 13),
('wordpress', 'wordpress.png', 'wordpress logo','http://wordpress.com/me/', 14),
('foursquare', 'foursquare.png', 'foursquare logo','http://foursquare.com/user/', 15),
('instagram', 'instagram.png', 'instagram logo','https://www.instagram.com/', 16),
('pinterest', 'pinterest.png', 'pinterest logo','https://www.pinterest.com/', 17),
('amazon', 'amazon.png', 'amazon logo','http://www.amazon.com/', 18),
('ebay', 'ebay.png', 'ebay logo','http://www.ebay.com/', 19);

-- social login block on the compose pages

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`,`Cache`) VALUES
(NULL, 'index', '1140px', 'Login By Sandklock Social Login', '_sk_social_login_block_index', 2, 0, 'PHP', 'return BxDolService::call(''social_login'', ''get_block_login'');', 1, 28.1, 'non', 0, 0),
(NULL,'member', '1140px', 'Social Login Block', '_sk_social_login_block_member', 2, 4, 'PHP', 'return BxDolService::call(''social_login'', ''get_member_block_login'');', 1, 28.1, 'memb', 0, 0),
(NULL, 'join', '1140px', 'Login By Sandklock Social Login', '_sk_social_login_block_join', 2, 0, 'PHP', 'return BxDolService::call(''social_login'', ''get_block_login'',array("join_page"));', 1, 28.1, 'non', 250, 0);

-- permalink

INSERT IGNORE INTO `sys_permalinks` VALUES 
(NULL, 'modules/?r=social_login', 'm/social_login', 'sk_social_login_permalinks'),
(NULL, 'modules/?r=social_login/', 'm/social_login/', 'sk_social_login_permalinks');

-- option permalinks

INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('sk_social_login_permalinks', 'on', 26, 'Enable friendly sandklock social login permalinks', 'checkbox', '', '', NULL);

-- admin menu

SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'Sandklock Social Login', '_sk_social_login_title_admin', '{siteUrl}modules/?r=social_login/administration/', 'Social Login module by Sandklock Developments','modules/sandklock/social_login/|sl_logo.png', @iMax+1);

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Sandklock Social Login Setting', @iMaxOrder+1);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`)  VALUES
('sk_social_login_display_captcha', 'on', @iCategId, 'Display captcha at Mapping Profile Form', 'checkbox', '', '', 1, ''),
('sk_social_login_post_twitter', 'on', @iCategId, 'Automatically post status on Twitter when user signup by Twitter', 'checkbox', '', '', 2, ''),
('sk_social_login_post_tumblr', 'on', @iCategId, 'Automatically post status on Tumblr when user signup by Tumblr', 'checkbox', '', '', 3, ''),
('sk_social_login_post_plurk', 'on', @iCategId, 'Automatically post status on Plurk when user signup by Plurk', 'checkbox', '', '', 4, ''),
('sk_social_login_post_mailru', 'on', @iCategId, 'Automatically post status on Mailru when user signup by Mailru', 'checkbox', '', '', 5, ''),
('sk_social_login_post_linkedin', 'on', @iCategId, 'Automatically post status on Linkedin when user signup by Linkedin', 'checkbox', '', '', 6, ''),
('sk_social_login_post_lastfm', 'on', @iCategId, 'Automatically post status on Lastfm when user signup by Lastfm', 'checkbox', '', '', 7, ''),
('sk_social_login_status_content', 'I have login this site. Join with me!', @iCategId, 'Status content', 'text', '', '', 8, '');


SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Sandklock Social Login Api Setting', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`)  VALUES
('sk_social_login_app_id', '', @iCategId, 'App ID', 'digit', '', '', 1, ''),
('sk_social_login_secret_key', '', @iCategId, 'Secret Key', 'digit', '', '', 2, '');

-- email template

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('sk_social_login_information', '<SiteName> account''s information', '<bx_include_auto:_email_header.html />

	<p>Hi <Username>,</p>
	<p>A new account - that mapping with <Network> account - was created on <SiteName> site. Please login with below information and change your password asap:</p>
	<p>Username: <Username></p>
	<p>Password: <Password></p>
	
	
	<bx_include_auto:_email_footer.html />', 'Social Login Template', '0');

-- injections

INSERT INTO `sys_injections` (`name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES 
('social_login_popup', '0', 'injection_footer', 'text', '<script>$.getScript(site_url+"modules/sandklock/social_login/js/social_login.js")</script>', 0, 1);

-- alerts handlers
INSERT IGNORE INTO `sys_alerts_handlers` (`name`, `class`, `file`) VALUES 
('sk_social_login_profile_delete', 'SkSocialLoginDeleteProfileResponse', 'modules/sandklock/social_login/classes/SkSocialLoginDeleteProfileResponse.php');
SET @iHandler := LAST_INSERT_ID();
INSERT IGNORE INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES ('profile', 'delete', @iHandler);