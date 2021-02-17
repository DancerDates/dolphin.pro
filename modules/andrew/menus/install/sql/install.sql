-- tables
CREATE TABLE `[db_prefix]_profile_settings` (
  `profile_id` int(11) unsigned NOT NULL default '0',
  `menu_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`profile_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- injections
-- INSERT INTO `sys_injections` (`id`, `name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES
-- (NULL, 'ams_head', 0, 'injection_between_logo_top_menu', 'php', 'return BxDolService::call("menus", "ams_vhead");', 0, 1),
-- (NULL, 'ams_foor', 0, 'injection_between_content_breadcrumb', 'php', 'return BxDolService::call("menus", "ams_vfoot");', 0, 1);

-- options
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES 
('ams_menu_number', '0', 13, 'Menu #', 'select', '', '', 0, 'PHP: return BxDolService::call("menus", "get_installed_menus");'),
('ams_en', '1', 0, '', 'digit', '', '', 0, '');

-- page blocks
INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'profile', '1140px', 'Menu settings', '_ams_CSS_menu', 1, 2, 'PHP', 'return BxDolService::call(''menus'', ''get_switcher_block'', array($this->oProfileGen->_iProfileID));', 0, 28.1, 'memb', 0);

-- action
SET @iMaxOrder = (SELECT `Order` + 1 FROM `sys_objects_actions` WHERE `Type` = 'Profile' ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
('{evalResult}', 'tasks', '', '$(''#ams_settings'').slideToggle(''slow'');', 'if ({ID} == {member_id} ) return _t( ''_ams_CSS_menu'' ); else return null;', @iMaxOrder, 'Profile', 0);

-- admin menu
SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `id`='14');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(14, 'navigation_menu_ml', '_ams_ml_navigation_menu', '{siteUrl}modules/?r=menus/builder/', 'Navigation menu manager from AndrewP (multilevels)', 'tasks', 'tasks', '', @iExtOrd+1);