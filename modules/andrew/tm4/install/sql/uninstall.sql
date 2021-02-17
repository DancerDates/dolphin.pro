-- settings
UPDATE `sys_options` SET `VALUE` = 'sys_tinymce' WHERE `sys_options`.`Name` = 'sys_editor_default' LIMIT 1;
-- `kateg` = 0,

SET @iOptCateg := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'tm4' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iOptCateg;
DELETE FROM `sys_options` WHERE `kateg` = @iOptCateg;

-- sys_objects_editor
DELETE FROM `sys_objects_editor` WHERE `object` = 'sys_tinymce4';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'TM4';