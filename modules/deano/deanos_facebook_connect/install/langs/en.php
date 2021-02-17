<?php
/***************************************************************************
* Date				: Feb 21, 2013
* Copywrite			: (c) 2013 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Facebook Connect
* Product Version	: 4.2.7
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/
	$sLangCategory = 'Deanos Facebook Connect';


    $aLangContent = array
    (
        '_dbcs_facebook'                         => 'Deanos FBC',
        '_dbcs_facebook_settings'                => 'Facebook connect settings',
        '_dbcs_facebook_information'             => 'Facebook connect information',
        '_dbcs_facebook_information_block'       => 'NOTE: Facebook connect requires a Facebook application. To create one or to get the keys to your existing application, go to <a href="http://www.facebook.com/developers/">http://www.facebook.com/developers/</a><br><br>To make sure you have your new Facebook Application properly setup for use with Facebook Connect, refer to the screen shots here. <a href="http://www.deanbassett.com/images/facebook_screenshots/">http://www.deanbassett.com/images/facebook_screenshots/</a><br><br>After completing these steps, insert your Application ID and Secret strings below:<br><br>A dolphin logon id will be selected from the Facebook fields in the order selected below. The first one is used by default and if that id is already in use, the next one in sequence is used.<br>&nbsp;',
        '_dbcs_facebook_error_occured'           => 'Error occurred',
        '_dbcs_facebook_profile_exist'           => 'Sorry, but profile {0} already exists',
        '_dbcs_facebook_profile_not_defined'     => 'Sorry, but profile not defined',
        '_dbcs_facebook_profile_error_info'      => 'Cant\'t define your profile information',
        '_dbcs_facebook_profile_error_api_keys'  => 'Please configure the module in admin panel (can\'t define facebook api keys)',
        '_dbcs_fbc_Restore'                    => 'Restore Backup',
        '_dbcs_fbc_Backup'                     => 'Create Backup',
        '_dbcs_fb_No Backups Found'            => 'No Backup Files Found',
        '_dbcs_fb_Backup Saved'                => 'Backup Saved',
        '_dbcs_fb_Backup Restored'             => 'Backup Restored',
        '_dbcs_fb_Backup Deleted'              => 'Backup Deleted',
        '_FieldCaption_dbcsFacebookProfile_Edit'   => 'Facebook Profile ID',
        '_FieldCaption_dbcsFacebookProfile_Join'   => 'Facebook Profile ID',
        '_FieldCaption_dbcsFacebookProfile_View'   => 'Facebook Profile ID',
        '_FieldCaption_dbcsFacebookProfile_Search'   => 'Facebook Profile ID',
        '_dbcs_FC_profile_info_required'   => 'Profile Check',
        '_dbcs_FC_profile_info_required_msg'   => 'Your profile is missing some required profile information. Please complete your profile.',
        '_dbcs_FC_profile_info_required_link'   => 'Go to profile editor',

        '_dbcs_fbc_enter_password'   => 'Enter Password',

        '_dbcs_fbc_pass_msg'   => 'Welcome. Thank you for signing up with us. Most of the information needed for your new account with us has been pulled from Facebook. However you will need to enter a password to use with your new account so you may login using our sites standard logon form.<br /><br />Please enter a password below.',
        '_dbcs_fbc_nickname'   => 'Logon ID',
        '_dbcs_fbc_password'   => 'Password',
        '_dbcs_fbc_confirm_password'   => 'Confirm Password',
        '_dbcs_fbc_email'   => 'Email Address',
		'_dbcs_fbc_sex' => 'Sex',
		'_dbcs_fbc_dob' => 'Date of Birth',
		'_dbcs_fbc_country' => 'Country',
		'_dbcs_fbc_city' => 'City',
		'_dbcs_fbc_zip' => 'Zip Code',

        '_dbcs_fbc_submit'   => 'Submit',
        '_dbcs_facebook_member_photos'   => 'Facebook Member Photos',
        '_dbcs_facebook_join_feature_unavailable'   => 'The feature to pass facebook values to the dolphin join form is not available on<br />Dolphin version 7.0.0<br /><br />This feature is only available on Dolphin versions 7.0.1 and higher.<br /><br />Please disable this feature in the settings for facebook connect, or upgrade to a newer version of Dolphin.',
        '_dbcs_permissions_error' => 'Bad Folder Permissions.',
        '_dbcs_permissions_error_msg' => 'Temporary data needs to be saved during the Facebook signup process. Facebook connect cannot store it\'s temporary data because the tmp folder does not appear to have write permissions.',
        '_dbcs_fb_ask_import' => 'Would you like to import your Facebook photo albums to this site?',
        '_dbcs_fb_ask_import_header' => 'Import Albums',
        '_dbcs_fb_importing_msg' => 'Importing your Facebook photo albums. One moment please...',
        '_dbcs_fb_importing_header' => 'Importing Albums',
        '_dbcs_fb_finish_header' => 'Account Created',
        '_dbcs_fb_finish_msg' => 'Your account has been created. Thank you for joining via Facebook Connect.',
        '_dbcs_fb_logon_label' => 'Your Logon ID:',
        '_dbcs_fb_password_label' => 'Your Password:',
        '_dbcs_fb_ip_banned' => 'Sorry. Your IP address has been banned.<br><br>Signup process aborted.',
        '_dbcs_fb_erh' => 'Error. Missing PHP Extension.',
        '_dbcs_fb_erc' => 'Facebook Connect requires the CURL PHP extension.<br><br>Check with your host to make sure it\'s installed.',
        '_dbcs_fb_erj' => 'Facebook Connect requires the JSON PHP extension.<br><br>Check with your host to make sure it\'s installed.',
        '_dbcs_fb_curl_test_failed' => 'Test for proper operstion of curl failed with the following message.<br /><br />{0}<br /><br />Contact your host.',
        '_dbcs_fb_dns_test_failed' => 'Servers DNS does not appear to be functioning.<br /><br />Could not resolve domain graph.facebook.com<br /><br />Contact your host',
        '_dbcs_fb_min_age_error' => 'Sorry. This site is restricted to {0} years of age and up.',
        '_dbcs_fb_invalid_email' => 'Sorry. Email address of {0} obtained from facebook is not a valid email address.<br><br>Signup process aborted.',
        '_dbcs_fbc_prompt' => 'Additional information required to complete signup.',
        '_dbcs_fbc_prompt_nick_msg' => 'A free nickname could not be automatically generated. Please choose a nickname to use on this website.',
        '_dbcs_fbc_prompt_email_msg' => 'The email address obtained from facebook is not valid. Please enter a valid email address for use on this website.',
        '_dbcs_fbc_default_headline' => '',
        '_dbcs_fbc_default_about_me' => '',
        '_dbcs_fbc_button_text_lg' => 'Connect with Facebook',
        '_dbcs_fbc_button_text_sm' => 'Connect',
        '_dbcs_fbc_button_continue' => 'Continue',
        '_dbcs_fbc_onemoment' => 'Creating your account. One moment please.',
        '_dbcs_fbc_login_popup_text' => 'One moment please.',
        '_dbcs_fbc_profile_type' => 'Profile Type',
        '_dbcs_fbc_yes' => 'Yes',
        '_dbcs_fbc_no' => 'No',
        '_dbcs_fbc_profile_photo' => 'Profile Photo',
		'_dbcs_fbc_FieldError_City_Mandatory' => 'You must specify your city',
		'_dbcs_fbc_FieldError_City_Max' => 'Please, enter {0} character(s) or less',
		'_dbcs_fbc_FieldError_City_Min' => 'Please, enter {0} character(s) or more',
		'_dbcs_fbc_FieldError_Country_Mandatory' => 'You must specify your country',
		'_dbcs_fbc_FieldError_DateOfBirth_Mandatory' => 'You must specify your birth date',
		'_dbcs_fbc_FieldError_DateOfBirth_Max' => 'You cannot join the site if you are older than {0} years',
		'_dbcs_fbc_FieldError_DateOfBirth_Min' => 'You cannot join the site if you are younger than {0} years',
		'_dbcs_fbc_FieldError_Email_Check' => 'Please enter correct e-mail',
		'_dbcs_fbc_FieldError_Email_Mandatory' => 'E-mail address is required',
		'_dbcs_fbc_FieldError_Email_Min' => 'Your email is too short',
		'_dbcs_fbc_FieldError_Email_Unique' => 'Account with this email already exists.',
		'_dbcs_fbc_FieldError_NickName_Check' => 'Your Username must contain only latin symbols, numbers or underscore ( _ ) or minus ( - ) signs',
		'_dbcs_fbc_FieldError_NickName_Mandatory' => 'You must enter Username',
		'_dbcs_fbc_FieldError_NickName_Max' => 'Your Username should be no longer than {0} characters long',
		'_dbcs_fbc_FieldError_NickName_Min' => 'Your Username must be at least {0} characters long',
		'_dbcs_fbc_FieldError_NickName_Unique' => 'This Username is already in use by another member. Please select another Username.',
		'_dbcs_fbc_FieldError_Password_Mandatory' => 'Password is required',
		'_dbcs_fbc_FieldError_Password_Max' => 'Your password should be no longer than {0} characters',
		'_dbcs_fbc_FieldError_Password_Min' => 'Your password must be at least {0} characters long',
		'_dbcs_fbc_FieldError_Sex_Mandatory' => 'Please, specify your gender',
		'_dbcs_fbc_FieldError_zip_Mandatory' => 'Please, specify your ZIP code',
		'_dbcs_fbc_FieldError_zip_Max' => 'Please, enter {0} character(s) or less',
		'_dbcs_fbc_FieldError_zip_Min' => 'Please, enter {0} character(s) or more',
		'_dbcs_fbc_FieldDesc_Profiletype_Join' => 'Select a profile type.',
		'_dbcs_fbc_FieldDesc_NickName_Join' => 'Choose a Username which will be used for logging in to the site',
		'_dbcs_fbc_FieldDesc_Password_Join' => 'Choose a password.',
		'_dbcs_fbc_FieldDesc_Password_Join_Confirm' => 'Confirm Password',
		'_dbcs_fbc_FieldDesc_Email_Join' => 'Enter your Email. Your password will be sent to this email.',
		'_dbcs_fbc_FieldDesc_DateOfBirth_Join' => 'Please specify your birth date: Year-Month-Day',
		'_dbcs_fbc_FieldDesc_Sex_Join' => 'Please specify your gender',
		'_dbcs_fbc_FieldDesc_Country_Join' => 'Select the country where you live ',
		'_dbcs_fbc_FieldDesc_City_Join' => 'Specify the city where you live ',
		'_dbcs_fbc_FieldDesc_zip_Join' => 'Enter your Post code (Zip)',

    );