-- tables
DROP TABLE IF EXISTS `dbcs_facebook_connect_data`;

    -- page compose pages
    DELETE FROM `sys_page_compose_pages` WHERE `Name`='profile_info_required';

    -- page compose blocks
    DELETE FROM `sys_page_compose` WHERE `Desc`='Profile Info Required';
 
    --
    -- Dumping data for table `sys_objects_auths`
    --

    DELETE FROM 
        `sys_objects_auths` 
    WHERE    
        `Title` = '_dbcs_facebook';
 
    --
    -- `sys_alerts_handlers` ;
    --

    SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers`  WHERE `name`  =  'dbcs_facebook_connect');

    DELETE FROM
        `sys_alerts_handlers`
    WHERE
        `id`  = @iHandlerId;

    --
    -- `sys_alerts` ;
    --

    DELETE FROM 
        `sys_alerts`
    WHERE
        `handler_id` =  @iHandlerId ;

-- Profiles
ALTER TABLE `Profiles` DROP `dbcsFacebookProfile`;


    -- 
    -- `sys_menu_admin`;
    --

    DELETE FROM 
        `sys_menu_admin` 
    WHERE
        `title` = '_dbcs_facebook';

--
-- `sys_options_cats` ;
-- Standard Settings

SET @iKategId = (SELECT `id` FROM `sys_options_cats` WHERE `name` = 'Deanos Facebook Connect' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `id` = @iKategId;
DELETE FROM `sys_options` WHERE `kateg` = @iKategId;
-- this option could have a cat id of 0. So remove it by name.
DELETE FROM `sys_options` WHERE `Name` = 'deanos_facebook_connect_prompt_profile_type';

--
-- permalink
--

DELETE FROM `sys_permalinks` WHERE `standard`  = 'modules/?r=deanos_facebook_connect/';
DELETE FROM `sys_options` WHERE `Name` = 'dbcs_facebook_connect_permalinks' AND `kateg` = 26;

-- Profile fields
DELETE FROM `sys_profile_fields` WHERE `Name` = 'dbcsFacebookProfile';



-- sys_injections
DELETE FROM `sys_injections` WHERE `Name` = 'profile_info_required';
DELETE FROM `sys_injections` WHERE `Name` = 'dbcs_fbc_popup_code';
DELETE FROM `sys_injections` WHERE `Name` = 'dbcs_fbc_js_button_code';
DELETE FROM `sys_injections` WHERE `Name` = 'dbcs_fbc_button_large';
DELETE FROM `sys_injections` WHERE `Name` = 'dbcs_fbc_button_large_nm';
DELETE FROM `sys_injections` WHERE `Name` = 'dbcs_fbc_button_small';
DELETE FROM `sys_injections` WHERE `Name` = 'dbcs_fbc_button_small_mr';
DELETE FROM `sys_injections` WHERE `Name` = 'dbcs_fbc_button_small_nm';
DELETE FROM `sys_injections` WHERE `Name` = 'dbcs_fbc_button_small_ns';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 't_dbcs_FaceBookJoined';
DELETE FROM `sys_email_templates` WHERE `Name` = 't_dbcs_FaceBookUnconfirmed';
