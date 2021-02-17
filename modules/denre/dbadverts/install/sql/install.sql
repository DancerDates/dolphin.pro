CREATE TABLE IF NOT EXISTS `db_adverts_advert` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Width` int(11) NOT NULL,
  `Fold` int(11) NOT NULL,
  `Page` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `Code` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `Active` tinyint(4) NOT NULL DEFAULT '0',
  `Created` date NOT NULL DEFAULT '0000-00-00',
  `campaign_start` date NOT NULL DEFAULT '2005-01-01',
  `campaign_end` date NOT NULL DEFAULT '2007-01-01',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0;

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
        (2, 'db_adverts', '_db_adverts', '{siteUrl}modules/?r=dbadverts/administration/', 'DbAdverts ', 'modules/denre/dbadverts/|icon.png', @iMax+1);

