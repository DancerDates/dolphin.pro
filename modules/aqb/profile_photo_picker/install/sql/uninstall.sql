SET @sPluginName = 'aqb_profile_photo_picker';

-- member info
DELETE FROM `sys_objects_member_info` WHERE `object` = 'aqb_profile_photo_picker_thumb' OR `object` = 'aqb_profile_photo_picker_icon';

-- thumb settings restore
SET @sOldThumb = (SELECT `Value` FROM `sys_options` WHERE `Name` = 'aqb_profile_photo_picker_old_thumb' LIMIT 1);
DELETE FROM `sys_options` WHERE `Name` = 'aqb_profile_photo_picker_old_thumb' LIMIT 1;
SET @sOldThumb = (IFNULL(@sOldThumb, (SELECT `Value` FROM `sys_options` WHERE `Name` = 'sys_member_info_thumb' LIMIT 1)));
UPDATE `sys_options` SET `Value` = @sOldThumb WHERE `Name` = 'sys_member_info_thumb' LIMIT 1;

-- icon settings restore
SET @sOldIcon = (SELECT `Value` FROM `sys_options` WHERE `Name` = 'aqb_profile_photo_picker_old_icon' LIMIT 1);
DELETE FROM `sys_options` WHERE `Name` = 'aqb_profile_photo_picker_old_icon' LIMIT 1;
SET @sOldIcon = (IFNULL(@sOldIcon, (SELECT `Value` FROM `sys_options` WHERE `Name` = 'sys_member_info_thumb_icon' LIMIT 1)));
UPDATE `sys_options` SET `Value` = @sOldIcon WHERE `Name` = 'sys_member_info_thumb_icon' LIMIT 1;

-- Profile action button
DELETE FROM `sys_objects_actions` WHERE `Type` = 'Profile' AND `Eval` LIKE '%aqb_profile_photo_picker%';

-- Profile Photo Field
ALTER TABLE `Profiles` DROP COLUMN `aqb_profile_photo_id`;

-- Profile Photo Block
DELETE FROM `sys_page_compose` WHERE `Desc` = 'Profile Photo Block (by Profile Photo Picker)';