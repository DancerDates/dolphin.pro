-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]fans`;
DROP TABLE IF EXISTS `[db_prefix]admins`;
DROP TABLE IF EXISTS `[db_prefix]images`;
DROP TABLE IF EXISTS `[db_prefix]videos`;
DROP TABLE IF EXISTS `[db_prefix]sounds`;
DROP TABLE IF EXISTS `[db_prefix]files`;
DROP TABLE IF EXISTS `[db_prefix]rating`;
DROP TABLE IF EXISTS `[db_prefix]rating_track`;
DROP TABLE IF EXISTS `[db_prefix]cmts`;
DROP TABLE IF EXISTS `[db_prefix]cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]views_track`;
DROP TABLE IF EXISTS `[db_prefix]activity`;
DROP TABLE IF EXISTS `[db_prefix]claim`;
DROP TABLE IF EXISTS `[db_prefix]profiles`;
DROP TABLE IF EXISTS `[db_prefix]cities`;
DROP TABLE IF EXISTS `[db_prefix]countries`;
DROP TABLE IF EXISTS `[db_prefix]youtube`;

-- forum tables
DROP TABLE IF EXISTS `[db_prefix]forum`;
DROP TABLE IF EXISTS `[db_prefix]forum_cat`;
DROP TABLE IF EXISTS `[db_prefix]forum_flag`;
DROP TABLE IF EXISTS `[db_prefix]forum_post`;
DROP TABLE IF EXISTS `[db_prefix]forum_topic`;
DROP TABLE IF EXISTS `[db_prefix]forum_user`;
DROP TABLE IF EXISTS `[db_prefix]forum_user_activity`;
DROP TABLE IF EXISTS `[db_prefix]forum_user_stat`;
DROP TABLE IF EXISTS `[db_prefix]forum_vote`;
DROP TABLE IF EXISTS `[db_prefix]forum_actions_log`;
DROP TABLE IF EXISTS `[db_prefix]forum_attachments`;
DROP TABLE IF EXISTS `[db_prefix]forum_signatures`;

-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_schools_local','modzzz_schools_local_state','modzzz_schools_view', 'modzzz_schools_celendar', 'modzzz_schools_main', 'modzzz_schools_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_schools_local','modzzz_schools_local_state','modzzz_schools_view', 'modzzz_schools_celendar', 'modzzz_schools_main', 'modzzz_schools_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Schools';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Schools';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'School Mates';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Schools';
 
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=schools/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_schools';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_schools';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_schools';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_schools';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_schools';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Schools';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_schools';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_schools';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_schools';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_schools' OR `Type` = 'modzzz_schools_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_schools';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_schools', 'modzzz_schoolsp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_schools';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 'modzzz_schools_broadcast' OR `Name` = 'modzzz_schools_join_request' OR `Name` = 'modzzz_schools_join_reject' OR `Name` = 'modzzz_schools_join_confirm' OR `Name` = 'modzzz_schools_fan_remove' OR `Name` = 'modzzz_schools_fan_become_admin' OR `Name` = 'modzzz_schools_admin_become_fan' OR `Name` = 'modzzz_schools_sbs' OR `Name` = 'modzzz_schools_invitation' OR `Name` = 'modzzz_schools_claim' OR `Name` = 'modzzz_schools_claim_assign';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Schools' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Schools' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Schools';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Schools';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_schools';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Schools' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_schools_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('schools make claim', 'schools view school', 'schools browse', 'schools search', 'schools add school', 'schools comments delete and edit', 'schools edit any school', 'schools delete any school', 'schools mark as featured', 'schools approve schools', 'schools broadcast message', 'schools photos add', 'schools sounds add', 'schools videos add', 'schools files add', 'schools allow embed');

DELETE FROM `sys_acl_actions` WHERE `Name` IN('schools make claim', 'schools view school', 'schools browse', 'schools search', 'schools add school', 'schools comments delete and edit', 'schools edit any school', 'schools delete any school', 'schools mark as featured', 'schools approve schools', 'schools broadcast message', 'schools photos add', 'schools sounds add', 'schools videos add', 'schools files add', 'schools allow embed');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_schools_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_schools_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_schools';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'schools';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_schools';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_schools';


DELETE FROM `sys_pre_values` WHERE  `Key` IN ('SchoolType','SchoolQualifications','SchoolLevel','SchoolSports', 'SchoolClubs');


SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_schools_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_schools';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_schools';


DROP TABLE IF EXISTS `[db_prefix]instructors_main`;
DROP TABLE IF EXISTS `[db_prefix]instructors_images`;

DROP TABLE IF EXISTS `[db_prefix]instructors_rating`;
DROP TABLE IF EXISTS `[db_prefix]instructors_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]instructors_cmts`;
DROP TABLE IF EXISTS `[db_prefix]instructors_cmts_track`;

DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_schools_instructors';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_schools_instructors';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_schools_instructors';


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_schools_instructors_view','modzzz_schools_instructors_browse');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_schools_instructors_view','modzzz_schools_instructors_browse');

 
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('schools add instructor');

DELETE FROM `sys_acl_actions` WHERE `Name` IN('schools add instructor');

DROP TABLE IF EXISTS `[db_prefix]courses_main`;
DROP TABLE IF EXISTS `[db_prefix]courses_images`;

DROP TABLE IF EXISTS `[db_prefix]courses_rating`;
DROP TABLE IF EXISTS `[db_prefix]courses_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]courses_cmts`;
DROP TABLE IF EXISTS `[db_prefix]courses_cmts_track`;

DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_schools_courses';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_schools_courses';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_schools_courses';


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_schools_courses_view','modzzz_schools_courses_browse');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_schools_courses_view','modzzz_schools_courses_browse');


-- BEGIN REMOVE EVENTS

DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_schools_event_join_request', 'modzzz_schools_event_join_reject', 'modzzz_schools_event_join_confirm', 'modzzz_schools_event_fan_remove', 'modzzz_schools_event_invitation');
 
DROP TABLE IF EXISTS `[db_prefix]events_main`;
DROP TABLE IF EXISTS `[db_prefix]events_images`;
DROP TABLE IF EXISTS `[db_prefix]events_images`;
DROP TABLE IF EXISTS `[db_prefix]events_videos`;
DROP TABLE IF EXISTS `[db_prefix]events_sounds`;
DROP TABLE IF EXISTS `[db_prefix]events_files`;

DROP TABLE IF EXISTS `[db_prefix]events_rating`;
DROP TABLE IF EXISTS `[db_prefix]events_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]events_cmts`;
DROP TABLE IF EXISTS `[db_prefix]events_cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]events_fans`;

DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_schools_events';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_schools_events';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_schools_events';


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_schools_events_view','modzzz_schools_events_browse');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_schools_events_view','modzzz_schools_events_browse');
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_schools_event_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;


-- BEGIN REMOVE Student

DROP TABLE IF EXISTS `[db_prefix]student_main`;
DROP TABLE IF EXISTS `[db_prefix]student_images`;
DROP TABLE IF EXISTS `[db_prefix]images`;
DROP TABLE IF EXISTS `[db_prefix]videos`;
DROP TABLE IF EXISTS `[db_prefix]sounds`;
DROP TABLE IF EXISTS `[db_prefix]files`;

DROP TABLE IF EXISTS `[db_prefix]student_rating`;
DROP TABLE IF EXISTS `[db_prefix]student_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]student_cmts`;
DROP TABLE IF EXISTS `[db_prefix]student_cmts_track`;

DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_schools_student';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_schools_student';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_schools_student';


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_schools_student_view','modzzz_schools_student_browse');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_schools_student_view','modzzz_schools_student_browse');

DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('schools add student');

DELETE FROM `sys_acl_actions` WHERE `Name` IN('schools add student');


-- BEGIN REMOVE News

DROP TABLE IF EXISTS `[db_prefix]news_main`;
DROP TABLE IF EXISTS `[db_prefix]news_images`;
DROP TABLE IF EXISTS `[db_prefix]images`;
DROP TABLE IF EXISTS `[db_prefix]videos`;
DROP TABLE IF EXISTS `[db_prefix]sounds`;
DROP TABLE IF EXISTS `[db_prefix]files`;

DROP TABLE IF EXISTS `[db_prefix]news_rating`;
DROP TABLE IF EXISTS `[db_prefix]news_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]news_cmts`;
DROP TABLE IF EXISTS `[db_prefix]news_cmts_track`;

DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_schools_news';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_schools_news';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_schools_news';


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_schools_news_view','modzzz_schools_news_browse');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_schools_news_view','modzzz_schools_news_browse');

DELETE FROM `sys_pre_values` WHERE `Key`='ListingStyle';