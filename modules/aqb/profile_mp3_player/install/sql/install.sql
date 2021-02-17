SET @sPluginName = 'aqb_profile_mp3_player';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = 2);
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_profile_mp3_player', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'For managing Profile MP3 Player module', 'music', '', '', @iOrder + 1);

INSERT INTO `sys_options_cats` SET `name` = @sPluginName;
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`)  VALUES
('aqb_profile_mp3_player_skin', 'blue.monday', @iCategoryId, 'Skin', 'select', 'return $arg0 == \'blue.monday\' || $arg0 == \'pink.flag\';', 'Not a valid option', 1, 'blue.monday,pink.flag'),
('aqb_profile_mp3_player_popup_width', '300', @iCategoryId, 'Popup window width', 'digit', 'return intval($arg0) >= 1;', 'Can not be 0', 2, ''),
('aqb_profile_mp3_player_popup_height', '200', @iCategoryId, 'Popup window height', 'digit', 'return intval($arg0) >= 1;', 'Can not be 0', 3, ''),
('aqb_profile_mp3_player_embed_width', '300', @iCategoryId, 'Embed window width', 'digit', 'return intval($arg0) >= 1;', 'Can not be 0', 4, ''),
('aqb_profile_mp3_player_embed_height', '100', @iCategoryId, 'Embed window height', 'digit', 'return intval($arg0) >= 1;', 'Can not be 0', 5, '');

ALTER TABLE `Profiles` ADD COLUMN `AqbMP3PlayerPlayAlbumID` INT DEFAULT 0;
ALTER TABLE `Profiles` ADD COLUMN `AqbMP3PlayerAutoPlay` TINYINT DEFAULT 0;
ALTER TABLE `Profiles` ADD COLUMN `AqbMP3PlayerOrder` TEXT;

SET @iOrder := (SELECT MIN(`Order`) FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Column` = 2);
SET @sPageWidth := (SELECT `PageWidth` FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Column` = 2 AND `Order` = @iOrder);
SET @fColWidth := (SELECT `ColWidth` FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Column` = 2 AND `Order` = @iOrder);

UPDATE `sys_page_compose` SET `Order` = `Order` + 1 WHERE `Page` = 'profile' AND `Column` = 1;

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('profile', @sPageWidth, 'Profile MP3 Player', '_aqb_profile_mp3_player_block_caption', 2, 0, 'PHP', 'return BxDolService::call(''aqb_profile_mp3_player'', ''get_profile_block'', array($this->oProfileGen->_iProfileID));', 1, @fColWidth, 'non,memb', 260);