DROP TABLE IF EXISTS `[db_prefix]handlers`, `[db_prefix]actions`, `[db_prefix]networks`, `[db_prefix]users`, `[db_prefix]settings`;

DELETE FROM `sys_permalinks` WHERE `check` IN ('sk_social_posting_permalinks');
DELETE FROM `sys_options` WHERE `Name` = 'sk_social_posting_permalinks';

SET @iHandlerId:= (SELECT `id` FROM `sys_alerts_handlers` WHERE `name`='sk_social_posting_content' LIMIT 1);
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandlerId;
DELETE FROM `sys_alerts` WHERE `handler_id`= @iHandlerId;

DELETE FROM `sys_menu_member` WHERE `Name` = 'sk_social_posting';

SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock Social posting' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;

DELETE FROM `sys_menu_top` WHERE `Name` = 'Posting Setting';

DELETE FROM `sys_menu_admin` WHERE `name` = 'Sandklock Social posting';

DELETE FROM `sys_injections` WHERE `name` = 'social_posting_inject';

-- page compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN
('sk_posting_setting');
DELETE FROM `sys_page_compose` WHERE `Page` IN
('sk_posting_setting');

SET @iAlertID := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'sk_social_posting_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts_handlers` WHERE `name` = 'sk_social_posting_profile_delete';
DELETE FROM `sys_alerts` WHERE `handler_id` = @iAlertID;