SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Geo Distance', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('Dately_geodistance_switch', 'on', @iCategId, 'Module active', 'checkbox', '', '', '2', ''),
('Dately_geodistance_format', '~%.2f', @iCategId, 'Output format', 'digit', '', '', '1', ''),
('Dately_geodistance_use_miles', 'off', @iCategId, 'Use Miles instead of Kilometer?', 'checkbox', '', '', '2', ''),
('Dately_geodistance_use_unit', 'on', @iCategId, 'Add unit to output?', 'checkbox', '', '', '2', ''),
('Dately_geodistance_finetune', '0', @iCategId, 'Finetune earth radius in km (0 = auto)', 'digit', '', '', '1', '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'Dately_geodistance', '_Dately_geodistance', '{siteUrl}modules/?r=geodistance/administration/', 'Geodistance Administration', 'modules/Dately/geodistance/|icon.png', @iMax+1);
