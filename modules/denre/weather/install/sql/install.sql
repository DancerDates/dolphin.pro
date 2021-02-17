-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
    (2, 'db_weather', '_db_weather', '{siteUrl}modules/?r=weather/administration/', 'Local weather module by Denre','cloud', @iMax+1);

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES
    ('Weather Admin', @iMaxOrder);
SET @iCategId := LAST_INSERT_ID();
SET @sSupportedModules = (SELECT GROUP_CONCAT(`uri` SEPARATOR ', ') FROM (SELECT `m`.`uri` FROM `sys_modules` `m` RIGHT JOIN `bx_wmap_parts` `w` ON `m`.`uri` = `w`.`part` WHERE `m`.`id` IS NOT NULL ORDER BY `m`.`uri` ASC) AS `modules`);

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
    ('db_weather_locator', '', @iCategId, 'Use GEO Locator', 'checkbox', '', '', '0', ''),
    ('db_weather_city', '', @iCategId, 'Default City', 'digit', '', '', '1', ''),
    ('db_weather_country', '', @iCategId, 'Default Country (two letter country code)', 'digit', '', '', '2', ''),
    ('db_weather_units', 'metric', @iCategId, 'Unit', 'select', '', '', '3', 'metric,imperial'),
    ('db_weather_modules', '', @iCategId, 'Activate weather module for', 'list', '', '', '4', @sSupportedModules),
    ('db_weather_api_key', '', @iCategId, 'OpenWeather API Key', 'digit', '', '', '4', '');

-- page blocks
INSERT INTO `sys_page_compose` (`Page`,`PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
    ('index', '1140px', 'Local Weather', '_db_weather', '0', '0', 'PHP', 'return  BxDolService::call(''weather'', ''weather_block'');', '11', '0', 'non,memb', '0'),
    ('member', '1140px', 'Local Weather', '_db_weather', '0', '0', 'PHP', 'return  BxDolService::call(''weather'', ''weather_block'');', '11', '0', 'non,memb', '0'),
    ('profile', '1140px', 'Local Weather', '_db_weather', '0', '0', 'PHP', 'return  BxDolService::call(''weather'', ''weather_integration'', array(''NoUri'', $this->oProfileGen->_iProfileID));', '11', '0', 'non,memb', '0');


-- alerts
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'db_weather_install', '', '', 'if ($this->aExtras[''res''][''result'']) BxDolService::call(''weather'', ''module_install'', array($this->aExtras[''uri'']));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'db_weather_uninstall', '', '', 'if ($this->aExtras[''res''][''result'']) BxDolService::call(''weather'', ''module_uninstall'', array($this->aExtras[''uri'']));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'uninstall', @iHandler);


