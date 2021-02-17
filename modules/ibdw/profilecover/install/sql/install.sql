SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');

INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(NULL, 2, 'Profile Cover', 'Profile Cover', '{siteUrl}modules/?r=profilecover/administration/', 'Profile Cover - Settings', 'diamond', '', '', @iExtOrd+1);

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Profile Cover', @iMaxOrder);

SET @iKategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` SET   `Name` = 'KeyCode',   `kateg` = @iKategId,   `desc`  = 'License Key (Activation code)<br><a target="_blank" href="ibdw/profilecover/activation.php">Click here to get the code</a>',   `Type`  = 'digit',   `VALUE` = '',   `order_in_kateg` = 1;
INSERT INTO `sys_options` SET   `Name` = 'usedefaultCover',   `kateg` = @iKategId,   `desc`  = 'Use default Boonex cover album',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 2;
INSERT INTO `sys_options` SET   `Name` = 'AlbumCoverName',   `kateg` = @iKategId,   `desc`  = 'Name for the Profile Cover Album<br>This option is ignored if the previous is enabled',   `Type`  = 'digit',   `VALUE` = 'Cover',   `order_in_kateg` = 3;
INSERT INTO `sys_options` SET   `Name` = 'Friends',   `kateg` = @iKategId,   `desc`  = 'Display the friends link',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 4;
INSERT INTO `sys_options` SET   `Name` = 'Photos',   `kateg` = @iKategId,   `desc`  = 'Display the photos link',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 5;
INSERT INTO `sys_options` SET   `Name` = 'Sounds',   `kateg` = @iKategId,   `desc`  = 'Display the sounds link',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 6;
INSERT INTO `sys_options` SET   `Name` = 'Videos',   `kateg` = @iKategId,   `desc`  = 'Display the videos link',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 7;
INSERT INTO `sys_options` SET   `Name` = 'Groups',   `kateg` = @iKategId,   `desc`  = 'Display the groups link',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 8;
INSERT INTO `sys_options` SET   `Name` = 'groupsmod',   `kateg` = @iKategId,   `desc`  = 'Groups Module',   `Type`  = 'select',   `VALUE` = 'Boonex',   `order_in_kateg` = 9,   `AvailableValues` ='Boonex,Modzzz';
INSERT INTO `sys_options` SET   `Name` = 'Events',   `kateg` = @iKategId,   `desc`  = 'Display the events link',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 10;
INSERT INTO `sys_options` SET   `Name` = 'eventsmod',   `kateg` = @iKategId,   `desc`  = 'Events Module',   `Type`  = 'select',   `VALUE` = 'Boonex',   `order_in_kateg` = 11,   `AvailableValues` ='Boonex,Modzzz';
INSERT INTO `sys_options` SET   `Name` = 'Ads',   `kateg` = @iKategId,   `desc`  = 'Display the ads link',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 12;
INSERT INTO `sys_options` SET   `Name` = 'adsmod',   `kateg` = @iKategId,   `desc`  = 'Ads Module',   `Type`  = 'select',   `VALUE` = 'Boonex',   `order_in_kateg` = 13,   `AvailableValues` ='Boonex,Modzzz';
INSERT INTO `sys_options` SET   `Name` = 'Polls',   `kateg` = @iKategId,   `desc`  = 'Display the polls link',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 14;
INSERT INTO `sys_options` SET   `Name` = 'polls_mod',   `kateg` = @iKategId,   `desc`  = 'Polls Module',   `Type`  = 'select',   `VALUE` = 'Boonex',   `order_in_kateg` = 15,   `AvailableValues` ='Boonex,Modzzz';
INSERT INTO `sys_options` SET   `Name` = 'Sites',   `kateg` = @iKategId,   `desc`  = 'Display the sites link',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 16;
INSERT INTO `sys_options` SET   `Name` = 'Blogs',   `kateg` = @iKategId,   `desc`  = 'Display the blogs link',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 17;
INSERT INTO `sys_options` SET   `Name` = 'blogsmod',   `kateg` = @iKategId,   `desc`  = 'Blogs Module',   `Type`  = 'select',   `VALUE` = 'Boonex',   `order_in_kateg` = 18,   `AvailableValues` ='Boonex,Modzzz';
INSERT INTO `sys_options` SET   `Name` = 'Pages',   `kateg` = @iKategId,   `desc`  = 'Display the pages link',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 19;
INSERT INTO `sys_options` SET   `Name` = 'pagesmod',   `kateg` = @iKategId,   `desc`  = 'Pages Module',   `Type`  = 'select',   `VALUE` = 'Zarcon',   `order_in_kateg` = 20,   `AvailableValues` ='Zarcon,AntonLV,Modzzz';
INSERT INTO `sys_options` SET   `Name` = 'Displayheadline',   `kateg` = @iKategId,   `desc`  = 'Display User Status Message',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 21;
INSERT INTO `sys_options` SET   `Name` = 'maxfilesize',   `kateg` = @iKategId,   `desc`  = 'Max image size (MB)',   `Type`  = 'digit',   `VALUE` = '1',   `order_in_kateg` = 22;
INSERT INTO `sys_options` SET   `Name` = 'profileimaget',   `kateg` = @iKategId,   `desc`  = 'Image to display',   `Type`  = 'select',   `VALUE` = 'Picture',   `order_in_kateg` = 23,   `AvailableValues` ='Picture,Thumbnail';
INSERT INTO `sys_options` SET   `Name` = 'xyfactor',   `kateg` = @iKategId,   `desc`  = 'Aspect Ration (X/Y)',   `Type`  = 'digit',   `VALUE` = '0.35',   `order_in_kateg` = 24;
INSERT INTO `sys_options` SET   `Name` = 'EnableEditProfilePictureLink',   `kateg` = @iKategId,   `desc`  = 'Enable link to edit the profile image',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 25;


CREATE TABLE IF NOT EXISTS `profilecover_code_reminder` (`id` INT NOT NULL PRIMARY KEY ,`addressr` VARCHAR( 100 ) NOT NULL,`website` VARCHAR( 200 ) NOT NULL) ENGINE = MYISAM;

INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`) VALUES (NULL , 'profile', '', 'The Profile Page Cover', '_ibdw_profilecover_modulename', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/profilecover/cover.php'');', '0', '0', 'memb', '0');

CREATE TABLE IF NOT EXISTS `ibdw_profile_cover` (`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,`Owner` INT( 11 ) NOT NULL ,`Hash` VARCHAR( 32 ) NOT NULL ,`PositionY` smallint(6) NOT NULL DEFAULT '0' , `PositionX` smallint(6) NOT NULL DEFAULT '0', `width` smallint(6)) ENGINE=MyISAM;

INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES ( 'profilecover', '0 0 * * *', 'profilecoverCron', 'modules/ibdw/profilecover/classes/profilecoverCron.php', '') ;