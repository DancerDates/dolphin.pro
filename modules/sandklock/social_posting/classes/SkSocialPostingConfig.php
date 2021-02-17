<?php
bx_import('BxDolConfig');

class SkSocialPostingConfig extends BxDolConfig {

	var $_aUnits;
	var $_aProfileActions;
	var $_aFriendActions;
	var $_aModulesSupportedBySpy;
    var $_aModulesNotSupportedBySpy;
	var $_aUrlSpyUnit;
	var $_aSupportSocialNetworks;
	var $_sFacebookAppID;
	var $_sFacebookAppKey;
	var $_sFacebookAppSecret;
	var $_sTwitterConsumerKey;
	var $_sTwitterConsumerSecret;
	var $_sLinkedInAppKey;
	var $_sLinkedInAppSecret;
	var $_sApiId;
	var $_sApiLoginUrl;
	var $_sApiServiceUrl;
	var $_sSecretKey;
	
	/*
	* Constructor.
	*/
	function __construct($aModule) {
		
		parent::__construct($aModule);
		
		$this->_sApiId = getParam('sk_posting_app_api_id');
		$this->_sSecretKey = getParam('sk_posting_app_api_secret');
		$this->_sApiLoginUrl = getParam('sk_posting_app_api_login_url');
		$this->_sApiServiceUrl = getParam('sk_posting_app_api_service_url');
		
		$this->_aUnits = array('profile'); //friend not supported , not support bx_wall , it causes conflict
		//$this->_aBxWallActions = array('update');
		$this->_aSupportedSetting = array('auto_publish' , 'no_ask');
		$this->_aProfileActions = array('join', 'edit', 'edit_status_message');
		$this->_aModulesSupportedBySpy  = array(
			'ads', 			'bx_blogs', 	'blogposts', 	'bx_events', 	'bx_files', 
			'bx_photos', 	'bx_groups', 	'bx_sites', 	'bx_sounds', 	'bx_store',
			'bx_poll',		'bx_videos', 	'bx_map',		
		);
        $this->_aModulesNotSupportedBySpy = array(
            'facebook_connect'
        );
		$this->_aUrlSpyUnit = array(
			'ads' => 'ads_url',
			'bx_blogs' => 'post_url',
			'blogposts' => 'post_url',
			'bx_events' => 'entry_url',
			'bx_files' => 'entry_url',
			'bx_photos' => 'entry_url',
			'bx_groups' => 'entry_url',
			'bx_sites' => 'site_url',
			'bx_sounds' => 'entry_url',
			'bx_store' => 'entry_url',
			'bx_poll' => 'poll_url',
			'bx_videos' => 'entry_url',
		);
	}
	
	function getAlertSystemName()
	{
		return 'sk_social_posting_content';
	}

}