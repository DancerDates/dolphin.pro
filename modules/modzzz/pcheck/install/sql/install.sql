-- create tables  

CREATE TABLE IF NOT EXISTS `[db_prefix]cron` (  
  `day` int(11) NOT NULL DEFAULT '0' 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO  `[db_prefix]cron` SET `day`=0;

CREATE TABLE IF NOT EXISTS `[db_prefix]profile_fields` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(100) collate utf8_general_ci NOT NULL, 
  `Weight` float NOT NULL DEFAULT '0',
  `Active` int(11) NOT NULL DEFAULT '1', 
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
INSERT INTO `[db_prefix]profile_fields` (`Name`, `Active`) SELECT `Name`, 0 FROM `sys_profile_fields` WHERE ((`ViewMembBlock` != 0 AND `Type` != 'system') OR `Name`='Avatar');

 -- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=pcheck/', 'm/pcheck/', 'modzzz_pcheck_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('PCheck', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_pcheck_permalinks', 'on', 26, 'Enable friendly permalinks in Profile Completeness module', 'checkbox', '', '', '0', ''),
('modzzz_pcheck_default_percent', '40', @iCategId, 'Default percent completion (to accomodate for compulsory profile fields)', 'digit', '', '', '0', ''),
('modzzz_pcheck_hide_complete', 'on', @iCategId, 'Hide the widget when profile 100 percent completed', 'checkbox', '', '', '0', ''),
('modzzz_pcheck_show_owner', '', @iCategId, 'Show widget to Profile Owner only', 'checkbox', '', '', '0', ''),
('modzzz_pcheck_notify_count', '3', @iCategId, 'Number of notifications to send (0 means continuous until profile is updated)', 'digit', '', '', '0', ''),
('modzzz_pcheck_notify_days', '7', @iCategId, 'How often to send notifications', 'digit', '', '', '0', ''),
('modzzz_pcheck_notify_photo', 'on', @iCategId, 'Notify members if there is no profile photo', 'checkbox', '', '', '0', ''),
('modzzz_pcheck_notify_complete', 'on', @iCategId, 'Notify members if profile is not 100 percent completed', 'checkbox', '', '', '0', '');
 
 

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_pcheck', '_modzzz_pcheck', '{siteUrl}modules/?r=pcheck/administration/', 'Profile Completeness module by Modzzz', 'glass', @iMax+1);
 
 
-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('member', '1140px', 'Profile Completeness block', '_modzzz_pcheck_block_profile_completeness', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''pcheck'', ''progress_block'', array());', 1, 28.1, 'non,memb', 0),
    ('profile', '1140px', 'Profile Completeness block', '_modzzz_pcheck_block_profile_completeness', 2, 3, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''pcheck'', ''progress_block'', array($this->oProfileGen->_iProfileID));', 1, 28.1, 'non,memb', 0);
 
 
-- email templates
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_pcheck_no_photo', 'Please upload a profile photo', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>You have not uploaded a Profile Photo. Please add photo <a href="<PhotoUploadUrl>">here</a> or upload an Avatar image <a href="<AvatarUrl>">here</a>. We appreciate your presence on the site.</p><bx_include_auto:_email_footer.html />', 'No profile photo notification', '0'),
('modzzz_pcheck_incomplete', 'Please update your profile completely', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>You have not completed updating your profile completely at <a href="<Domain>"><SiteName></a>. Please log on to your account and edit your profile <a href="<ProfileUpdateUrl>">here</a>. We appreciate your presence on the site.</p><bx_include_auto:_email_footer.html />', 'Incomplete profile notification', '0');
 
 -- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_pcheck_profile_edit', '', '', 'BxDolService::call(''pcheck'', ''response_profile_edit'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'edit', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_pcheck_photo_add', '', '', 'BxDolService::call(''pcheck'', ''response_photo_add'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'add', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_pcheck_photo_delete', '', '', 'BxDolService::call(''pcheck'', ''response_photo_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);

 

 -- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_pcheck_profile_join', '', '', 'BxDolService::call(''pcheck'', ''response_profile_join'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'join', @iHandler);


INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
( 'modzzz_pcheck_cron', '0 0 * * *', 'BxPCheckCron', 'modules/modzzz/pcheck/classes/BxPCheckCron.php', '');


-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'pcheck allow view', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
     (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);