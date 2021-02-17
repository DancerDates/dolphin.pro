-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Geo Distance' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'Dately_geodistance';
