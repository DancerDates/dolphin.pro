DELETE FROM `sys_menu_admin` WHERE `name` = 'db_weather';

DELETE FROM `sys_options_cats` WHERE `name` = 'Weather Admin' LIMIT 1;
DELETE FROM `sys_options` WHERE `Name` LIKE "db_weather_%";

DELETE FROM `sys_page_compose` WHERE `Caption` = '_db_weather';

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'db_weather_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'db_weather_uninstall' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

