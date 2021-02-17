SET @sPluginName = 'aqb_profile_photo_picker';

-- member info
INSERT INTO `sys_objects_member_info` (`object`, `title`, `type`, `override_class_name`, `override_class_file`) VALUES
('aqb_profile_photo_picker_thumb', '_aqb_ppp_member_info_profile_photo', 'thumb', 'AqbProfilePhotoPickerMemberInfo', 'modules/aqb/profile_photo_picker/classes/AqbProfilePhotoPickerMemberInfo.php'),
('aqb_profile_photo_picker_icon', '_aqb_ppp_member_info_profile_photo_icon', 'thumb_icon', 'AqbProfilePhotoPickerMemberInfo', 'modules/aqb/profile_photo_picker/classes/AqbProfilePhotoPickerMemberInfo.php');

-- thumb settings update
SET @sOldThumb = (SELECT `Value` FROM `sys_options` WHERE `Name` = 'sys_member_info_thumb' LIMIT 1);
INSERT INTO `sys_options` (`Name`, `Value`) VALUES ('aqb_profile_photo_picker_old_thumb', @sOldThumb);
UPDATE `sys_options` SET `Value` = 'aqb_profile_photo_picker_thumb' WHERE `Name` = 'sys_member_info_thumb' LIMIT 1;

-- icon settings update
SET @sOldIcon = (SELECT `Value` FROM `sys_options` WHERE `Name` = 'sys_member_info_thumb_icon' LIMIT 1);
INSERT INTO `sys_options` (`Name`, `Value`) VALUES ('aqb_profile_photo_picker_old_icon', @sOldIcon);
UPDATE `sys_options` SET `Value` = 'aqb_profile_photo_picker_icon' WHERE `Name` = 'sys_member_info_thumb_icon' LIMIT 1;

-- Profile action button
SET @iMOrder := (SELECT MAX(`Order`) FROM `sys_objects_actions` WHERE `Type`= 'Profile');
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`)
VALUES ('{evalResult}', 'user', '', 'aqb_profile_photo_picker_popup({ID}); return false;', 'return BxDolService::call(''aqb_profile_photo_picker'', ''get_profile_action_button'', array({ID}, {member_id}));', @iMOrder, 'Profile');

-- Profile Photo Field
ALTER TABLE `Profiles` ADD COLUMN `aqb_profile_photo_id` char(32) NOT NULL default '';

-- Profile Photo Block
INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache`)
SELECT NULL, `Page`, `PageWidth`, 'Profile Photo Block (by Profile Photo Picker)' AS `Desc`, `Caption`, `Column`, `Order`, `Func`, 'return BxDolService::call(''aqb_profile_photo_picker'', ''profile_photo_block'', array($this->oProfileGen->_iProfileID));' AS `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache` FROM  `sys_page_compose` WHERE `Desc` = 'Profile Photo Block' LIMIT 1;