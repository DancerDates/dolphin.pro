
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Deano - Cron Monitor' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'deano_cron_monitor_permalinks';
DELETE FROM `sys_options` WHERE `Name` = 'deano_cron_monitor_install_date';
DELETE FROM `sys_options` WHERE `Name` = 'deano_cron_monitor_last_run';
DELETE FROM `sys_options` WHERE `Name` = 'deano_cron_monitor_last_run2';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=cron_monitor/';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'Cron Monitor';

-- ALTER TABLE `sys_cron_jobs` DROP `lastRun`;
-- ALTER TABLE `sys_cron_jobs` DROP `nextRun`;
-- ALTER TABLE `sys_cron_jobs` DROP `failCount`;

DELETE FROM `sys_cron_jobs` WHERE `name` = 'deano_cron_monitor';
