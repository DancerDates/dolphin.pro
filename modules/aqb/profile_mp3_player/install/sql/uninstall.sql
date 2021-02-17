SET @sPluginName = 'aqb_profile_mp3_player';

DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;

SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = @sPluginName);

DELETE FROM `sys_options` WHERE `kateg` = @iCategoryId;

DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryId;

ALTER TABLE `Profiles` DROP COLUMN `AqbMP3PlayerPlayAlbumID`;

ALTER TABLE `Profiles` DROP COLUMN `AqbMP3PlayerAutoplay`;

ALTER TABLE `Profiles` DROP COLUMN `AqbMP3PlayerOrder`;

DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Caption` = '_aqb_profile_mp3_player_block_caption';