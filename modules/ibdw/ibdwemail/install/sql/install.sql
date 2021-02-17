CREATE TABLE IF NOT EXISTS `ibdw_emailnotification` (
  `ID` int(11) NOT NULL auto_increment,
  `key_actions` varchar(200) collate utf8_unicode_ci NOT NULL,
  `key_title` varchar(200) collate utf8_unicode_ci NOT NULL,
  `lang_key` varchar(200) collate utf8_unicode_ci NOT NULL,
  `active` int(2) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM ;

INSERT INTO `ibdw_emailnotification` (`ID`, `key_actions`, `key_title`, `lang_key`, `active`) VALUES
(1, 'like_action', '_ibdw_emailnotify_like_title', '_ibdw_emailnotify_like', 1),
(2, 'share', '_ibdw_emailnotify_share_title', '_ibdw_emailnotify_share', 1),
(7, 'messaggiowall', '_ibdw_emailnotify_messagewall_title', '_ibdw_emailnotify_messagewall', 1),
(6, 'commento', '_ibdw_emailnotify_commento_title', '_ibdw_emailnotify_commento', 1),
(8, 'richiesta_amicizia', '_ibdw_emailnotify_richiesta_amicizia_title', '_ibdw_emailnotify_richiesta_amicizia', 1),
(9, 'tag_photodeluxe', '_ibdw_emailnotify_tagphoto', '_ibdw_emailnotify_tagphotoaction', 1),
(10, 'comment_photodeluxe', '_ibdw_emailnotify_photocomment', '_ibdw_emailnotify_photocommentaction', 1),
(11, 'like_photodeluxe', '_ibdw_emailnotify_photolike', '_ibdw_emailnotify_photolikeaction', 1);

INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES (NULL, '0', 'IBDWEmail config', 'IBDWEmail config', '{siteUrl}modules/ibdw/ibdwemail/configurazione.php', 'IBDWEmail config', 'modules/ibdw/ibdwemail/templates/|mail.png', '', '', '10');
