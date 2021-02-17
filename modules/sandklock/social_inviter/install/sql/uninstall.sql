DROP TABLE IF EXISTS `sk_social_inviter_users`;
DROP TABLE IF EXISTS `sk_social_inviter_networks`;

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Sandklock Social Inviter' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `Name` = 'Sandklock Social Inviter';

DELETE FROM `sys_menu_admin` WHERE `name` = 'Sandklock Social Inviter';

DELETE FROM `sys_page_compose` WHERE `Page`='member' AND `Desc`='Social Inviter block' AND `Caption`='_sk_social_inviter_profile_block' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='index' AND `Desc`='Social Inviter block' AND `Caption`='_sk_social_inviter_profile_block' AND `Func`='PHP';

DELETE FROM `sys_permalinks` WHERE `check` IN ('sk_social_inviter_permalinks');
DELETE FROM `sys_options` WHERE `Name` = 'sk_social_inviter_permalinks';

SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock Social Inviter Setting' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;

DELETE FROM `sys_menu_admin` WHERE `name` = 'Sandklock Social Inviter';

DELETE FROM `sys_objects_actions` WHERE `Type` LIKE 'sk_social_inviter%';

DELETE FROM `sys_injections` WHERE `name` = 'social_inviter_popup';

DELETE FROM `sys_email_templates` WHERE `Name` = 'sk_social_inviter_email';