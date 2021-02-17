-- page compose pages
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('index', '1140px', 'Birthday block', '_abb_main', 2, 1, 'PHP', 'return BxDolService::call(''birthblock'', ''index_block'', array());', 1, 28.1, 'non,memb', 0);

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'a_birth_block', '_abb_main', '{siteUrl}m/birthblock/administration/', 'Upcoming Birthdays module by AndrewP', 'bolt', @iMax+1);

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('a_birth_block', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('abb_amount', '3', @iCategId, 'Amount of days for block', 'digit', '', '', '0', '');