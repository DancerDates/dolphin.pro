DROP TABLE IF EXISTS `sk_social_login_users`;
DROP TABLE IF EXISTS `sk_social_login_networks`;

DELETE FROM `sys_page_compose` WHERE `Page`='index' AND `Desc`='Login By Sandklock Social Login' AND `Caption`='_sk_social_login_block_index' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='join' AND `Desc`='Login By Sandklock Social Login' AND `Caption`='_sk_social_login_block_join' AND `Func`='PHP';
DELETE FROM `sys_page_compose` WHERE `Page`='member' AND `Desc`='Social Login Block' AND `Caption`='_sk_social_login_block_member' AND `Func`='PHP';

DELETE FROM `sys_permalinks` WHERE `check` IN ('sk_social_login_permalinks');
DELETE FROM `sys_options` WHERE `Name` = 'sk_social_login_permalinks';

SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock Social Login Setting' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;

DELETE FROM `sys_email_templates` WHERE `Name` = 'sk_social_login_information';

DELETE FROM `sys_injections` WHERE `name` = 'social_login_popup';

DELETE FROM `sys_menu_admin` WHERE `name` = 'Sandklock Social Login';

SET @iAlertID := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'sk_social_login_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts_handlers` WHERE `name` = 'sk_social_login_profile_delete';
DELETE FROM `sys_alerts` WHERE `handler_id` = @iAlertID;
