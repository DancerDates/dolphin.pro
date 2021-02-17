SET @sPluginName = 'aqb_pfprivacy';


-- tables
DROP TABLE IF EXISTS `[db_prefix]entries`;


-- options
SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name`=@sPluginName LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `name`=@sPluginName LIMIT 1;
DELETE FROM `sys_options` WHERE `kateg`=@iCategoryId OR `Name` IN (CONCAT('permalinks_module_', @sPluginName));


-- pages & blocks
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN (CONCAT(@sPluginName, '_home'));
DELETE FROM `sys_page_compose` WHERE `Page` IN (CONCAT(@sPluginName, '_home')) OR (`Page` = 'pedit' AND `Caption` = '_aqb_pfprivacy_block_privacy_edit');


-- menus & links
DELETE FROM `sys_menu_top` WHERE `Name` IN(CONCAT(@sPluginName, '_owner'));

DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;

DELETE FROM `sys_permalinks` WHERE `check`=CONCAT('permalinks_module_', @sPluginName);


-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = @sPluginName;