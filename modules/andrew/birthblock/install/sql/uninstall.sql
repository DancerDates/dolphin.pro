-- page compose pages
DELETE FROM `sys_page_compose` WHERE `Caption`='_abb_main' AND `Func`='PHP';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'a_birth_block';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'a_birth_block' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;