ALTER TABLE `sys_menu_top` CHANGE `Link` `Link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE `sys_objects_actions` CHANGE `Type` `Type` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE `sys_stat_member` CHANGE `Type` `Type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 
 

-- create tables 
CREATE TABLE IF NOT EXISTS `[db_prefix]youtube` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]claim` (
  `ID` int(10) unsigned NOT NULL auto_increment, 
  `listing_id` int(11) NOT NULL, 
  `member_id` int(11) NOT NULL,
  `message` text NOT NULL, 
  `claim_date` int(11) NOT NULL default '0',
  `assign_date` int(11) NOT NULL default '0',
  `processed` int(11) NOT NULL default '0', 
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]activity` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `school_id` int(11) NOT NULL,
  `lang_key` varchar(100) collate utf8_unicode_ci NOT NULL,
  `params` text collate utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` enum('add','delete','change','commentPost','rate','join','unjoin','commentPost','featured','unfeatured','makeAdmin','removeAdmin') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `school_id` (`school_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,  
  `motto` varchar(255) NOT NULL,
  `president` varchar(255) NOT NULL,
  `affiliations` varchar(255) NOT NULL,
  `mascot` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `colors` varchar(255) NOT NULL, 
  `enrolled_students` int(11) NOT NULL,
  `academic_staff_count` varchar(30) NOT NULL, 
  `admin_staff_count` varchar(30) NOT NULL, 
  `year_established` int(11) NOT NULL,
  `founder` varchar(100) NOT NULL,  
  `country` varchar(2) NOT NULL,
  `city` varchar(64) NOT NULL,
  `street` varchar(150) NOT NULL,
  `zip` varchar(16) NOT NULL, 
  `fax` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `website` varchar(1100) NOT NULL,  
  `status` enum('approved','pending') NOT NULL default 'approved',
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `fans_count` int(11) NOT NULL,
  `student_count` varchar(30) NOT NULL, 
  `alumni_count` int(11) NOT NULL,
  `events_count` int(11) NOT NULL,
  `news_count` int(11) NOT NULL,  
  `featured` tinyint(4) NOT NULL,
  `allow_view_school_to` int(11) NOT NULL,
  `allow_view_fans_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL,  
  `allow_post_in_forum_to` varchar(16) NOT NULL,
  `allow_join_to` int(11) NOT NULL,
  `join_confirmation` tinyint(4) NOT NULL default '0',
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  `state` varchar(10) NOT NULL default '',  
  `ethnicity` varchar(100) NOT NULL, 
  `school_level` int(11) NOT NULL,
  `school_type` int(11) NOT NULL,
  `school_sports` text NOT NULL,
  `school_clubs` text NOT NULL,
  `school_qualifications` text NOT NULL, 
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`),
  FULLTEXT KEY `search` (`title`,`desc`,`tags`,`categories`),
  FULLTEXT KEY `tags` (`tags`),
  FULLTEXT KEY `categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

 
CREATE TABLE IF NOT EXISTS `[db_prefix]fans` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  `confirmed` tinyint(4) unsigned NOT NULL default '0',
  `year_entered` int(11) NOT NULL,
  `year_left` int(11) NOT NULL, 
  `membership_type` enum('student','alumni','fan') NOT NULL default 'fan',  
  PRIMARY KEY  (`id_entry`,`id_profile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]admins` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]views_track` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- create forum tables

CREATE TABLE `[db_prefix]forum` (
  `forum_id` int(10) unsigned NOT NULL auto_increment,
  `forum_uri` varchar(255) NOT NULL default '',
  `cat_id` int(11) NOT NULL default '0',
  `forum_title` varchar(255) default NULL,
  `forum_desc` varchar(255) NOT NULL default '',
  `forum_posts` int(11) NOT NULL default '0',
  `forum_topics` int(11) NOT NULL default '0',
  `forum_last` int(11) NOT NULL default '0',
  `forum_type` enum('public','private') NOT NULL default 'public',
  `forum_order` int(11) NOT NULL default '0',
  `entry_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `cat_id` (`cat_id`),
  KEY `forum_uri` (`forum_uri`),
  KEY `entry_id` (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE `[db_prefix]forum_cat` (
  `cat_id` int(10) unsigned NOT NULL auto_increment,
  `cat_uri` varchar(255) NOT NULL default '',
  `cat_name` varchar(255) default NULL,
  `cat_icon` varchar(32) NOT NULL default '',
  `cat_order` float NOT NULL default '0',
  `cat_expanded` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`cat_id`),
  KEY `cat_order` (`cat_order`),
  KEY `cat_uri` (`cat_uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]forum_cat` (`cat_id`, `cat_uri`, `cat_name`, `cat_icon`, `cat_order`) VALUES 
(1, 'Schools', 'Schools', '', 64);

CREATE TABLE `[db_prefix]forum_flag` (
  `user` varchar(32) NOT NULL default '',
  `topic_id` int(11) NOT NULL default '0',
  `when` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE `[db_prefix]forum_post` (
  `post_id` int(10) unsigned NOT NULL auto_increment,
  `topic_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `user` varchar(32) NOT NULL default '0',
  `post_text` mediumtext NOT NULL,
  `when` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `reports` int(11) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `user` (`user`),
  KEY `when` (`when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE `[db_prefix]forum_topic` (
  `topic_id` int(10) unsigned NOT NULL auto_increment,
  `topic_uri` varchar(255) NOT NULL default '',
  `forum_id` int(11) NOT NULL default '0',
  `topic_title` varchar(255) NOT NULL default '',
  `when` int(11) NOT NULL default '0',
  `topic_posts` int(11) NOT NULL default '0',
  `first_post_user` varchar(32) NOT NULL default '0',
  `first_post_when` int(11) NOT NULL default '0',
  `last_post_user` varchar(32) NOT NULL default '',
  `last_post_when` int(11) NOT NULL default '0',
  `topic_sticky` int(11) NOT NULL default '0',
  `topic_locked` tinyint(4) NOT NULL default '0',
  `topic_hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `forum_id_2` (`forum_id`,`when`),
  KEY `topic_uri` (`topic_uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user` (
  `user_name` varchar(32) NOT NULL default '',
  `user_pwd` varchar(32) NOT NULL default '',
  `user_email` varchar(128) NOT NULL default '',
  `user_join_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE `[db_prefix]forum_user_activity` (
  `user` varchar(32) NOT NULL default '',
  `act_current` int(11) NOT NULL default '0',
  `act_last` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user_stat` (
  `user` varchar(32) NOT NULL default '',
  `posts` int(11) NOT NULL default '0',
  `user_last_post` int(11) NOT NULL default '0',
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE `[db_prefix]forum_vote` (
  `user_name` varchar(32) NOT NULL default '',
  `post_id` int(11) NOT NULL default '0',
  `vote_when` int(11) NOT NULL default '0',
  `vote_point` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`user_name`,`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 

CREATE TABLE `[db_prefix]forum_actions_log` (
  `user_name` varchar(32) NOT NULL default '',
  `id` int(11) NOT NULL default '0',
  `action_name` varchar(32) NOT NULL default '',
  `action_when` int(11) NOT NULL default '0',
  KEY `action_when` (`action_when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_attachments` (
  `att_hash` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `att_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `att_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `att_when` int(11) NOT NULL,
  `att_size` int(11) NOT NULL,
  `att_downloads` int(11) NOT NULL,
  PRIMARY KEY (`att_hash`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]forum_signatures` (
  `user` varchar(32) NOT NULL,
  `signature` varchar(255) NOT NULL,
  `when` int(11) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
 

-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_view', 'School View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_main', 'Schools Home', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_my', 'Schools My', @iMaxOrder+4);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_local', 'Local Schools Page', @iMaxOrder+5); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_local_state', 'Local Schools State Page', @iMaxOrder+6); 

 

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
	
	('modzzz_schools_view', '1140px', 'School''s description block', '_modzzz_schools_block_desc', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
	('modzzz_schools_view', '1140px', 'School''s students block', '_modzzz_schools_block_students', '2', '2', 'Students', '', '1', '71.9', 'non,memb', '0'),    
	('modzzz_schools_view', '1140px', 'School''s alumni block', '_modzzz_schools_block_alumni', '2', '3', 'Alumni', '', '1', '71.9', 'non,memb', '0'),    
	('modzzz_schools_view', '1140px', 'School''s fans block', '_modzzz_schools_block_fans', '2', '4', 'Fans', '', '1', '71.9', 'non,memb', '0'),    
 	('modzzz_schools_view', '1140px', 'School''s photo block', '_modzzz_schools_block_photo', '2', '6', 'Photo', '', '1', '71.9', 'non,memb', '0'),
	('modzzz_schools_view', '1140px', 'School''s video embed block', '_modzzz_schools_block_video_embed', '2', '7', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'),  
	('modzzz_schools_view', '1140px', 'School''s videos block', '_modzzz_schools_block_video', '2', '8', 'Video', '', '1', '71.9', 'non,memb', '0'),    
	('modzzz_schools_view', '1140px', 'School''s sounds block', '_modzzz_schools_block_sound', '2', '9', 'Sound', '', '1', '71.9', 'non,memb', '0'),    
	('modzzz_schools_view', '1140px', 'School''s files block', '_modzzz_schools_block_files', '2', '10', 'Files', '', '1', '71.9', 'non,memb', '0'),    
	('modzzz_schools_view', '1140px', 'School''s local block', '_modzzz_schools_block_local', '2', '11', 'Local', '', '1', '71.9', 'non,memb', '0'),
 	('modzzz_schools_view', '1140px', 'School''s other block', '_modzzz_schools_block_other', '2', '12', 'Other', '', '1', '71.9', 'non,memb', '0'),
	('modzzz_schools_view', '1140px', 'School''s comments block', '_modzzz_schools_block_comments', '2', '13', 'Comments', '', '1', '71.9', 'non,memb', '0'),
	('modzzz_schools_view', '1140px', 'School''s forum block', '_modzzz_schools_block_forum', '2', '14', 'Forum', '', '1', '71.9', 'non,memb', '0'), 

	('modzzz_schools_view', '1140px', 'School''s actions block', '_modzzz_schools_block_actions', '3', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),    
	('modzzz_schools_view', '1140px', 'School''s rate block', '_modzzz_schools_block_rate', '3', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
	('modzzz_schools_view', '1140px', 'School''s info block', '_modzzz_schools_block_info', '3', '2', 'Info', '', '1', '28.1', 'non,memb', '0'),
	('modzzz_schools_view', '1140px', 'School''s statistics block', '_modzzz_schools_block_statistics', '3', '3', 'Statistics', '', '1', '28.1', 'non,memb', '0'),  
	('modzzz_schools_view', '1140px', 'School''s details block', '_modzzz_schools_block_details', '3', '4', 'Details', '', '1', '28.1', 'non,memb', '0'),
	('modzzz_schools_view', '1140px', 'School''s contact block', '_modzzz_schools_block_contact', '3', '5', 'Contact', '', '1', '28.1', 'non,memb', '0'), 
	('modzzz_schools_view', '1140px', 'School''s location address', '_modzzz_schools_block_location', '3', '6', 'Location', '', '1', '28.1', 'non,memb', '0'), 
	('modzzz_schools_view', '1140px', 'School''s location map', '_modzzz_schools_block_map_view', 3, '7', 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''schools'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),
	('modzzz_schools_view', '1140px', 'School''s social sharing block', '_sys_block_title_social_sharing', 3, '8', 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
 
	('modzzz_schools_local_state', '1140px', 'Local State Schools', '_modzzz_schools_block_browse_state_schools', '2', '0', 'StateSchools', '', '1', '71.9', 'non,memb', '0'), 
	('modzzz_schools_local_state', '1140px', 'Local States', '_modzzz_schools_block_browse_state', '3', '0', 'States', '', '1', '28.1', 'non,memb', '0'),

	('modzzz_schools_local', '1140px', 'Local Schools', '_modzzz_schools_block_browse_country', '1', '0', 'Region', '', '1', '100', 'non,memb', '0'),  
	 	
	('modzzz_schools_main', '1140px', 'Featured Schools', '_modzzz_schools_block_featured', '2', '0', 'Featured', '', '1', '71.9', 'non,memb', '0'),
	('modzzz_schools_main', '1140px', 'Map', '_Map', '2', '1', 'PHP', 'return BxDolService::call(''wmap'', ''homepage_part_block'', array (''schools''));', 1, 71.9, 'non,memb', 0),
	('modzzz_schools_main', '1140px', 'Recent Schools', '_modzzz_schools_block_recent', '2', '2', 'Recent', '', '1', '71.9', 'non,memb', '0'),
	('modzzz_schools_main', '1140px', 'Schools Forum Posts', '_modzzz_schools_block_forum', '2', '4', 'Forum', '', '1', '71.9', 'non,memb', '0'),
	('modzzz_schools_main', '1140px', 'School Comments', '_modzzz_schools_block_latest_comments', '2', '5', 'Comments', '', '1', '71.9', 'non,memb', '0'), 
	
	('modzzz_schools_main', '1140px', 'Search School', '_modzzz_schools_block_search', '3', '0', 'Search', '', '1', '28.1', 'non,memb', '0'),
	('modzzz_schools_main', '1140px', 'School Categories', '_modzzz_schools_block_common_categories', '3', '1', 'Categories', '', '1', '28.1', 'non,memb', '0'),	 
	('modzzz_schools_main', '1140px', 'School Tags', '_tags_plural', '3', '2', 'Tags', '', '1', '28.1', 'non,memb', '0'), 
        ('modzzz_schools_main', '1140px', 'School Calendar', '_modzzz_schools_block_calendar', '3', '3', 'Calendar', '', '1', '28.1', 'non,memb', '0'),
  
	('modzzz_schools_my', '1140px', 'Administration Owner', '_modzzz_schools_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
	('modzzz_schools_my', '1140px', 'User''s schools', '_modzzz_schools_block_users_schools', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
	
	('index', '1140px', 'Schools', '_modzzz_schools_block_homepage', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''schools'', ''homepage_block'');', 1, 71.9, 'non,memb', 0),
	('member', '1140px', 'Schools', '_modzzz_schools_block_account', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''schools'', ''accountpage_block'');', 1, 71.9, 'non,memb', 0),  
	('profile', '1140px', 'School Mates', '_modzzz_schools_block_school_mates', 3, 8, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''schools'', ''school_mates'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0),
	('profile', '1140px', 'User Schools', '_modzzz_schools_block_my_schools_joined', 3, 9, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''schools'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0);

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=schools/', 'm/schools/', 'modzzz_schools_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Schools', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_schools_permalinks', 'on', 26, 'Enable friendly permalinks in schools', 'checkbox', '', '', '0', ''),
('modzzz_schools_autoapproval', 'on', @iCategId, 'Activate all schools after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_schools_author_comments_admin', 'on', @iCategId, 'Allow school admin to edit and delete any comment', 'checkbox', '', '', '0', ''),
('modzzz_schools_max_email_invitations', '10', @iCategId, 'Max number of email invitation to send per one invite', 'digit', '', '', '0', ''),
('category_auto_app_modzzz_schools', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
   
('modzzz_schools_perpage_view_fans', '6', @iCategId, 'Number of fans to show on school view page', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_browse_fans', '30', @iCategId, 'Number of fans to show on browse fans page', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_main_recent', '10', @iCategId, 'Number of recently added SCHOOLS to show on schools home', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_browse', '14', @iCategId, 'Number of schools to show on browse pages', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_profile', '4', @iCategId, 'Number of schools to show on profile page', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_homepage', '5', @iCategId, 'Number of schools to show on homepage', 'digit', '', '', '0', ''),
('modzzz_schools_homepage_default_tab', 'recent', @iCategId, 'Default schools block tab on homepage', 'digit', '', '', '0', 'featured,recent,top,popular'),
('modzzz_schools_forum_max_preview', '150', @iCategId, 'length of forum post snippet to show on main page', 'digit', '', '', '0', ''),
('modzzz_schools_comments_max_preview', '150', @iCategId, 'length of comments snippet to show on main page', 'digit', '', '', '0', ''), 
('modzzz_schools_perpage_main_featured', '4', @iCategId, 'Number of featured schools to show on main page', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_main_popular', '4', @iCategId, 'Number of popular schools to show on main page', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_main_top', '4', @iCategId, 'Number of top rated schools to show on main page', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_main_feed', '5', @iCategId, 'Number of activity feed to show on main page', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_main_comment', '5', @iCategId, 'Number of comments to show on main page', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_accountpage', '5', @iCategId, 'Number of schools to show on account page', 'digit', '', '', '0', ''), 
('modzzz_schools_max_preview', '300', @iCategId, 'Length of school description snippet to show in blocks', 'digit', '', '', '0', ''),
('modzzz_schools_default_country', 'US', @iCategId, 'default country for location', 'digit', '', '', 0, ''),  
('modzzz_schools_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', '');

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'modzzz_schools', '_modzzz_schools', 'BxSchoolsSearchResult', 'modules/modzzz/schools/classes/BxSchoolsSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_schools', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]main', 'rate', 'rate_count', 'id', 'BxSchoolsVoting', 'modules/modzzz/schools/classes/BxSchoolsVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_schools', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]main', 'id', 'comments_count', 'BxSchoolsCmts', 'modules/modzzz/schools/classes/BxSchoolsCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_schools', '[db_prefix]views_track', 86400, '[db_prefix]main', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'modzzz_schools', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_schools_permalinks', 'm/schools/browse/tag/{tag}', 'modules/?r=schools/browse/tag/{tag}', '_modzzz_schools');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'modzzz_schools', 'SELECT `Categories` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_schools_permalinks', 'm/schools/browse/category/{tag}', 'modules/?r=schools/browse/category/{tag}', '_modzzz_schools');

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
 ('Elementary School', '0', 'modzzz_schools', '0', 'active'), 
('Primary School', '0', 'modzzz_schools', '0', 'active'),
('Prep School', '0', 'modzzz_schools', '0', 'active'),
('Secondary School', '0', 'modzzz_schools', '0', 'active'),
('High School', '0', 'modzzz_schools', '0', 'active'),
('Community College', '0', 'modzzz_schools', '0', 'active'),
('Technical Institute', '0', 'modzzz_schools', '0', 'active'),
('College', '0', 'modzzz_schools', '0', 'active'),
('University', '0', 'modzzz_schools', '0', 'active'),
('Other', '0', 'modzzz_schools', '0', 'active');
  

-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'modzzz_schools'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'modzzz_schools'),
    ('{TitleShare}', 'share-square-o', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '2', 'modzzz_schools'),
    ('{TitleBroadcast}', 'envelope', '{BaseUri}broadcast/{ID}', '', '', '3', 'modzzz_schools'), 
     
    ('{TitleJoin}', '{IconJoin}', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''join/{ID}/{iViewer}'';', '4', 'modzzz_schools'),
 
    ('{TitleInvite}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''invite/{ID}'';', '7', 'modzzz_schools'),
    ('{AddToFeatured}', 'star-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '8', 'modzzz_schools'),
    ('{TitleManageFans}', 'users', '', 'showPopupAnyHtml (''{BaseUri}manage_fans_popup/{ID}'');', '', '9', 'modzzz_schools'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos/{URI}', '', '', '10', 'modzzz_schools'),
    ('{TitleEmbed}', 'film', '{BaseUri}embed/{URI}', '', '', '11', 'modzzz_schools'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos/{URI}', '', '', '11', 'modzzz_schools'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds/{URI}', '', '', '12', 'modzzz_schools'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files/{URI}', '', '', '13', 'modzzz_schools'),
    ('{TitleSubscribe}', 'paperclip', '', '{ScriptSubscribe}', '', '14', 'modzzz_schools'),
    ('{TitleClaim}', 'key', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''claim/{ID}'';', '15', 'modzzz_schools'),  
    ('{evalResult}', 'plus', '{BaseUri}browse/my&filter=add_school', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_schools_action_add_school'') : '''';', '1', 'modzzz_schools_title'),
    ('{evalResult}', 'pencil', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_schools_action_my_schools'') : '''';', '2', 'modzzz_schools_title');
    
-- top menu 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Schools', '_modzzz_schools_menu_root', 'modules/?r=schools/view/|modules/?r=schools/broadcast/|modules/?r=schools/invite/|modules/?r=schools/edit/|forum/schools/|modules/?r=schools/claim/|modules/?r=schools/embed/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'pencil', '', '0', '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'School View', '_modzzz_schools_menu_view_school', 'modules/?r=schools/view/{modzzz_schools_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'School View Forum', '_modzzz_schools_menu_view_forum', 'forum/schools/forum/{modzzz_schools_view_uri}-0.htm|forum/schools/', 1, 'non,memb', '', '', '$oModuleDb = new BxDolModuleDb(); return $oModuleDb->getModuleByUri(''forum'') ? true : false;', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'School View Fans', '_modzzz_schools_menu_view_fans', 'modules/?r=schools/browse_fans/{modzzz_schools_view_uri}', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'School View Comments', '_modzzz_schools_menu_view_comments', 'modules/?r=schools/comments/{modzzz_schools_view_uri}', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
 
SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Schools', '_modzzz_schools_menu_root', 'modules/?r=schools/home/|modules/?r=schools/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'pencil', 'pencil', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Schools Main Page', '_modzzz_schools_menu_main', 'modules/?r=schools/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Recent Schools', '_modzzz_schools_menu_recent', 'modules/?r=schools/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Top Rated Schools', '_modzzz_schools_menu_top_rated', 'modules/?r=schools/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Popular Schools', '_modzzz_schools_menu_popular', 'modules/?r=schools/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Featured Schools', '_modzzz_schools_menu_featured', 'modules/?r=schools/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Schools Tags', '_modzzz_schools_menu_tags', 'modules/?r=schools/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_schools'),
(NULL, @iCatRoot, 'Schools Categories', '_modzzz_schools_menu_categories', 'modules/?r=schools/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_schools'),
(NULL, @iCatRoot, 'Calendar', '_modzzz_schools_menu_calendar', 'modules/?r=schools/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Search', '_modzzz_schools_menu_search', 'modules/?r=schools/search', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Local Schools', '_modzzz_schools_menu_local', 'modules/?r=schools/local', 12, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');


SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 9, 'Schools', '_modzzz_schools_menu_my_schools_profile', 'modules/?r=schools/browse/user/{profileUsername}|modules/?r=schools/browse/joined/{profileUsername}', ifnull(@iCatProfileOrder,1), 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 4, 'Schools', '_modzzz_schools_menu_my_schools_profile', 'modules/?r=schools/browse/my', ifnull(@iCatProfileOrder,1), 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_schools', '_modzzz_schools', '{siteUrl}modules/?r=schools/administration/', 'Schools module by Modzzz','pencil', @iMax+1);

 
-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'modzzz_schools', 'modzzz_schools', 'modules/?r=schools/browse/recent', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''approved''', 'modules/?r=schools/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''pending''', 'pencil', @iStatSiteOrder);

-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('modzzz_schools', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('modzzz_schoolsp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_modzzz_schools', '__modzzz_schools__ (<a href="modules/?r=schools/browse/my&filter=add_school">__l_add__</a>)');

-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_schools_claim', '<NickName> is claiming ownership of a School Listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<SenderLink>"><SenderName></a> has claimed ownership of a School Listing, <b><a href="<ListUrl>"><ListTitle></a></b>:</p><pre><Message></pre><bx_include_auto:_email_footer.html />', 'School Listing Claim', '0'),  
('modzzz_schools_claim_assign', 'Your Claim on a School Listing at <SiteName> is granted', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>A School Listing, <a href="<ListLink>"><ListTitle></a> that you claimed at <a href="<SiteUrl>"><SiteName></a> has been assigned to you. You now have ownership and administrative rights to the listing.</p><bx_include_auto:_email_footer.html />', 'Claimed School Listing assignment notification', '0'), 
('modzzz_schools_broadcast', '<BroadcastTitle>', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<EntryUrl>"><EntryTitle></a> school admin has sent the following broadcast message:</p> <pre><BroadcastMessage></pre> <p>--</p> <bx_include_auto:_email_footer.html />', 'Schools broadcast message', '0'),
('modzzz_schools_join_request', 'New join request to your school', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>New join request in your school <a href="<EntryUrl>"><EntryTitle></a>. Please review this request and reject or confirm it.</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'New join request to a school notification message', '0'),
('modzzz_schools_join_reject', 'Your join request to a school was rejected', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Sorry, but your request to join <a href="<EntryUrl>"><EntryTitle></a> school was rejected by school admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Join request to a school was rejected notification message', '0'),
('modzzz_schools_join_confirm', 'Your join request to a school was confirmed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Congratulations! Your request to join <a href="<EntryUrl>"><EntryTitle></a> school was confirmed by school admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Join request to a school was confirmed notification message', '0'),
('modzzz_schools_fan_remove', 'You were removed from fans of a school', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>You was removed from fans of <a href="<EntryUrl>"><EntryTitle></a> school by school admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'User was removed from fans of school notification message', '0'),
('modzzz_schools_fan_become_admin', 'You become admin of a school', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Congratulations! You become admin of <a href="<EntryUrl>"><EntryTitle></a> school.</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'User become admin of a school notification message', '0'),
('modzzz_schools_admin_become_fan', 'You school admin status was removed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Your admin status was removed from <a href="<EntryUrl>"><EntryTitle></a> school by school admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'User school admin status was removed notification message', '0'),
('modzzz_schools_invitation', 'Invitation to school: <SchoolName>', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to this school:</p> <pre><InvitationText></pre> <p> <b>School Information:</b><br /> Name: <SchoolName><br /> Location: <SchoolLocation><br /> <a href="<SchoolUrl>">More details</a> </p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Events invitation template', '0'),
('modzzz_schools_sbs', 'School was changed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<ViewLink>"><EntryTitle></a> school was changed: <br /> <ActionName> </p> <p>You may cancel the subscription by clicking the following link: <a href="<UnsubscribeLink>"><UnsubscribeLink></a></p> <p>--</p> <bx_include_auto:_email_footer.html />', 'School subscription template', '0');

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools allow embed', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools photos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools sounds add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools videos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools files add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
  

INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools make claim', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools view school', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools add school', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools add student', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools broadcast message', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools edit any school', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools delete any school', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools approve schools', NULL);
 
-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_schools_profile_delete', '', '', 'BxDolService::call(''schools'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_schools_media_delete', '', '', 'BxDolService::call(''schools'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);

-- member menu
SET @iMemberMenuParent = (SELECT `ID` FROM `sys_menu_member` WHERE `Name` = 'AddContent');
SET @iMemberMenuOrder = (SELECT MAX(`Order`) + 1 FROM `sys_menu_member` WHERE `Parent` = IFNULL(@iMemberMenuParent, -1));
INSERT INTO `sys_menu_member` SET `Name` = 'modzzz_schools', `Eval` = 'return BxDolService::call(''schools'', ''get_member_menu_item_add_content'');', `Type` = 'linked_item', `Parent` = IFNULL(@iMemberMenuParent, 0), `Order` = IFNULL(@iMemberMenuOrder, 1);


-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('schools', 'view_school', '_modzzz_schools_privacy_view_school', '3'),
('schools', 'view_fans', '_modzzz_schools_privacy_view_fans', '3'),
('schools', 'comment', '_modzzz_schools_privacy_comment', 'f'),
('schools', 'rate', '_modzzz_schools_privacy_rate', 'f'),
('schools', 'post_in_forum', '_modzzz_schools_privacy_post_in_forum', 'f'),
('schools', 'join', '_modzzz_schools_privacy_join', '3'),
('schools', 'upload_photos', '_modzzz_schools_privacy_upload_photos', 'a'),
('schools', 'upload_videos', '_modzzz_schools_privacy_upload_videos', 'a'),
('schools', 'upload_sounds', '_modzzz_schools_privacy_upload_sounds', 'a'),
('schools', 'upload_files', '_modzzz_schools_privacy_upload_files', 'a');

-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('modzzz_schools', '', '', 'return BxDolService::call(''schools'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_schools', 'change', 'modzzz_schools_sbs', 'return BxDolService::call(''schools'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_schools', 'commentPost', 'modzzz_schools_sbs', 'return BxDolService::call(''schools'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_schools', 'join', 'modzzz_schools_sbs', 'return BxDolService::call(''schools'', ''get_subscription_params'', array($arg2, $arg3));');

INSERT INTO `sys_pre_values` ( `Key`, `Value`, `Order`, `LKey`) VALUES 
('SchoolLevel', 0, 0, '_Select'),
('SchoolLevel', 1, 1, '_modzzz_schools_level_kindergarten'),
('SchoolLevel', 2, 2, '_modzzz_schools_level_elementary'),
('SchoolLevel', 3, 3, '_modzzz_schools_level_high_school'),
('SchoolLevel', 4, 4, '_modzzz_schools_level_junior_high'),
('SchoolLevel', 5, 5, '_modzzz_schools_level_prep_school'),
('SchoolLevel', 6, 6, '_modzzz_schools_level_technical_institute'),
('SchoolLevel', 7, 7, '_modzzz_schools_level_community_college'),
('SchoolLevel', 8, 8, '_modzzz_schools_level_college'),
('SchoolLevel', 9, 9, '_modzzz_schools_level_university'),
('SchoolLevel', 10, 10, '_modzzz_schools_other')  
 ; 


INSERT INTO `sys_pre_values` ( `Key`, `Value`, `Order`, `LKey`) VALUES 
('SchoolType', 0, 0, '_Select'),
('SchoolType', 1, 1, '_modzzz_schools_type_public'),
('SchoolType', 2, 2, '_modzzz_schools_type_private'),
('SchoolType', 3, 3, '_modzzz_schools_type_charter'),
('SchoolType', 4, 4, '_modzzz_schools_type_military'),
('SchoolType', 5, 5, '_modzzz_schools_other') 
 ;  

INSERT INTO `sys_pre_values` ( `Key`, `Value`, `Order`, `LKey`) VALUES 
('SchoolQualifications', 0, '', '_Select'),
('SchoolQualifications', 1, 1, '_modzzz_schools_qualifications_certificate'),
('SchoolQualifications', 2, 2, '_modzzz_schools_qualifications_certificate_school'), 
('SchoolQualifications', 3, 3, '_modzzz_schools_qualifications_diploma'),
('SchoolQualifications', 4, 4, '_modzzz_schools_qualifications_diploma_high'),
('SchoolQualifications', 5, 5, '_modzzz_schools_qualifications_diploma_hnd'),
('SchoolQualifications', 6, 6, '_modzzz_schools_qualifications_diploma_post'), 
('SchoolQualifications', 7, 7, '_modzzz_schools_qualifications_associates_degree'),
('SchoolQualifications', 8, 8, '_modzzz_schools_qualifications_bachelors_degree'),
('SchoolQualifications', 9, 9, '_modzzz_schools_qualifications_masters_degree'),
('SchoolQualifications', 10, 10, '_modzzz_schools_qualifications_post_grad_degree'),
('SchoolQualifications', 11, 11, '_modzzz_schools_other'); 
   
 
 INSERT INTO `sys_pre_values` ( `Key`, `Value`, `Order`, `LKey`) VALUES 
('SchoolClubs', 0, '', '_Select'),
('SchoolClubs', 2, 2, '_modzzz_schools_clubs_debate'),
('SchoolClubs', 3, 3, '_modzzz_schools_clubs_drama'),
('SchoolClubs', 4, 4, '_modzzz_schools_clubs_community_service'),
('SchoolClubs', 5, 5, '_modzzz_schools_clubs_dance'),
('SchoolClubs', 6, 6, '_modzzz_schools_clubs_art'), 
('SchoolClubs', 7, 7, '_modzzz_schools_clubs_science'),
('SchoolClubs', 8, 8, '_modzzz_schools_clubs_chess'),
('SchoolClubs', 9, 9, '_modzzz_schools_clubs_music'),
('SchoolClubs', 10, 10, '_modzzz_schools_clubs_choir'),
('SchoolClubs', 11, 11, '_modzzz_schools_other') 
 ;  

INSERT INTO `sys_pre_values` ( `Key`, `Value`, `Order`, `LKey`) VALUES 
('SchoolSports', 0, '', '_Select'),
('SchoolSports', 2, 2, '_modzzz_schools_sports_football'),
('SchoolSports', 3, 3, '_modzzz_schools_sports_soccer'),
('SchoolSports', 4, 4, '_modzzz_schools_sports_basketball'),
('SchoolSports', 5, 5, '_modzzz_schools_sports_cricket'),
('SchoolSports', 6, 6, '_modzzz_schools_sports_table_tennis'), 
('SchoolSports', 7, 7, '_modzzz_schools_sports_lawn_tennis'),
('SchoolSports', 8, 8, '_modzzz_schools_sports_swimming'),
('SchoolSports', 9, 9, '_modzzz_schools_sports_hockey'),
('SchoolSports', 10, 10, '_modzzz_schools_sports_bowling'), 
('SchoolSports', 11, 11, '_modzzz_schools_sports_golf'),
('SchoolSports', 12, 12, '_modzzz_schools_sports_netball'),
('SchoolSports', 13, 13, '_modzzz_schools_sports_track_and_field'),
('SchoolSports', 14, 14, '_modzzz_schools_sports_rugby'),
('SchoolSports', 15, 15, '_modzzz_schools_sports_volleyball'),
('SchoolSports', 16, 16, '_modzzz_schools_sports_board_games'),
('SchoolSports', 17, 17, '_modzzz_schools_other') 
 ;  


 -- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('modzzz_schools', '_modzzz_schools', '0.8', 'auto', 'BxSchoolsSiteMaps', 'modules/modzzz/schools/classes/BxSchoolsSiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('modzzz_schools', '_modzzz_schools', 'modzzz_schools_main', 'created', '', '', 1, @iMaxOrderCharts);


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_schools_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''schools'', ''map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);
 
 


-- Add Instructor functionality

ALTER TABLE `modzzz_schools_main` ADD `instructors_count` int(11) NOT NULL; 
 
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
('modzzz_schools_view', '1140px', 'School''s instructors block', '_modzzz_schools_block_instructors', '2', '5', 'Instructors', '', '1', '71.9', 'non,memb', '0');  
 
SET @iCatRoot = (SELECT `ID` FROM `sys_menu_top` WHERE `Parent`=0 AND `Name`='Schools' AND `Caption`='_modzzz_schools_menu_root' AND `Type`='system' AND `Active`=1);
 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
 (NULL, @iCatRoot, 'School View Instructors', '_modzzz_schools_menu_view_instructors', 'modules/?r=schools/instructors/browse/{modzzz_schools_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''); 
 
CREATE TABLE IF NOT EXISTS `[db_prefix]instructors_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `school_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `profile_id` INT NOT NULL,
  `use_profile_desc` int(11) NOT NULL,
  `use_profile_photo` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `position` varchar(50) NOT NULL,  
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `type` enum('site','external') NOT NULL default 'external',  
  `icon` varchar(255) NOT NULL, 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_instructors_uri` (`uri`),
  KEY `school_instructors_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
    
CREATE TABLE IF NOT EXISTS `[db_prefix]instructors_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `[db_prefix]instructors_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
     
CREATE TABLE IF NOT EXISTS `[db_prefix]instructors_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
    
CREATE TABLE IF NOT EXISTS `[db_prefix]instructors_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `[db_prefix]instructors_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_schools_instructors', '[db_prefix]instructors_rating', '[db_prefix]instructors_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]instructors_main', 'rate', 'rate_count', 'id', 'BxSchoolsInstructorsVoting', 'modules/modzzz/schools/classes/BxSchoolsInstructorsVoting.php');
 
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_schools_instructors', '[db_prefix]instructors_cmts', '[db_prefix]instructors_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]instructors_main', 'id', 'comments_count', 'BxSchoolsInstructorsCmts', 'modules/modzzz/schools/classes/BxSchoolsInstructorsCmts.php');
  
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''instructors/edit/{ID}'';', '0', 'modzzz_schools_instructors'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''instructors/delete/{ID}'';', '1', 'modzzz_schools_instructors'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/instructors/{URI}', '', '', '2', 'modzzz_schools_instructors');
  
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

SET @iMaxOrder = @iMaxOrder + 1;
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_instructors_view', 'School Instructors View', @iMaxOrder);
 

SET @iMaxOrder = @iMaxOrder + 1;
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_instructors_browse', 'School Instructors Browse', @iMaxOrder);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
('modzzz_schools_instructors_browse', '1140px', 'School Instructors''s browse instructors block', '_modzzz_schools_block_browse_instructors', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

('modzzz_schools_instructors_view', '1140px', 'School Instructors''s description block', '_modzzz_schools_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_instructors_view', '1140px', 'School Instructors''s photos block', '_modzzz_schools_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_instructors_view', '1140px', 'School Instructors''s comments block', '_modzzz_schools_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0'),    
('modzzz_schools_instructors_view', '1140px', 'School Instructors''s info block', '_modzzz_schools_block_info', '2', '0', 'Info', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_instructors_view', '1140px', 'School Instructors''s actions block', '_modzzz_schools_block_actions', '2', '1', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_instructors_view', '1140px', 'School Instructors''s rate block', '_modzzz_schools_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0');    

 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
('{TitleInstructorsAdd}', 'plus', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''instructors/add/{ID}'';', '14', 'modzzz_schools'); 
  
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
('{TitleInstructorsAttend}', 'plus', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''instructors/add/{ID}/exist'';', '16', 'modzzz_schools'); 


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=schools/instructors/add/|modules/?r=schools/instructors/edit/|modules/?r=schools/instructors/view/|modules/?r=schools/instructors/browse/') WHERE `Parent`=0 AND `Name`='Schools' AND `Type`='system' AND `Caption`='_modzzz_schools_menu_root'; 
  
INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools add instructor', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
-- [END] INSTRUCTOR


-- Add Course functionality

ALTER TABLE `modzzz_schools_main` ADD `courses_count` int(11) NOT NULL; 
 
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
('modzzz_schools_view', '1140px', 'School''s courses block', '_modzzz_schools_block_courses', '2', '6', 'Courses', '', '1', '71.9', 'non,memb', '0');  
 
SET @iCatRoot = (SELECT `ID` FROM `sys_menu_top` WHERE `Parent`=0 AND `Name`='Schools' AND `Caption`='_modzzz_schools_menu_root' AND `Type`='system' AND `Active`=1);
 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'School View Courses', '_modzzz_schools_menu_view_courses', 'modules/?r=schools/courses/browse/{modzzz_schools_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''); 
 
CREATE TABLE IF NOT EXISTS `[db_prefix]courses_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `school_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,

  `credits` varchar(100) NOT NULL default '',
  `course_code` varchar(100) NOT NULL default '',
  `instructors` varchar(255) NOT NULL,  
  `semester` varchar(100) NOT NULL default '',
  `timetable` text NOT NULL,
  `delivery_methods` text NOT NULL, 
  `overview` text NOT NULL,
  `objectives` text NOT NULL,
  `prerequisite` text NOT NULL,
  `content` text NOT NULL,
  `assessment` text NOT NULL,
  `materials` text NOT NULL,
  `specialization` text NOT NULL,

  `status` enum('approved','pending') NOT NULL default 'approved', 
  `icon` varchar(255) NOT NULL, 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_courses_uri` (`uri`),
  KEY `school_courses_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
    
CREATE TABLE IF NOT EXISTS `[db_prefix]courses_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `[db_prefix]courses_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
     
CREATE TABLE IF NOT EXISTS `[db_prefix]courses_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
    
CREATE TABLE IF NOT EXISTS `[db_prefix]courses_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `[db_prefix]courses_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_schools_courses', '[db_prefix]courses_rating', '[db_prefix]courses_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]courses_main', 'rate', 'rate_count', 'id', 'BxSchoolsCoursesVoting', 'modules/modzzz/schools/classes/BxSchoolsCoursesVoting.php');
 
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_schools_courses', '[db_prefix]courses_cmts', '[db_prefix]courses_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]courses_main', 'id', 'comments_count', 'BxSchoolsCoursesCmts', 'modules/modzzz/schools/classes/BxSchoolsCoursesCmts.php');
  
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''courses/edit/{ID}'';', '0', 'modzzz_schools_courses'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''courses/delete/{ID}'';', '1', 'modzzz_schools_courses'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/courses/{URI}', '', '', '2', 'modzzz_schools_courses');
  
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

SET @iMaxOrder = @iMaxOrder + 1;
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_courses_view', 'School Courses View', @iMaxOrder);
 

SET @iMaxOrder = @iMaxOrder + 1;
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_courses_browse', 'School Courses Browse', @iMaxOrder);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
('modzzz_schools_courses_browse', '1140px', 'School Courses''s browse courses block', '_modzzz_schools_block_browse_courses', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),
 
('modzzz_schools_courses_view', '1140px', 'School Courses''s description block', '_modzzz_schools_block_course_details', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_courses_view', '1140px', 'School Courses''s photos block', '_modzzz_schools_block_photo', '2', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_courses_view', '1140px', 'School Courses''s comments block', '_modzzz_schools_block_comments', '2', '2', 'Comments', '', '1', '71.9', 'non,memb', '0'),    
('modzzz_schools_courses_view', '1140px', 'School Courses''s info block', '_modzzz_schools_block_info', '3', '0', 'Info', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_courses_view', '1140px', 'School Courses''s actions block', '_modzzz_schools_block_actions', '3', '1', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_courses_view', '1140px', 'School Courses''s rate block', '_modzzz_schools_block_rate', '3', '2', 'Rate', '', '1', '28.1', 'non,memb', '0');    

 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
('{TitleCoursesAdd}', 'plus', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''courses/add/{ID}'';', '14', 'modzzz_schools'); 
  

UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=schools/courses/add/|modules/?r=schools/courses/edit/|modules/?r=schools/courses/view/|modules/?r=schools/courses/browse/') WHERE `Parent`=0 AND `Name`='Schools' AND `Type`='system' AND `Caption`='_modzzz_schools_menu_root'; 
  
-- [END] COURSES

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
('modzzz_schools_courses_view', '1140px', 'School Courses''s info block', '_modzzz_schools_block_info', '3', '0', 'Info', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_instructors_view', '1140px', 'School Instructors''s info block', '_modzzz_schools_block_info', '3', '0', 'Info', '', '1', '28.1', 'non,memb', '0'); 
 
UPDATE `sys_page_compose` SET `Order`=`Order`+1 WHERE `Column`=2 and `Page`='modzzz_schools_instructors_view';

UPDATE `sys_page_compose` SET `Order`=`Order`+1 WHERE `Column`=2 and `Page`='modzzz_schools_courses_view';


-- [BEGIN] NEWS
 

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
('modzzz_schools_view', '1140px', 'School''s news block', '_modzzz_schools_block_news', '2', '6', 'News', '', '1', '71.9', 'non,memb', '0');  
 
 
SET @iCatRoot = (SELECT `ID` FROM `sys_menu_top` WHERE `Parent`=0 AND `Name`='Schools' AND `Caption`='_modzzz_schools_menu_root' AND `Type`='system' AND `Active`=1);
 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
 (NULL, @iCatRoot, 'School View News', '_modzzz_schools_menu_view_news', 'modules/?r=schools/news/browse/{modzzz_schools_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
 

CREATE TABLE IF NOT EXISTS `modzzz_schools_news_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `school_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL, 
  `desc` text NOT NULL, 
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `icon` varchar(255) NOT NULL, 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_news_uri` (`uri`),
  KEY `school_news_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
   
CREATE TABLE IF NOT EXISTS `modzzz_schools_news_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 

CREATE TABLE IF NOT EXISTS `modzzz_schools_news_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_news_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_news_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_news_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_schools_news_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_schools_news_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
    
CREATE TABLE IF NOT EXISTS `modzzz_schools_news_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_schools_news', 'modzzz_schools_news_rating', 'modzzz_schools_news_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_schools_news_main', 'rate', 'rate_count', 'id', 'BxSchoolsNewsVoting', 'modules/modzzz/schools/classes/BxSchoolsNewsVoting.php');
 
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_schools_news', 'modzzz_schools_news_cmts', 'modzzz_schools_news_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_schools_news_main', 'id', 'comments_count', 'BxSchoolsNewsCmts', 'modules/modzzz/schools/classes/BxSchoolsNewsCmts.php');
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/edit/{ID}'';', '0', 'modzzz_schools_news'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/delete/{ID}'';', '1', 'modzzz_schools_news'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/news/{URI}', '', '', '2', 'modzzz_schools_news');  
 
 

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

SET @iMaxOrder = @iMaxOrder + 1;
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_news_view', 'School News View', @iMaxOrder);
 
SET @iMaxOrder = @iMaxOrder + 1;
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_news_browse', 'School News Browse', @iMaxOrder);
  
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
('modzzz_schools_news_browse', '1140px', 'School News''s browse news block', '_modzzz_schools_block_browse_news', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

('modzzz_schools_news_view', '1140px', 'School News''s description block', '_modzzz_schools_block_desc', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_news_view', '1140px', 'School News''s photos block', '_modzzz_schools_block_photo', '2', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_news_view', '1140px', 'School News''s videos block', '_modzzz_schools_block_video', '2', '3', 'Video', '', '1', '71.9', 'non,memb', '0'),    
('modzzz_schools_news_view', '1140px', 'School News''s sounds block', '_modzzz_schools_block_sound', '2', '4', 'Sound', '', '1', '71.9', 'non,memb', '0'),    
('modzzz_schools_news_view', '1140px', 'School News''s files block', '_modzzz_schools_block_files', '2', '5', 'Files', '', '1', '71.9', 'non,memb', '0'),   
('modzzz_schools_news_view', '1140px', 'School News''s comments block', '_modzzz_schools_block_comments', '2', '2', 'Comments', '', '1', '71.9', 'non,memb', '0'),     
('modzzz_schools_news_view', '1140px', 'School News''s info block', '_modzzz_schools_block_info', '3', '0', 'Info', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_news_view', '1140px', 'School News''s actions block', '_modzzz_schools_block_actions', '3', '1', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_news_view', '1140px', 'School News''s rate block', '_modzzz_schools_block_rate', '3', '2', 'Rate', '', '1', '28.1', 'non,memb', '0');     
 
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleNewsAdd}', 'plus', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/add/{ID}'';', '15', 'modzzz_schools'); 
 
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=schools/news/add/|modules/?r=schools/news/edit/|modules/?r=schools/news/view/|modules/?r=schools/news/browse/') WHERE `Parent`=0 AND `Name`='Schools' AND `Type`='system' AND `Caption`='_modzzz_schools_menu_root'; 
 
-- [END] - NEWS



-- [BEGIN] - EVENTS
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_schools_event_join_request', 'New join request to your school event', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>New join request in your school event <a href="<EntryUrl>"><EntryTitle></a>. Please review this request and reject or confirm it.</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'New join request to a school event notification message', '0'),
('modzzz_schools_event_join_reject', 'Your join request to a school event was rejected', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Sorry, but your request to join <a href="<EntryUrl>"><EntryTitle></a> school event was rejected by the admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'rejected join request to a school event notification message', '0'),
('modzzz_schools_event_join_confirm', 'Your join request to a school event was confirmed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Congratulations! Your request to join <a href="<EntryUrl>"><EntryTitle></a> school event was confirmed by school admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'confirmed join request to a school event notification message', '0'),
('modzzz_schools_event_fan_remove', 'You were removed from participants of a school event', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>You was removed from participants of <a href="<EntryUrl>"><EntryTitle></a> school event by school admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'participant removed from school event notification message', '0'),
('modzzz_schools_event_invitation', 'Invitation to school event: <EventName>', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to a school event:</p> <pre><InvitationText></pre> <p> <b>Event Information:</b><br /> Name: <EventName><br /> Location: <EventLocation><br /> <a href="<EventUrl>">More details</a> </p> <p>--</p> <bx_include_auto:_email_footer.html />', 'school event invitation template', '0');
 

INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('schools', 'view_participants', '_bx_events_privacy_view_participants', '3'); 

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
('modzzz_schools_view', '1140px', 'School''s events block', '_modzzz_schools_block_events', '2', '6', 'Events', '', '1', '71.9', 'non,memb', '0');  
 
SET @iCatRoot = (SELECT `ID` FROM `sys_menu_top` WHERE `Parent`=0 AND `Name`='Schools' AND `Caption`='_modzzz_schools_menu_root' AND `Type`='system' AND `Active`=1);

 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
 (NULL, @iCatRoot, 'School View Events', '_modzzz_schools_menu_view_events', 'modules/?r=schools/events/browse/{modzzz_schools_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''); 
  
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `school_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL, 
  `desc` text NOT NULL, 
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `icon` varchar(255) NOT NULL, 
  `thumb` int(11) NOT NULL,
  `event_start` int(11) NOT NULL,  
  `event_end` int(11) NOT NULL,  
  `country` varchar(2) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `state` varchar(10) NOT NULL default '',
  `address1` varchar(100) NOT NULL default '',
  `zip` varchar(100) NOT NULL default '',
  `place` varchar(255) NOT NULL default '',  
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `fans_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL, 
  `allow_join_to`  varchar(16) NOT NULL,
  `join_confirmation` tinyint(4) NOT NULL default '0',
  `allow_view_participants_to` varchar(16) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_events_uri` (`uri`),
  KEY `school_events_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
    
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
     
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_events_fans` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  `confirmed` tinyint(4) unsigned NOT NULL default '0', 
  PRIMARY KEY  (`id_entry`,`id_profile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_schools_events', 'modzzz_schools_events_rating', 'modzzz_schools_events_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_schools_events_main', 'rate', 'rate_count', 'id', 'BxSchoolsEventsVoting', 'modules/modzzz/schools/classes/BxSchoolsEventsVoting.php');
  
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_schools_events', 'modzzz_schools_events_cmts', 'modzzz_schools_events_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_schools_events_main', 'id', 'comments_count', 'BxSchoolsEventsCmts', 'modules/modzzz/schools/classes/BxSchoolsEventsCmts.php');
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''events/edit/{ID}'';', '0', 'modzzz_schools_events'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''events/delete/{ID}'';', '1', 'modzzz_schools_events'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/events/{URI}', '', '', '2', 'modzzz_schools_events'),  
    ('{TitleJoin}', '{IconJoin}', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event_join/{ID}/{iViewer}'';', '3', 'modzzz_schools_events'),
    ('{TitleInvite}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event_invite/{ID}'';', '4', 'modzzz_schools_events'),
    ('{TitleManageFans}', 'users', '', 'showPopupAnyHtml (''{BaseUri}event_manage_fans_popup/{ID}'');', '', '5', 'modzzz_schools_events');  
 
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

SET @iMaxOrder = @iMaxOrder + 1;
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_events_view', 'School Events View', @iMaxOrder);
  
SET @iMaxOrder = @iMaxOrder + 1;
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_events_browse', 'School Events Browse', @iMaxOrder);
 
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
('modzzz_schools_events_browse', '1140px', 'School Events''s browse events block', '_modzzz_schools_block_browse_events', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

('modzzz_schools_events_view', '1140px', 'School Events''s description block', '_modzzz_schools_block_desc', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_events_view', '1140px', 'School Events''s photos block', '_modzzz_schools_block_photo', '2', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_events_view', '1140px', 'School Events''s videos block', '_modzzz_schools_block_video', '2', '3', 'Video', '', '1', '71.9', 'non,memb', '0'),    
('modzzz_schools_events_view', '1140px', 'School Events''s sounds block', '_modzzz_schools_block_sound', '2', '4', 'Sound', '', '1', '71.9', 'non,memb', '0'),    
('modzzz_schools_events_view', '1140px', 'School Events''s files block', '_modzzz_schools_block_files', '2', '5', 'Files', '', '1', '71.9', 'non,memb', '0'), 
('modzzz_schools_events_view', '1140px', 'School Events''s comments block', '_modzzz_schools_block_comments', '2', '2', 'Comments', '', '1', '71.9', 'non,memb', '0'),     
('modzzz_schools_events_view', '1140px', 'School Events''s info block', '_modzzz_schools_block_info', '3', '0', 'Info', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_events_view', '1140px', 'School Events''s actions block', '_modzzz_schools_block_actions', '3', '1', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_events_view', '1140px', 'School Events''s rate block', '_modzzz_schools_block_rate', '3', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),     
('modzzz_schools_events_view', '1140px', 'School Events''s location block', '_modzzz_schools_block_location', '3', '3', 'Location', '', '1', '28.1', 'non,memb', '0'),     
('modzzz_schools_events_view', '1140px', 'School Events''s Location', '_modzzz_schools_block_map_view', 3, 4, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''schools_event'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),
('modzzz_schools_events_view', '1140px', 'School Event''s participants block', '_modzzz_schools_block_participants', '3', '5', 'Participants', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_events_view', '1140px', 'School Event''s unconfirmed participants block', '_modzzz_schools_block_participants_unconfirmed', '3', '6', 'ParticipantsUnconfirmed', '', '1', '28.1', 'non,memb', '0');  
  
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleEventsAdd}', 'plus', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''events/add/{ID}'';', '15', 'modzzz_schools'); 
 
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=schools/events/add/|modules/?r=schools/events/edit/|modules/?r=schools/events/view/|modules/?r=schools/events/browse/') WHERE `Parent`=0 AND `Name`='Schools' AND `Type`='system' AND `Caption`='_modzzz_schools_menu_root'; 
  
 
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_schools_event_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''schools'', ''event_map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);

-- [END] - EVENTS
 

SET @iCategId = (SELECT `id` FROM  `sys_options_cats` WHERE `name`='Schools');
 
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES   
('modzzz_schools_perpage_view_subitems', '6', @iCategId, 'Number of items (Instructors etc.) to show on school view page', 'digit', '', '', '0', ''),
('modzzz_schools_perpage_browse_subitems', '30', @iCategId, 'Number of items (Instructors etc.) to show on the sub section browse page', 'digit', '', '', '0', '');    
 
 

-- [BEGIN] - Students
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
('modzzz_schools_view', '1140px', 'School''s student block', '_modzzz_schools_block_student', '2', '6', 'Student', '', '1', '71.9', 'non,memb', '0'),  
('modzzz_schools_view', '1140px', 'School''s alumni block', '_modzzz_schools_block_alumni', '2', '7', 'Alumni', '', '1', '71.9', 'non,memb', '0');  
 
SET @iCatRoot = (SELECT `ID` FROM `sys_menu_top` WHERE `Parent`=0 AND `Name`='Schools' AND `Caption`='_modzzz_schools_menu_root' AND `Type`='system' AND `Active`=1);
 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
 (NULL, @iCatRoot, 'School View Student', '_modzzz_schools_menu_view_student', 'modules/?r=schools/student/browse/{modzzz_schools_view_uri}', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''), 
 (NULL, @iCatRoot, 'School View Alumni', '_modzzz_schools_menu_view_alumni', 'modules/?r=schools/alumni/browse/{modzzz_schools_view_uri}', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''); 
  
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_student_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `school_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `membership_type` varchar(20) NOT NULL, 
  `use_profile_desc` int(11) NOT NULL,
  `use_profile_photo` int(11) NOT NULL,  
  `year_entered` int(11) NOT NULL,
  `year_left` int(11) NOT NULL,  
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL NOT NULL default '3',
  `allow_rate_to` varchar(16) NOT NULL NOT NULL default '3',
  `allow_upload_photos_to` varchar(16) NOT NULL NOT NULL default 'a', 
  `allow_upload_videos_to` varchar(16) NOT NULL NOT NULL default 'a',
  `allow_upload_sounds_to` varchar(16) NOT NULL NOT NULL default 'a',
  `allow_upload_files_to` varchar(16) NOT NULL NOT NULL default 'a',
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_student_uri` (`uri`),
  KEY `school_student_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_schools_student_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_student_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_student_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_schools_student_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_schools_student_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_schools_student', 'modzzz_schools_student_rating', 'modzzz_schools_student_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_schools_student_main', 'rate', 'rate_count', 'id', 'BxSchoolsStudentVoting', 'modules/modzzz/schools/classes/BxSchoolsStudentVoting.php');
 
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_schools_student', 'modzzz_schools_student_cmts', 'modzzz_schools_student_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_schools_student_main', 'id', 'comments_count', 'BxSchoolsStudentCmts', 'modules/modzzz/schools/classes/BxSchoolsStudentCmts.php');
  
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''student/edit/{ID}'';', '0', 'modzzz_schools_student'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''student/delete/{ID}'';', '1', 'modzzz_schools_student'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/student/{URI}', '', '', '2', 'modzzz_schools_student');
 

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

SET @iMaxOrder = @iMaxOrder + 1;
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_student_view', 'School Student View', @iMaxOrder);
  
SET @iMaxOrder = @iMaxOrder + 1;
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_schools_student_browse', 'School Student Browse', @iMaxOrder);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
('modzzz_schools_student_browse', '1140px', 'School Student''s browse student block', '_modzzz_schools_block_browse_student', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

('modzzz_schools_student_view', '1140px', 'School Student''s description block', '_modzzz_schools_block_desc', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_student_view', '1140px', 'School Student''s photos block', '_modzzz_schools_block_photo', '2', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
('modzzz_schools_student_view', '1140px', 'School Student''s comments block', '_modzzz_schools_block_comments', '2', '2', 'Comments', '', '1', '71.9', 'non,memb', '0'),    
('modzzz_schools_student_view', '1140px', 'School Student''s actions block', '_modzzz_schools_block_actions', '3', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('modzzz_schools_student_view', '1140px', 'School Student''s rate block', '_modzzz_schools_block_rate', '3', '1', 'Rate', '', '1', '28.1', 'non,memb', '0');     
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleStudentAdd}', 'plus', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''student/add/{ID}'';', '15', 'modzzz_schools'), 
    ('{TitleStudentAttend}', 'plus', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxSchoolsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''student/add/{ID}/exist'';', '16', 'modzzz_schools'); 
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=schools/student/add/|modules/?r=schools/student/edit/|modules/?r=schools/student/view/|modules/?r=schools/student/browse/|modules/?r=schools/alumni/browse/') WHERE `Parent`=0 AND `Name`='Schools' AND `Type`='system' AND `Caption`='_modzzz_schools_menu_root'; 
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'schools add student', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

 