CREATE TABLE IF NOT EXISTS `[db_prefix]users` (
	`id` int(11) unsigned not null auto_increment,
	`network` varchar(50) not null,
	`friend_identity` varchar(100) NOT NULL DEFAULT '',
	`profile_id` int(11) unsigned not null,
	`date` date default NULL,
	primary key(`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]networks` (
  `name` varchar(10) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `status` enum('enabled','disabled') NOT NULL default 'enabled',
  `description` varchar(255) NOT NULL,
  `profile_url` varchar(255) NOT NULL,
  `caption` varchar(200) NOT NULL,
  `order` int(2) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `[db_prefix]networks` (`name`, `logo`, `description`,`profile_url`,`caption` , `order`) VALUES
('lastfm', 'lastfm.png', 'lastfm logo','http://www.last.fm/user/','Last.fm', 1),
--('linkedin', 'linkedin.png', 'linkedin logo','http://www.linkedin.com/','LinkedIn', 3),
('twitter', 'twitter.png', 'twitter logo','http://www.twitter.com/','Twitter', 2),
('tumblr', 'tumblr.png', 'tumblr logo','http://www.tumblr.com/','Tumblr', 3),
('plurk', 'plurk.png', 'plurk logo','http://www.plurk.com/','Plurk', 4),
('mailru', 'mailru.png', 'mailru logo','http://my.mail.ru/','Mail.ru', 5),
('google', 'google.png', 'google logo','http://plus.google.com/','Google mail', 6),
('other', 'other.png', 'other logo','','', 7);

-- permalink
INSERT IGNORE INTO `sys_permalinks` VALUES 
(NULL, 'modules/?r=social_inviter', 'm/social_inviter', 'sk_social_inviter_permalinks'),
(NULL, 'modules/?r=social_inviter/', 'm/social_inviter/', 'sk_social_inviter_permalinks');

INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('sk_social_inviter_permalinks', 'on', 26, 'Enable friendly sandklock social inviter permalinks', 'checkbox', '', '', NULL);

-- top menu 
--SET @iTopMenuLastOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
--INSERT IGNORE INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
--(NULL, 0, 'Sandklock Social Inviter', '_sk_social_inviter_top_menu', 'modules/?r=social_inviter|modules/?r=social_inviter/home', @iTopMenuLastOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'modules/sandklock/social_inviter/|si_logo1.png', '', 1, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');

INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'Sandklock Social Inviter', '_sk_social_inviter_admin_menu', '{siteUrl}modules/?r=social_inviter/administration/', 'Social Inviter module by Sandklock', 'modules/sandklock/social_inviter/|si_logo1.png', @iMax+1);

-- blocks on index,profile page

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`,`Cache`) VALUES
(NULL, 'member', '1140px', 'Social Inviter block', '_sk_social_inviter_profile_block', 1, 0, 'PHP', 'return BxDolService::call(''social_inviter'', ''gen_inviter_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'memb', 0,0),
(NULL, 'index', '1140px', 'Social Inviter block', '_sk_social_inviter_profile_block', 1, 0, 'PHP', 'return BxDolService::call(''social_inviter'', ''gen_inviter_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'memb', 0,0);

-- action button

--INSERT INTO `sys_objects_actions` (`ID`, `Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
--(NULL, '_sk_social_inviter_home_button', 'modules/sandklock/social_inviter/|si_logo1.png', '{module_url}', '', '', 0, '[db_prefix]', 1);

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Sandklock Social Inviter Setting', @iMaxOrder+1);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`)  VALUES
('sk_social_inviter_invitation_link', '', @iCategId, 'Invitation Link', 'digit', '', '', 3, ''),
('sk_social_inviter_invitation_message', 'I have found out an amazing website. Join it now!', @iCategId, "Invitation Message's Content", 'text', '', '', 2, ''),
('sk_social_inviter_invitation_title', 'An interesting website', @iCategId, "Invitation Message's Title", 'digit', '', '', 1, ''),
('sk_social_inviter_quantity_invitation', '1000', @iCategId, 'Maximum invitation member can send per day', 'digit', '', '', 4, '');


SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`)
SELECT 'Sandklock SocialAll API Settings', @iMaxOrder FROM `sys_options_cats` 
WHERE NOT EXISTS (SELECT * FROM `sys_options_cats` WHERE name='Sandklock SocialAll API Settings') LIMIT 1;

SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock SocialAll API Settings' LIMIT 1);
INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`)  VALUES
('sk_socialall_settings_app_id', '', @iCategId, 'App ID', 'digit', '', '', 1, ''),
('sk_socialall_settings_secret_key', '', @iCategId, 'Secret Key', 'digit', '', '', 2, '');

-- email template

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('sk_social_inviter_email', 'An interesting website', '<bx_include_auto:_email_header.html />

<p>Hello<FriendName>,</p>

<div style="float:left;width:80px;padding-top:15px;">
  <img src="<Avatar>"/>
</div>

<div style="float:left;width:400px;">
  <p>
	<a href="<ProfileUrl>"><strong><NickName></strong></a> has invited you to our website with the message:<br/>
	<blockquote><Message></blockquote>
  </p>
  <p>
	Please go to <a href="<SiteUrl>"><SiteName></a> to join and play with your friend.
  </p>
</div>

<div style="clear:both;"></div>

<bx_include_auto:_email_footer.html />', 'Social Inviter Template', '0');

-- injections

INSERT INTO `sys_injections` (`name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES 
('social_inviter_popup', '0', 'injection_footer', 'text', '<script>$.getScript(site_url+"modules/sandklock/social_inviter/js/social_inviter.js")</script>', 0, 1);