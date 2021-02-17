DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=photo_rotator/';
-- admin menu
--DELETE FROM `sys_menu_admin` WHERE `name` = 'ml_photo_zoom';

DELETE FROM `sys_page_compose` WHERE `Caption` = '_bx_photos_block_rotator';
DELETE FROM `sys_page_compose` WHERE `Caption` = '_ml_photo_rotator_block_rotator';
