-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) 
VALUES(2, 'MTools_Page_Editor', '_mchristiaan_mtools', '{siteUrl}modules/?r=mtools/administration/', 'MTools Page Editor by M.Christiaan', 'modules/mchristiaan/mtools/|icon.png', '', '', @iMax+1);

