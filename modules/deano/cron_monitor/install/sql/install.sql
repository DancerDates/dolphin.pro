
-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Deano - Cron Monitor', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('deano_cron_monitor_permalinks', 'on', 26, 'Enable friendly permalinks in Cron Monitor', 'checkbox', '', '', '0', ''),
('deano_cron_monitor_install_date', '0', 0, 'Date Installed', 'digit', '', '', '0', ''),
('deano_cron_monitor_last_run', '0', 0, 'Last Cron Run', 'digit', '', '', '0', ''),
('deano_cron_monitor_last_run2', '0', 0, 'Last Cron Run', 'digit', '', '', '0', ''),
('deano_cron_monitor_date_format', 'm-d-Y, h:i A', @iCategId, 'Date and Time Format', 'digit', '', '', '1', '');

-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=cron_monitor/', 'm/cron_monitor/', 'deano_cron_monitor_permalinks');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'Cron Monitor', '_deano_cron_monitor', '{siteUrl}modules/?r=cron_monitor/administration/', 'Cron Monitor by Deano', 'modules/deano/cron_monitor/|icon.png', @iMax+1);

-- ALTER TABLE `sys_cron_jobs` ADD `lastRun` INT NOT NULL DEFAULT '0';
-- ALTER TABLE `sys_cron_jobs` ADD `nextRun` INT NOT NULL DEFAULT '0';
-- ALTER TABLE `sys_cron_jobs` ADD `failCount` INT NOT NULL DEFAULT '0';

-- Cron Jobs
INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('deano_cron_monitor', '* * * * *', 'deanoCronMonitorCron', 'modules/deano/cron_monitor/classes/deanoCronMonitorCron.php', '');
