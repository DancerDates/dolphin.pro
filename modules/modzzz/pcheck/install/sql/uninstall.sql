-- tables
DROP TABLE IF EXISTS `[db_prefix]profile_fields`; 
DROP TABLE IF EXISTS `[db_prefix]cron`; 
 
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=pcheck/';
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_pcheck';
   
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'PCheck' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

DELETE FROM `sys_options` WHERE `Name` IN ('modzzz_pcheck_permalinks' );
 
DELETE FROM `sys_page_compose` WHERE `Caption` = '_modzzz_pcheck_block_profile_completeness';


-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_pcheck_no_photo','modzzz_pcheck_incomplete');
 

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_pcheck_profile_edit' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_pcheck_profile_join' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_pcheck_photo_add' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_pcheck_photo_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
-- cron_jobs
DELETE FROM `sys_cron_jobs` WHERE `name` IN ('modzzz_pcheck_cron','modzzz_pcheck_init_cron');
 

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('pcheck allow view');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('pcheck allow view');
