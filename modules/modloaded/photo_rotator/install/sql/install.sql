INSERT IGNORE INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('bx_photos_view', '998px', 'Photo''s rotator block', '_ml_photo_rotator_block_rotator', '2', '0', 'PHP', 'return BxDolService::call(''photo_rotator'', ''rotator'',array($this->aFileInfo[ID], $this->aFileInfo[Hash]));', '1', '33', 'non,memb', '0');
INSERT IGNORE  INTO `sys_permalinks` VALUES (NULL, 'modules/?r=photo_rotator/', 'm/photo_rotator/', 'ml_photo_rotator_permalinks');

--SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
--INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
--(2, 'ml_photo_zoom', '_ml_photo_zoom', '{siteUrl}modules/?r=photo_rotator/administration/', 'Photo Rotator by Modloaded', 'modules/modloaded/photo_zoom/|photo_zoom.png', @iMax+1);

