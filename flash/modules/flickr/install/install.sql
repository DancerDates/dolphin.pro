CREATE TABLE IF NOT EXISTS `[module_db_prefix]Elements` (
`ID` BIGINT NOT NULL AUTO_INCREMENT ,
`User` VARCHAR(20) DEFAULT '' NOT NULL,
`Title` VARCHAR(255) DEFAULT '' NOT NULL,
`Desc` TEXT DEFAULT '' NOT NULL,
`Param` ENUM('tag','user') DEFAULT 'tag' NOT NULL ,
`Value` VARCHAR( 255 ) NOT NULL DEFAULT '',
`PhotoID` varchar(20) NOT NULL default '',
`Thumb` varchar(255) NOT NULL default '',
`Play` varchar(255) NOT NULL default '',
`Save` varchar(255) NOT NULL default '',
`Author` varchar(50) NOT NULL default 'Unknown',
`Tags` TEXT DEFAULT '' NOT NULL,
`Date` INT(10) DEFAULT 0 NOT NULL,
`Public` ENUM('true','false') DEFAULT 'true' NOT NULL ,
`Rating` VARCHAR(4) DEFAULT '0.0' NOT NULL,
`Voted` INT(11) DEFAULT 0 NOT NULL,
`Votes` TEXT NOT NULL DEFAULT '',
`Views` BIGINT DEFAULT 0 NOT NULL,
`Category` INT(4) DEFAULT 0 NOT NULL,
`Order` INT NOT NULL DEFAULT 0,
`Parent` BIGINT DEFAULT 0 NOT NULL ,
PRIMARY KEY (`ID`)
);

TRUNCATE TABLE `[module_db_prefix]Elements`;

INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Adventure', 'Adventure', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Animals', 'Animals', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Art', 'Art', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Autos', 'Auto', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Autumn', 'Autumn', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Beaches', 'Beach', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Bridges', 'Bridge', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Buildings', 'Building', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Caves', 'Cave', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Cities', 'City', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Desert', 'Desert', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Flowers', 'Flower', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Forest/Woodland', 'Forest', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Lakes', 'Lake', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Mountains', 'Mountain', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Oceans', 'Ocean', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Rivers', 'River', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Monuments', 'Monument', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Space', 'Space', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Sports', 'Sport', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Sunrise/Sunset', 'Sunrise Sunset', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Technology', 'Technology', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Tropical', 'Tropical', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Waterfalls', 'Waterfall', 1);
INSERT INTO `[module_db_prefix]Elements`(`Title`, `Value`, `Category`) VALUES('Wintry Landscape', 'Winter', 1);

UPDATE `[module_db_prefix]Elements` SET `Order`=`ID`;

CREATE TABLE IF NOT EXISTS `[module_db_prefix]Favorites` (
`ID` BIGINT NOT NULL AUTO_INCREMENT ,
`User` VARCHAR(20) DEFAULT '' NOT NULL,
`Element` BIGINT NOT NULL default '0',
PRIMARY KEY ( `ID` )
);