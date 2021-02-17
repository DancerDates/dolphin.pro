-- tables
DROP TABLE IF EXISTS `[db_prefix]_profile_settings`;

-- injections
-- DELETE FROM `sys_injections` WHERE `name`='ams_head';
-- DELETE FROM `sys_injections` WHERE `name`='ams_foor';

-- options
DELETE FROM `sys_options` WHERE `Name` = 'ams_menu_number';
DELETE FROM `sys_options` WHERE `Name` = 'ams_en';

-- page blocks
DELETE FROM `sys_page_compose` WHERE `Caption`='_ams_CSS_menu';

-- actions
DELETE FROM `sys_objects_actions` WHERE `Type` = 'Profile' AND `Icon`='tasks';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'navigation_menu_ml';