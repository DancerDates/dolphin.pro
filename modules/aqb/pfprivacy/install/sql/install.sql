SET @sPluginName = 'aqb_pfprivacy';


-- tables
CREATE TABLE `[db_prefix]entries` (
  `user_id` int(11) unsigned NOT NULL default '0',
  `field_id` int(11) unsigned NOT NULL default '0',
  `allow_view_to` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`, `field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- options
SET @iCategoryOrder = (SELECT MAX(`menu_order`) FROM `sys_options_cats`) + 1;
INSERT INTO `sys_options_cats`(`name` , `menu_order` ) VALUES (@sPluginName, @iCategoryOrder);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for My Guests module', 'checkbox', '', '', 0, ''),
('aqb_pfprivacy_via_pedit', 'on', @iCategoryId, 'Show profile fields privacy manager on Profile Edit page', 'checkbox', '', '', 1, '');


-- pages & blocks
SET @iPCPOrder = (SELECT IFNULL(MAX(`Order`), 0) FROM `sys_page_compose_pages`);
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES
(CONCAT(@sPluginName, '_home'), 'Profile Fields Privacy', @iPCPOrder + 1);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(CONCAT(@sPluginName, '_home'), '1140px', 'Profile Fields Privacy', '_aqb_pfprivacy_block_privacy_edit', 1, 1, 'Edit', '', 1, 100, 'memb', 0);

SET @sPage ='pedit';
SET @iPageColumn = 1;
SET @iPageMaxOrder = (SELECT IFNULL(MAX(`Order`), 0) FROM `sys_page_compose` WHERE `Page` = @sPage AND `Column` = @iPageColumn LIMIT 1);
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(@sPage, '1140px', 'Profile Fields Privacy', '_aqb_pfprivacy_block_privacy_edit', @iPageColumn, @iPageMaxOrder + 1, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''aqb_pfprivacy'', ''get_block_privacy_edit'');', 13, 71.9, 'memb', 0);


-- menus & links
SET @iTMParent = 4;
SET @iTMOrderOwner = (SELECT IFNULL(`Order`, 0) FROM `sys_menu_top` WHERE `Parent` = @iTMParent AND `Name`='Profile Info' LIMIT 1);
INSERT INTO `sys_menu_top`(`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(@iTMParent, CONCAT(@sPluginName, '_owner'), '_aqb_pfprivacy_tm_home', CONCAT('modules/?r=', @sPluginName, '/home/|modules/?r=', @sPluginName, '/'), @iTMOrderOwner+1, 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');


SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_pfprivacy_am_item', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'For managing Profile Fields Privacy', 'lock', '', '', @iOrder+1);


INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));


-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
(@sPluginName, 'view', '_aqb_pfprivacy_privacy_view', '3');