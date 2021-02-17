-- This must remain for a few versions to correct the improper removal on older versions.
DELETE FROM `sys_options` WHERE `Name` = 'deanos_facebook_connect_prompt_profile_type';

-- 
-- `sys_menu_admin`;
--
SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin` SET `name` = 'Deanos Facebook Connect', `title` = '_dbcs_facebook', `url` = '{siteUrl}modules/?r=deanos_facebook_connect/administration/', `description` = 'Manage Deanos \'facebook connect\' settings', `icon` = 'modules/deano/deanos_facebook_connect/|facebook-icon_little.png', `parent_id` = 2, `order` = @iOrder+1;

-- tables
CREATE TABLE IF NOT EXISTS `dbcs_facebook_connect_data` (
  `memberID` int(11) NOT NULL,
  `NagTime` int(11) NOT NULL,
  `LogoutURL` text NOT NULL,
  `OldNickName` varchar(255) NOT NULL,
  `FacebookUrl` varchar(255) NOT NULL,
  `FacebookUserName` varchar(255) NOT NULL,
  UNIQUE KEY `memberID` (`memberID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

    -- page compose pages
    INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`, `System`) VALUES ('profile_info_required', 'Profile Info Required', 0, 1);

    -- page compose blocks
    INSERT INTO `sys_page_compose` (`Page`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES ('profile_info_required', 'Profile Info Required', '_dbcs_FC_profile_info_required', 1, 0, 'BlockOne', '', 1, 100, 'non,memb', 0);

    --
    -- Dumping data for table `sys_objects_auths`
    --

    INSERT INTO `sys_objects_auths` (`Title`, `Link`) VALUES ('_dbcs_facebook', 'modules/?r=deanos_facebook_connect/login_form');

    --
    -- `sys_alerts_handlers` ;
    --

    INSERT INTO
        `sys_alerts_handlers`
    SET
        `name`  = 'dbcs_facebook_connect',
        `class` = 'BxDbcsFaceBookConnectAlerts',
        `file`  = 'modules/deano/deanos_facebook_connect/classes/BxDbcsFaceBookConnectAlerts.php';

    SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers`  WHERE `name`  =  'dbcs_facebook_connect');
  
    --
    -- `sys_alerts` ;
    --

    INSERT INTO
        `sys_alerts`
    SET
        `unit`       = 'profile',
        `action`     = 'logout',
        `handler_id` = @iHandlerId;

    INSERT INTO
        `sys_alerts`
    SET
        `unit`       = 'profile',
        `action`     = 'edit',
        `handler_id` = @iHandlerId;

    INSERT INTO
        `sys_alerts`
    SET
        `unit`       = 'profile',
        `action`     = 'delete',
        `handler_id` = @iHandlerId;

-- Profiles
ALTER TABLE `Profiles` ADD `dbcsFacebookProfile` BIGINT(20) NOT NULL ;
ALTER TABLE `Profiles` ADD INDEX (`dbcsFacebookProfile`) ;

--
-- `sys_options_cats` ;
-- Standard settings catagory

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Deanos Facebook Connect', @iMaxOrder);
SET @iKategId = (SELECT LAST_INSERT_ID());

-- sys_options

INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_api_key', `kateg` = @iKategId, `desc` = 'Facebook Application ID', `Type` = 'digit', `VALUE` = '', `order_in_kateg` = 1;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_secret_key', `kateg` = @iKategId, `desc` = 'Facebook Application Secret Key', `Type` = 'digit', `VALUE` = '', `order_in_kateg` = 2;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_use_popup', `kateg` = @iKategId, `desc` = 'Use facebook popup logon method', `Type` = 'checkbox', `VALUE` = '', `AvailableValues` = 'FirstName_LastName,FirstName,LastName', `order_in_kateg` = 3;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_option1', `kateg` = @iKategId, `desc` = 'Use facebook field for logon(First)', `Type` = 'select', `VALUE` = 'Username', `AvailableValues` = 'Username,FirstName,FirstName_LastName,LastName', `order_in_kateg` = 4;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_option2', `kateg` = @iKategId, `desc` = 'Use facebook field for logon(Second)', `Type` = 'select', `VALUE` = 'FirstName', `AvailableValues` = 'Username,FirstName,FirstName_LastName,LastName', `order_in_kateg` = 5;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_option3', `kateg` = @iKategId, `desc` = 'Use facebook field for logon(Third)', `Type` = 'select', `VALUE` = 'FirstName_LastName', `AvailableValues` = 'Username,FirstName,FirstName_LastName,LastName', `order_in_kateg` = 6;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_option4', `kateg` = @iKategId, `desc` = 'Use facebook field for logon(Fourth)', `Type` = 'select', `VALUE` = 'LastName', `AvailableValues` = 'Username,FirstName,FirstName_LastName,LastName', `order_in_kateg` = 7;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_allow_spaces', `kateg` = @iKategId, `desc` = 'Allow Spaces in Nick Name<br />Do not enable unless you have modified<br />dolphin to allow spaces in nickname', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 8;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_nag_time', `kateg` = @iKategId, `desc` = 'Profile check interval/hours', `Type` = 'digit', `VALUE` = '0', `order_in_kateg` = 9;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_redirect2', `kateg` = @iKategId, `desc` = 'Redirect to page after connect', `Type` = 'digit', `VALUE` = 'member.php', `order_in_kateg` = 10;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_logout_redirect', `kateg` = @iKategId, `desc` = 'Redirect to page after logout', `Type` = 'digit', `VALUE` = '', `order_in_kateg` = 11;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_unregister_redirect', `kateg` = @iKategId, `desc` = 'Redirect to page after unregister', `Type` = 'digit', `VALUE` = '', `order_in_kateg` = 12;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_fb_logout', `kateg` = @iKategId, `desc` = 'When logging out of site, also log out of facebook', `Type` = 'checkbox', `VALUE` = 'on', `order_in_kateg` = 13;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_match_email', `kateg` = @iKategId, `desc` = 'On connect, match facebook account to existing<br />dolphin account if email address matches', `Type` = 'checkbox', `VALUE` = 'on', `order_in_kateg` = 14;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_set_status_active_oc', `kateg` = @iKategId, `desc` = 'On connect, set status on unconfirmed to active<br>on previously connected facebook accounts', `Type` = 'checkbox', `VALUE` = 'on', `order_in_kateg` = 15;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_use_geo_ip', `kateg` = @iKategId, `desc` = 'Use Geo IP to find country.<br />Otherwise use default country setting', `Type` = 'checkbox', `VALUE` = 'on', `order_in_kateg` = 16;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_dcnty', `kateg` = @iKategId, `desc` = 'Default country', `Type` = 'digit', `VALUE` = 'US', `order_in_kateg` = 17;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_use_join_form', `kateg` = @iKategId, `desc` = 'Send Facebook Info to Dolphin Join Form<br />Note: With this option on, all other options<br />below are ignored. When the dolphin join form<br />is used, facebook connect passes control of<br />the signup process back to dolphin', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 18;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_das', `kateg` = @iKategId, `desc` = 'Default active status', `Type` = 'select', `VALUE` = 'Active', `AvailableValues` = 'Use Dolphins Settings,Active,Unconfirmed,Approval', `order_in_kateg` = 19;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_default_membership', `kateg` = @iKategId, `desc` = 'Default membership', `Type` = 'select', `VALUE` = 'Dolphins Default', `AvailableValues` = 'Dolphins Default', `order_in_kateg` = 20;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_auto_prompt_nick', `kateg` = @iKategId, `desc` = 'Auto prompt for nickname if free<br />nick name was not found', `Type` = 'checkbox', `VALUE` = 'on', `order_in_kateg` = 21;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_auto_prompt_email', `kateg` = @iKategId, `desc` = 'Auto prompt for email if email<br />address from facebook is not valid', `Type` = 'checkbox', `VALUE` = 'on', `order_in_kateg` = 22;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_auto_prompt_dob', `kateg` = @iKategId, `desc` = 'Auto prompt for date of birth if date of birth<br />from facebook is not valid', `Type` = 'checkbox', `VALUE` = 'on', `order_in_kateg` = 23;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_prompt_profile_type', `kateg` = @iKategId, `desc` = 'Prompt for profile type', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 24;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_prompt_pass', `kateg` = @iKategId, `desc` = 'Prompt for a password on join', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 25;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_prompt_nick', `kateg` = @iKategId, `desc` = 'Prompt for a nickname on join', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 26;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_prompt_email', `kateg` = @iKategId, `desc` = 'Prompt for a email on join', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 27;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_prompt_sex', `kateg` = @iKategId, `desc` = 'Prompt for a Sex/Gender on join', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 28;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_prompt_dob', `kateg` = @iKategId, `desc` = 'Prompt for date of birth on join', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 29;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_prompt_country', `kateg` = @iKategId, `desc` = 'Prompt for country on join', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 30;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_prompt_city', `kateg` = @iKategId, `desc` = 'Prompt for city on join', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 31;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_prompt_zip', `kateg` = @iKategId, `desc` = 'Prompt for zip code on join', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 32;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_redirect1', `kateg` = @iKategId, `desc` = 'Redirect to page after join', `Type` = 'digit', `VALUE` = 'pedit.php?ID={memberid}', `order_in_kateg` = 33;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_autofriend_list', `kateg` = @iKategId, `desc` = 'Comma delimited list of friends to automatically<br />add to new account. (Leave empty to disable)', `Type` = 'digit', `VALUE` = '', `order_in_kateg` = 34;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_facebook_friends', `kateg` = @iKategId, `desc` = 'Auto friend any current members who are also<br />friends with new member on facebook', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 35;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_copy_photo', `kateg` = @iKategId, `desc` = 'Set main Facebook photo as profile photo', `Type` = 'checkbox', `VALUE` = 'on', `order_in_kateg` = 36;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_import_albums', `kateg` = @iKategId, `desc` = 'Import Facebook photo albums and photos', `Type` = 'checkbox', `VALUE` = '', `order_in_kateg` = 37;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_import_privacy', `kateg` = @iKategId, `desc` = 'Album import privacy options', `Type` = 'select', `VALUE` = 'Import all albums. Preserve privacy.', `AvailableValues` = 'Import all albums. Preserve privacy.,Import all albums. Force to public.,Import all albums. Force to members.,Import all albums. Force to me only.,Import all albums. Force to friends.,Import public albums only.,Import profile pictures only.', `order_in_kateg` = 38;
INSERT INTO `sys_options` SET `Name` = 'dbcs_facebook_connect_show_email', `kateg` = @iKategId, `desc` = 'Show Email Address as login ID instead of Nickname<br />on finish page and welcome email.', `Type` = 'checkbox', `VALUE` = '', `AvailableValues` = '', `order_in_kateg` = 39;


    --
    -- permalink
    --

    INSERT INTO 
        `sys_permalinks` 
    SET
        `standard`  = 'modules/?r=deanos_facebook_connect/', 
        `permalink` = 'm/deanos_facebook_connect/', 
        `check`     = 'dbcs_facebook_connect_permalinks';
        
    --
    -- settings
    --

    INSERT INTO 
        `sys_options` 
    (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) 
        VALUES
    ('dbcs_facebook_connect_permalinks', 'on', 26, 'Enable friendly permalinks in Deanos Facebook Connect', 'checkbox', '', '', '0', '');

    UPDATE `sys_profile_fields` SET `Max` = 500 WHERE `Name` = 'NickName' LIMIT 1;


    INSERT INTO 
	`sys_profile_fields`
    (`Name`, `Type`, `Control`, `Extra`, `Min`, `Max`, `Values`, `UseLKey`, `Check`, `Unique`, `Default`, `Mandatory`, `Deletable`, `JoinPage`, `JoinBlock`, `JoinOrder`, `EditOwnBlock`, `EditOwnOrder`, `EditAdmBlock`, `EditAdmOrder`, `EditModBlock`, `EditModOrder`, `ViewMembBlock`, `ViewMembOrder`, `ViewAdmBlock`, `ViewAdmOrder`, `ViewModBlock`, `ViewModOrder`, `ViewVisBlock`, `ViewVisOrder`, `SearchParams`, `SearchSimpleBlock`, `SearchSimpleOrder`, `SearchQuickBlock`, `SearchQuickOrder`, `SearchAdvBlock`, `SearchAdvOrder`, `MatchField`, `MatchPercent`)
	VALUES
    ('dbcsFacebookProfile', 'text', NULL, '', NULL, NULL, '', 'LKey', '', 0, '', 0, 1, 0, 17, 7, 17, 8, 17, 7, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, '', 0, NULL, 0, NULL, 0, NULL, 0, 0);

-- sys_injections
INSERT INTO `sys_injections` (`name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES
('profile_info_required', 1, 'injection_head', 'php', 'return BxDolService::call(''deanos_facebook_connect'', ''CheckProfile'');', 0, 1),
('dbcs_fbc_popup_code', 0, 'injection_header', 'php', 'return BxDolService::call(''deanos_facebook_connect'', ''get_fb_js_code'');', 0, 1),
('dbcs_fbc_button_large', 0, 'injection_fbc_button_large', 'php', 'return BxDolService::call(''deanos_facebook_connect'', ''get_button'', array (''large'', ''margin-top: 8px;'', true));', 0, 1),
('dbcs_fbc_button_large_nm', 0, 'dbcs_fbc_button_large_nm', 'php', 'return BxDolService::call(''deanos_facebook_connect'', ''get_button'', array (''large'', '''', true));', 0, 1),
('dbcs_fbc_button_small', 0, 'injection_fbc_button_small', 'php', 'return BxDolService::call(''deanos_facebook_connect'', ''get_button'', array (''small'', ''float:left; margin-left: 10px;'', false));', 0, 1),
('dbcs_fbc_button_small_mr', 0, 'dbcs_fbc_button_small_mr', 'php', 'return BxDolService::call(''deanos_facebook_connect'', ''get_button'', array (''small'', ''float:left; margin-right: 10px;'', false));', 0, 1),
('dbcs_fbc_button_small_nm', 0, 'dbcs_fbc_button_small_nm', 'php', 'return BxDolService::call(''deanos_facebook_connect'', ''get_button'', array (''small'', ''float:left;'', false));', 0, 1),
('dbcs_fbc_button_small_ns', 0, 'dbcs_fbc_button_small_ns', 'php', 'return BxDolService::call(''deanos_facebook_connect'', ''get_button'', array (''small'', '''', false));', 0, 1);


INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('t_dbcs_FaceBookJoined', 'Your profile has been activated.', '<html><head></head><body style="font: 12px Verdana; color:#000000">\r\n<p><b>Dear <RealName></b>,</p>\r\n\r\n<p>Your profile has been activated. Thank you for joining via Facebook Connect!</p>\r\n\r\n<p>Simply follow the link below to enjoy our services:<br /><a href="<Domain>member.php"><Domain>member.php</a></p>\r\n\r\n<p>Your identification number (ID): <span style="color:#FF6633"><recipientID></span></p>\r\n\r\n<p>Your e-mail used for registration: <span style="color:#FF6633"><Email></span></p><p>Your Logon ID is: <span style="color:#FF6633"><NickName></span></p><p>Your Password is: <span style="color:#FF6633"><NewPassword></span></p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>--</p>\r\n<p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!!\r\n<br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Facebook profile activation message template.', 0),
('t_dbcs_FaceBookUnconfirmed', 'Confirm your profile', '<html><head></head><body style="font: 12px Verdana; color:#000000">\r\n<p><b>Dear <RealName></b>,</p>\r\n\r\n<p>Thank you for registering at <SiteName> via Facebook Connect!</p>\r\n\r\n<p style="color:#3B5C8E">CONFIRMATION CODE: <ConfCode></p>\r\n\r\n<p>Or you can also simply follow the link below:\r\n<a href="<ConfirmationLink>"><ConfirmationLink></a></p>\r\n\r\n<p>This is necessary to complete your registration.<br />Without doing that you won''t be submitted to our database.</p>\r\n\r\n<p>Your identification number (ID): <span style="color:#FF6633; font-weight:bold;"><recipientID></span></p>\r\n\r\n<p>Your e-mail used for registration: \r\n<span style="color:#FF6633"><Email></span></p><p>Your Logon ID is: <span style="color:#FF6633"><NickName></span></p><p>Your Password is: <span style="color:#FF6633"><NewPassword></span></p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>--</p>\r\n<p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!!\r\n<br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Facebook Profile e-mail confirmation message template.', 0);

-- Turn of PHPIDS Security. Should be off by default now for dolphin 7.0.4 and up.
UPDATE IGNORE `sys_options` SET `VALUE` = '-1' WHERE `Name` = 'sys_security_impact_threshold_log';
UPDATE IGNORE `sys_options` SET `VALUE` = '-1' WHERE `Name` = 'sys_security_impact_threshold_block';
