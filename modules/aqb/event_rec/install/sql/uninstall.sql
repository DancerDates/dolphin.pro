SET @sPluginName = 'aqb_event_rec';

DELETE FROM `sys_menu_admin` WHERE `name` = @sPluginName;

SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = @sPluginName LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;

DROP TABLE IF EXISTS `[db_prefix]settings`;

DELETE  `sys_alerts_handlers`,`sys_alerts` 
FROM `sys_alerts_handlers`,`sys_alerts` 
WHERE `sys_alerts`.`handler_id` = `sys_alerts_handlers`.`id`  AND `sys_alerts_handlers`.`name` = 'aqb_event_rec';

DELETE FROM `sys_page_compose` WHERE `Page` = 'bx_events_view'  AND `Caption` = '_aqb_eventrec_info_block';

DELETE FROM `sys_cron_jobs` WHERE `name` = 'aqb_events_res_updates';