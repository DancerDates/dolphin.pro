-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` VALUES(NULL, 'tm4', @iMaxOrder);
SET @iOptCateg = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('tm4_skin', 'lightgray', @iOptCateg, 'TinyMCE4 skin', 'select', 'return strlen($arg0) > 0;', 'cannot be empty.', NULL, 'lightgray,charcoal,pepper-grinder,tundora,xenmce');
UPDATE `sys_options` SET `VALUE` = 'sys_tinymce4' WHERE `sys_options`.`Name` = 'sys_editor_default' LIMIT 1;
-- `kateg` = @iOptCateg,

-- sys_objects_editor
INSERT INTO `sys_objects_editor` (`object`, `title`, `skin`, `override_class_name`, `override_class_file`) VALUES
('sys_tinymce4', 'TinyMCE4', 'lightgray', 'TM4EditorTinyMCE4', 'modules/andrew/tm4/classes/TM4EditorTinyMCE4.php');

-- admin menu
SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(NULL, 2, 'TM4', '_atm4_main', '{siteUrl}m/tm4/administration', 'TinyMCE4 administration', 'align-center', '', '', @iExtOrd+1);