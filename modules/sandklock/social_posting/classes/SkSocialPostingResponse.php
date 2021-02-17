<?php
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');

class SkSocialPostingResponse extends BxDolAlertsResponse {
	var $_oModule;

	/**
	 * Constructor
	 * @param  SkSocialPosting $oModule - an instance of current module
	 */
	function __construct($oModule) {
		parent::__construct();
		$this->_oModule = $oModule;
	}

	function reflect($oAlert){
		$iSender_id = $oAlert->iSender;
		$aAction = $this->director($oAlert);
		if($aAction)
		{
			if($aAction['params'])
			{
				$aSearch = array('\\\\', "\'", '\"');
				$aReplace = array('&#92;', '&#39;', '&#34;');
				foreach($aAction['params'] as $key => $value)
				{
					$aAction['params'][$key] = str_replace($aSearch, $aReplace, htmlspecialchars($value));
				}
			}
			$iActionID = $this->_oModule->_oDb->insertEvent($iSender_id, $aAction); 
			if($iActionID > 0)
			{
				return $iActionID;
			}
		}
		return 0;
	}

	function director($oAlert){
		$sUnit =  $oAlert->sUnit;
		$method_name =  'inflect_'. $sUnit;
		$rest =  NULL;
		if($this->spy_supported($sUnit))
		{
			return $this->inflect_spy($oAlert);
		}
		else if(method_exists($this, $method_name))
		{
			$rest = $this->{$method_name}($oAlert);
		}
		return $rest;
	}

	function spy_supported($sUnit){
		return array_search($sUnit, $this->_oModule->_oConfig->_aModulesSupportedBySpy) > -1;
	}
	
	function profile_supported($sAction){
		return array_search($sAction, $this->_oModule->_oConfig->_aProfileActions) > -1;
	}
	
	function inflect_bx_wall($oAlert)
	{
		$sLangKey = '_sk_social_posting_wall_update';
		$aProfileInfo = getProfileInfoDirect($this->_oModule->_iVisitorID);
		$aParam = array(
			'profile_nick' => $aProfileInfo['NickName'],
			'sex' => $aProfileInfo['Sex'] == 'female' ? 'her' : 'his',
		);
		$aAction = array(
			'lang_key' => $sLangKey,
			'params' => $aParam,
			'link' => BX_DOL_URL_ROOT . 'm/wall/index/' . getNickName($this->_oModule->_iVisitorID)
		);
		return $aAction;
	}
	
	function inflect_profile($oAlert)
	{
		$sAction =  $oAlert->sAction;
		$aAction = NULL;
		if($this->profile_supported($sAction))
		{
			$iSender_id =  $oAlert->iSender;
			$sLangKey = '_sk_social_posting_profile_' . $sAction;
			$aExtraParams = $oAlert->aExtras;
			$aParam = array();
			switch ($sAction)
			{
				case 'join':
					bx_import('BxDolMailBox');
					$sMessageSubject = (getParam('sk_posting_subject_message_sign_up') == '') 
										? _t('_sk_social_posting_subject_new_user_join') 
										: getParam('sk_posting_subject_message_sign_up');
					$sMessageBody = (getParam('sk_posting_content_message_sign_up') == '')
										?_t('_sk_social_posting_message_new_user_join')
										: getParam('sk_posting_content_message_sign_up');
					$vRecipientID = $oAlert->iObject;
					$aMailBoxSetting = array(
						'member_id' => 1,
						'recipient_id' => $vRecipientID,
					);
					$oMailBox = new BxDolMailBox('', $aMailBoxSetting);
					$aComposeSettings = array
				    (
				    	'send_copy' => false,				
				    	'send_copy_to_me' => false ,
				    	'notification' => false
				    );
					$oMailBox->sendMessage($sMessageSubject, $sMessageBody, $vRecipientID, $aComposeSettings);
				return NULL;
				case 'edit':
					$aProfileInfo = getProfileInfoDirect($iSender_id);
					$aParam['profile_link'] = BX_DOL_URL_ROOT . $aProfileInfo['NickName'];
					$aParam['profile_nick'] = $aProfileInfo['NickName'];
					$aParam['sex'] = $aProfileInfo['Sex'] == 'female' ? 'her' : 'his';
				break;
				case 'edit_status_message':
					$aProfileInfo = getProfileInfoDirect($iSender_id);
					$aParam['profile_link'] = BX_DOL_URL_ROOT . $aProfileInfo['NickName'];
					$aParam['profile_nick'] = $aProfileInfo['NickName'];
					$aParam['sex'] = $aProfileInfo['Sex'] == 'female' ? 'her' : 'his';
					$aParam['new_message'] = $aExtraParams[0];
					if($aParam['new_message'] == '')
						return NULL;
				break;
			}
			
			$aAction = array(
				'lang_key' => $sLangKey,
				'params' => $aParam,
				'link' => BX_DOL_URL_ROOT . getNickName($this->_oModule->_iVisitorID)
			);
		}
		return $aAction;
	}

	function inflect_spy($oAlert)
	{
		$aResult = NULL;
		$aInternalHandlers = $this->_oModule->_oDb->getInternalHandlers();
		$_aInternalHandlers = array();
		// procces all recived handlers;
		if($aInternalHandlers && is_array($aInternalHandlers) ) {
			foreach($aInternalHandlers as $iKey => $aItems)
			{
				$_aInternalHandlers[ $aItems['alert_unit'] . '_' . $aItems['alert_action'] ] = $aItems;
			}
		}
		$sKey = $oAlert->sUnit . '_' . $oAlert -> sAction;

		// call defined method;
		if( array_key_exists($sKey, $_aInternalHandlers) ) {
			if( BxDolRequest::serviceExists($_aInternalHandlers[$sKey]['module_uri'], $_aInternalHandlers[$sKey]['module_method']) ) {
				// define functions parameters;
				$aParams = array(
                        'action'       => $oAlert -> sAction,
                        'object_id'    => $oAlert -> iObject,
                        'sender_id'    => $oAlert -> iSender,
                        'extra_params' => $oAlert -> aExtras,
				);
				$aResult = BxDolService::call($_aInternalHandlers[$sKey]['module_uri'], $_aInternalHandlers[$sKey]['module_method'], $aParams);
				if($aResult)
				{
					if($oAlert->sUnit == 'bx_map')
						$aResult = array_merge($aResult, array('link' => BX_DOL_URL_ROOT . getNickName($this->_oModule->_iVisitorID)));
					else if($oAlert->sUnit == 'bx_sites')
						$aResult = array_merge($aResult, array('link' => BX_DOL_URL_ROOT . $aResult['params'][$this->_oModule->_oConfig->_aUrlSpyUnit[$oAlert->sUnit]]));
					else 
						$aResult = array_merge($aResult, array('link' => $aResult['params'][$this->_oModule->_oConfig->_aUrlSpyUnit[$oAlert->sUnit]]));
					return $aResult;
				}
			}
		}
		return $aResult;
	}

	/**
	 * Overwtire the method of parent class.
	 *
	 * @param BxDolAlerts $oAlert an instance of alert.
	 */
	function response($oAlert){
		return $this->reflect($oAlert);
	}
}
?>