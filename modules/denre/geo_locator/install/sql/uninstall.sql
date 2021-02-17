DELETE FROM `sys_page_compose` WHERE `Desc` = 'DB GEO';

DELETE FROM `sys_alerts` WHERE `handler_id` IN (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` like 'db_geo%');
DELETE FROM `sys_alerts_handlers` WHERE `name` like 'db_geo_%';

DELETE FROM `sys_options` WHERE `name` LIKE 'db_geo%';
DELETE FROM `sys_options_cats` WHERE `name` LIKE 'DB GEO%';

DELETE FROM `sys_menu_admin` WHERE  `name` = 'db_geo';

DROP TABLE IF EXISTS `db_geo_locations`;
DROP TABLE IF EXISTS `db_country_codes`;
DROP TABLE IF EXISTS `db_time_offset`;
DROP TABLE IF EXISTS `db_geo_includes`;
