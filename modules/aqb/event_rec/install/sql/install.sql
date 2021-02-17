SET @sPluginName = 'aqb_event_rec';

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, @sPluginName, '_aqb_eventrec_menu', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/administration/'), 'Recurring Events extension from AQB Soft', 'modules/aqb/event_rec/|rec_icon.png', @iMax+1);

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES (@sPluginName, @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
(CONCAT(@sPluginName, '_enable_rec_option'), 'on', @iCategId, 'Enable recurring panel', 'checkbox', '', '', 1),
(CONCAT(@sPluginName, '_enable_rec_clean_part'), 'on', @iCategId, 'Remove participants before an event re-occurs', 'checkbox', '', '', 2);

CREATE TABLE IF NOT EXISTS`[db_prefix]settings` (
  `entry_id` int(10) unsigned NOT NULL,
  `start` varchar(10) NOT NULL default '',
  `use_standard` enum('1', '0') NOT NULL default '0',
  `end` varchar(10) NOT NULL default '',
  `duration` varchar(10) NOT NULL default '',
  `repeat` tinyint(1) unsigned NOT NULL default 1,
  `repeat_week_days` varchar(20) NOT NULL default '',  
  `every_week_number` int(10) unsigned NOT NULL default 0,
  `date_start` date default NULL,
  `date_end` date default NULL,
  `range_date` tinyint(1) unsigned NOT NULL default 1,
  `occurrence` int(10) unsigned NOT NULL default 0,
  `performed_occurrence` int(10) unsigned NOT NULL default 0,
  PRIMARY KEY (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'aqb_event_rec', 'AqbEventRecAlertsResponse', 'modules/aqb/event_rec/classes/AqbEventRecAlertsResponse.php', '');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES
(NULL, 'bx_events', 'add', @iHandler),
(NULL, 'bx_events', 'change', @iHandler),
(NULL, 'bx_events', 'delete', @iHandler);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
('bx_events_view', '1140px', 'Event''s recurring information block', '_aqb_eventrec_info_block', '3', '1', 'PHP', 'return BxDolService::call(''aqb_event_rec'', ''get_recurring_block'', array($this->aDataEntry[$this->_oDb->_sFieldId]));', '1', '28.1', 'non,memb', '0');

INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('aqb_events_res_updates', '*/15 * * * *', 'AqbEventRecCron', 'modules/aqb/event_rec/classes/AqbEventRecCron.php', '');