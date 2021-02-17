<?php
bx_import('BxDolModule');
require_once ('SkSocialPostingResponse.php');
session_start();

class SkSocialPostingModule extends BxDolModule {

	var $_iVisitorID;
	var $_sModuleUri;

    function __construct(&$aModule) {
        parent::__construct($aModule);
		$this->_iVisitorID = (isMember()) ? (int) $_COOKIE['memberID'] : 0;
		$this->_sModuleUri = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
    }
    
	function serviceResponse($oAlert)
	{
		if(!$this->_iVisitorID)
			return;
		$oResponse = new SkSocialPostingResponse($this);
		$iAction = $oResponse->response($oAlert);
		if($iAction > 0)
		{
			$aHandleAction = $this->_oDb->getHandledByUnitAction($oAlert->sUnit, $oAlert->sAction);
			if($aHandleAction['enable'] == 0)
				return;
			
			$data = array(
				'unit' => $oAlert->sUnit,
				'action' => $oAlert->sAction,
				'order' => 1
			);
			
			$aUserConfigs = $this->_oDb->getAllUserSettings($this->_iVisitorID);
			$aSupportedNetworks = $this->_oDb->getSupportedNetworks();
			
			$iCheckNoAsk = 	$aUserConfigs['no_ask'][$aHandleAction['id']];
			$iAutoPublish = $aUserConfigs['auto_publish'][$aHandleAction['id']];
			
			$aData = $this->serviceGetActionData($iAction);
				
			if($iCheckNoAsk == 1)
			{
				if($iAutoPublish == 1)
				{
					foreach($aSupportedNetworks as $sNetwork)
					{
						if($aUserConfigs[$sNetwork['name']][$aHandleAction['id']] !== 0)
						{ 
							$aNetworkUser = $this->_oDb->getNetworkUserBy(array('profile_id' => $this->_iVisitorID,'network' => $sNetwork['name']));
							if(!empty($aNetworkUser)){
								if(!$this->isTokenExpired($aNetworkUser[0]['expired_time']))
									$this->postMessage($aNetworkUser[0],$aData['message']);
							}
						}
					}
					return;
				}
				else 
					return;
			}
			else
			{
				if($iAutoPublish == 1)
				{
					$iAllConnected = true;
					$aTemp = array();
					foreach($aSupportedNetworks as $sNetwork)
					{
						if($aUserConfigs[$sNetwork['name']][$aHandleAction['id']] !== 0)
						{ 
							$aNetworkUser = $this->_oDb->getNetworkUserBy(array('profile_id' => $this->_iVisitorID,'network' => $sNetwork['name']));
							if(empty($aNetworkUser)){
								$iAllConnected = false;
								break;
							}
							$aTemp[$sNetwork['name']] = $aNetworkUser[0];
						}
					}
					if($iAllConnected == true)
					{
						foreach($aSupportedNetworks as $sNetwork)
						{
							$this->postMessage($aTemp[$sNetwork['name']],$aData['message']);
						}
						return;
					}
				}
			}
			
			@setcookie('skposting_popup', '1', null,'/');
			$aData = array_merge($aData, array('handle_id' => $aHandleAction['id']));
			$aData['message'] = str_replace("'",'&#39;' , $aData['message']) ;  
			$session = BxDolSession::getInstance();
			$session->setValue('skposting_data', $aData);
			return;
		}
	}
    
	function actionLogin(){
	
		$this->checkLogged();
		
		if(bx_get('error') == 'login_failed' || (!bx_get('token') || !bx_get('network'))){
			exit($this->_oTemplate->parseHtmlByName('posting.html',array(
				'js_content' => "window.opener.showSocialPostingMes('"._t('_sk_social_posting_login_failed')."','error','0');
								window.close();"
				)
			));
		}
		
		$sToken = bx_get('token');
		$sNetwork = bx_get('network');
		$sExpiredTime = bx_get('expired_time') - 60;
		
		
		$aQueryParam = array(
			'token' => $sToken
		);
		
		$sSig = $this->signRequest($aQueryParam);
		$aQueryParam['sig'] = $sSig;
		
		$aRequestHeader = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($aQueryParam),
			)
		);
		
		$sUrl = $this->_oConfig->_sApiServiceUrl.'/user';
		
		$oContext = stream_context_create($aRequestHeader);
		
		$jResult = json_decode(file_get_contents($sUrl,false,$oContext), true);
        
        if(isset($jResult['error'])){
            exit($this->_oTemplate->parseHtmlByName('posting.html',array(
				'js_content' => "window.opener.showSocialPostingMes('".$jResult['error']."','error','0');
								window.close();"
				)));
        }
        
        if(isset($jResult['success']))
		  $aUserInfo = $jResult['result'];
		
		$sIdentity = ($sNetwork=='tumblr') ? $aUserInfo['profile_url'] : $aUserInfo['id'];
		
		$aExistedUser = $this->_oDb->getNetworkUserBy(array('identity' => $sIdentity));
		
		if(!empty($aExistedUser)){
		
			if(!empty($_SESSION['reconnect'][$sNetwork])){
				$this->_oDb->updateNetworkUser(array('expired_time' => $sExpiredTime,'token' => $sToken),$sIdentity);
				unset($_SESSION['reconnect'][$sNetwork]);
				$sImgSrc = $this->_oTemplate->getImageUrl('loading.gif');
				echo $this->_oTemplate->parseHtmlByName('posting.html',array(
					'js_content' => "
									window.opener.socialPostingHandleReconnecting('".$sNetwork."','".$aExistedUser[0]['username']."','".$sImgSrc."');
									window.close();
									"
					)
				);
			}else{
				echo $this->_oTemplate->parseHtmlByName('posting.html',array(
					'js_content' => "
									window.opener.showSocialPostingMes('"._t('_sk_social_posting_connected_account',$sNetwork)."','error','0');
									window.close();
									"
					)
				);
			}
			exit;
		}
		
		$aConnectedNetwork = $this->_oDb->getNetworkUserBy(array('network' => $sNetwork , 'profile_id' => $this->_iVisitorID));
		
		if(!empty($aConnectedNetwork)){
			echo $this->_oTemplate->parseHtmlByName('posting.html',array(
				'js_content' => "
								window.opener.showSocialPostingMes('"._t('_sk_social_posting_connected_network',$sNetwork)."','error','0');
								window.close();
								"
				)
			);
		}
		
		$aParam = array(
			'profile_id' => $this->_iVisitorID,
			'identity' => $sIdentity,
			'username' => empty($aUserInfo['username']) ? $this->genNetUsername($aUserInfo) : $aUserInfo['username'],
			'token' => $sToken,
			'expired_time' => $sExpiredTime,
			'network' => $sNetwork,
		);
		
		if(!$this->_oDb->createNetworkUser($aParam))
			exit($this->_oTemplate->parseHtmlByName('posting.html',array(
    			'js_content' => "
    							window.opener.showSocialPostingMes('"._t('_sk_social_posting_database_error',$sNetwork)."','error','0');
    							window.close();
    							"
    			))
    		);
            
		if(!empty($_SESSION['connect'][$sNetwork])){
			$sImgSrc = $this->_oTemplate->getImageUrl('loading.gif');
			echo $this->_oTemplate->parseHtmlByName('posting.html',array(
				'js_content' => "
								window.opener.socialPostingHandleReconnecting('".$sNetwork."','".$aParam['username']."','".$sImgSrc."');
								window.close();
								"
				)
			);
			unset($_SESSION['connect'][$sNetwork]);
			exit;
		} else {
            echo $this->_oTemplate->parseHtmlByName('posting.html',array(
    			'js_content' => "
    							window.opener.showSocialPostingMes('"._t('_sk_social_posting_connected',$sNetwork)."','message','1');
    							window.close();
    							"
    			)
    		);
            exit;
		}
	}

	function actionShowPostingForm(){
		$session = BxDolSession::getInstance();
		$aData = $session->getValue('skposting_data');
		
		$aSupportedNetwork = $this->_oDb->getSupportedNetworks();
		$aUserConfigs = $this->_oDb->getUserConfig($this->_iVisitorID);
		$sImgSrc = $this->_oTemplate->getImageUrl('loading.gif');
		
		foreach($aSupportedNetwork as $key=>$aNetwork){
		
			$aNetworkUser = $this->_oDb->getNetworkUserBy(array('network' => $aNetwork['name'] , 'profile_id' => $this->_iVisitorID));
			$login_url = $this->getLoginUrl($aNetwork['name']);
			// WILL CHECK TOKEN EXPIRED TIME LATER . IF TOKEN IS EXPIRED , USER NEED TO CONNECT NETWORK AGAIN
			
			if($this->isTokenExpired($aNetworkUser[0]['expired_time']))
				$_SESSION['reconnect'][$aNetwork['name']] = true;
			
			$aSupportedNetwork[$key]['checked'] = $aUserConfigs[$aNetwork['name']][$aData['handle_id']] == 1 ? 'checked="checked"' : '';
								
			$aSupportedNetwork[$key]['disabled'] = '';
			
			if(!empty($aNetworkUser)){
				$aSupportedNetwork[$key]['connect_link'] = ($this->isTokenExpired($aNetworkUser[0]['expired_time'])) ? 
								'Your session is expired (<a id="sk_connect_'.$aNetwork['name'].'" href="javascript:void(popupPosting(\''.$login_url.'\',\''.$sImgSrc.'\',false))">Reconnect</a>)': 
								'Connected as '.$aNetworkUser[0]['username'].' (<a id="sk_connect_'.$aNetwork['name'].'" href="javascript:void(socialPostingDisconnect(\''.$aNetwork['name'].'\',\''.$sImgSrc.'\'),false)">Disconnect</a>)';
				
			}
			else{
				$_SESSION['connect'][$aNetwork['name']] = true;
				$aSupportedNetwork[$key]['connect_link'] = '<a id="sk_connect_'.$aNetwork['name'].'" href="javascript:void(popupPosting(\''.$login_url.'\',\''.$sImgSrc.'\',false))">Connect</a>';
			}
			
			$aSupportedNetwork[$key]['img_src'] = $this->_oTemplate->getImageUrl($aNetwork['logo']);
			
		}
		
		$aVar = array(
			'bx_repeat:networks' => $aSupportedNetwork,
			'checked_auto_publish' => $aUserConfigs['auto_publish'][$aData['handle_id']] == 1 ? 'checked="checked"' : '',
			'checked_no_ask' => $aUserConfigs['no_ask'][$aData['handle_id']] == 1 ? 'checked="checked"' : '',
			'avatar' => get_member_thumbnail($this->_iVisitorID, 'none'),
			'posting_message' => $aData['message'],
		);
		
		$sContent = $this->_oTemplate->parseHtmlByName('posting_form.html',$aVar);
		
		$sCaption = _t('_sk_social_posting_posting_popup_caption');
		
		$sCaptionItem = <<<BLAH
		<div class="dbTopMenu">
			<i class="login_ajx_close sys-icon remove" onclick="skposting.not_publish()" style="cursor: pointer;"></i>
		</div>
BLAH;
		echo $GLOBALS['oFunctions']->transBox(DesignBoxContent($sCaption, $sContent, 11,$sCaptionItem),true);
		
	}
    
	function actionAjaxPost(){
		$session =  BxDolSession::getInstance();
		$aData = $session->getValue('skposting_data');
		if(!$aData)
			exit(_t('_sk_social_posting_error_1_time_only'));
		
		$aSupportedNetwork = $this->_oDb->getSupportedNetworks();
		$bFlag = true;
		foreach($aSupportedNetwork as $aNetwork){
			$iTemp = bx_get($aNetwork['name']);
			if(!isset($iTemp)){
				$bFlag = false;
				break;
			}
		}
		$iAutoPublish = bx_get('auto_publish');
		$iNoAsk = bx_get('no_ask');
		if($this->_iVisitorID > 0 && $bFlag && isset($iAutoPublish) && isset($iNoAsk))
		{
			$this->setUserSetting($this->_iVisitorID, 'auto_publish', $aData['handle_id'], $iAutoPublish);
			$this->setUserSetting($this->_iVisitorID, 'no_ask', $aData['handle_id'], $iNoAsk);
			$sMessage = bx_get('message');
			if($sMessage)
				$aData['message'] = $sMessage;
                
            $aStatus = array();
			foreach($aSupportedNetwork as $aNetwork)
			{   
				$i{$aNetwork['name']} = bx_get($aNetwork['name']);
				$this->setUserSetting($this->_iVisitorID, $aNetwork['name'], $aData['handle_id'], $i{$aNetwork['name']});
				if($i{$aNetwork['name']} == 1){
				    if($this->isLongMessage($sMessage,$aNetwork['name'])) {
    				    $aStatus[] = _t('_sk_social_posting_error_long_message', $aNetwork['name']);
    				    continue;
    				}
                    
					$aNetworkUser = $this->_oDb->getNetworkUserBy(array('profile_id' => $this->_iVisitorID,'network' => $aNetwork['name']));
					if(!empty($aNetworkUser) && !$this->isTokenExpired($aNetworkUser[0]['expired_time'])){
                        $sUrl = (!empty($aData['link'])) ? $aData['link'] : false;
                        $sResult = $this->postMessage($aNetworkUser[0], $sMessage, $sUrl);
                        if($sResult !== true)
                            $aStatus[] = $aNetwork['name'] . ": " . $sResult;
                        else $aStatus[] = _t('_sk_social_posting_success_posted', $aNetwork['name']);
					} else $aStatus[] = _t('_sk_social_posting_error_expired_connection', $aNetwork['name']);
				}
			}
			$session->unsetValue('skposting_data');
            
            $sRes = '';
            foreach($aStatus as $sStatus)
                $sRes .= $sStatus . "\n";
            
			echo $sRes; 
		} else echo _t('_sk_social_posting_error_post_fail');
	}
    
	function actionAdministration($sUrl = '')
	{
		if (!$this->isAdmin()) {
			$this->_oTemplate->displayAccessDenied ();
			return;
		}

		$this->_oTemplate->pageStart();
		$this->getScriptNoPopup();
		$aMenu = array(
            'manage_handler' => array(
                'title' => _t('_sk_social_posting_caption_manage_handlers'), 
                'href' => $this->_sModuleUri . 'administration/manage_handler', 
                '_func' => array ('name' => 'getAdministrationManageHandler', 'params' => array()),
			),
            'settings' => array(
                'title' => _t('_sk_social_posting_caption_settings'), 
                'href' => $this->_sModuleUri . 'administration/settings', 
                '_func' => array ('name' => 'getAdministrationSettings', 'params' => array()),
			),
		);

		if (empty($aMenu[$sUrl]))
			$sUrl = 'settings';

		$aMenu[$sUrl]['active'] = 1;
		$sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

		echo $this->_oTemplate->adminBlock ($sContent, _t('_sk_social_posting_title_admin'), $aMenu);

		$this->_oTemplate->pageCodeAdmin(_t('_sk_social_posting_page_admin'));
	}

	function getAdministrationManageHandler()
	{
		$this->updateSpyHandlers();
		BxDolAlerts::cache();
		
		$aModuleInfos = $this->getHandlerSupportedInfo();
		if(!$aModuleInfos)
			return MsgBox(_t('_sk_social_posting_text_no_module'));
		bx_import('BxTemplFormView');
		$aForm = array(
			'form_attrs' => array(
				'name' => 'update_form',
				'action' => '',
				'method' => 'post',
			),
			'params' => array (
				'db' => array(
					'submit_name' => 'update_form',
				),
			),
			'inputs' => array(
				'all_header' => array(
					'type' => 'block_header',
					'caption' => 'All Modules',
					'collapsable' => false,
                    'collapsed' => false,
				),
				'all' => array(
					'type' => 'checkbox_set',
					'caption' => 'All Actions',
					'name' => 'action_all',
					'values' => array(
						'enable_all' => _t('_sk_social_posting_label_enable'), 
						'default_all' => _t('_sk_social_posting_label_default_to_publish')
					),
				),
				'all_end' => array(
					'type' => 'block_end',
				),
			),
		);
		$aActionIDs = array();
		foreach($aModuleInfos as $aModuleInfo)
		{
			$sModuleName = $aModuleInfo['module'];
			$aActions = $aModuleInfo['action'];
			
			$aInputAction = array(
			); 
			foreach($aActions as $aAction)
			{
				if($sModuleName == 'profile' && $aAction['alert_action'] == 'join')
					continue;
				$aActionIDs[] = $aAction['id'];
				$aValue = array();
				if($aAction['enable'])
					$aValue[] = 'enable_' . $aAction['id'];
				if($aAction['default_post'])
					$aValue[] = 'default_' . $aAction['id'];
				$aInputAction[$sModuleName . '_' . $aAction['alert_action']] = array(
					'type' => 'checkbox_set',
					'caption' => ucwords(str_replace('_', ' ', $aAction['alert_action'])),
					'name' => 'action',
					'values' => array(
						'enable_' . $aAction['id'] => _t('_sk_social_posting_label_enable'), 
						'default_' . $aAction['id'] => _t('_sk_social_posting_label_default_to_publish')
					),
					'value' => $aValue,
				);
			}
			$aInputHeader = array(
				$sModuleName . '_header' => array(
					'type' => 'block_header',
					'caption' => 'Module ' . ucwords(str_replace('_', ' ', $sModuleName)),
					'collapsable' => true,
                    'collapsed' => false,
				)
			);
			$aInputEnd = array(
				$sModuleName . '_end' => array(
					'type' => 'block_end',
				)
			);
			$aInputModule = array_merge($aInputHeader, $aInputAction, $aInputEnd);
			$aForm['inputs'] = array_merge($aForm['inputs'], $aInputModule);	
		}
		
		$sActionIDs = implode($aActionIDs, ',');
		$sJsAdmin = <<<EOF
		<script type="text/javascript">
		$(function() {
			$('form#update_form input[name=\'action_all[]\']:eq(0)').change(
				function() {
					var sActionID = "$sActionIDs";
					var aActionIDs = sActionID.split(',');
					var sIsCheck = false;
					if($(this).is(':checked'))
						sIsCheck = true;
					for(var i in aActionIDs)
					{
						$('form#update_form input[name=\'action[]\'][value="enable_' + aActionIDs[i] + '"]').prop("checked", sIsCheck);
					}
				}
			);
			$('form#update_form input[name=\'action_all[]\']:eq(1)').change(
				function() {
					var sActionID = "$sActionIDs";
					var aActionIDs = sActionID.split(',');
					var sIsCheck = false;
					if($(this).is(':checked'))
						sIsCheck = true;
					for(var i in aActionIDs)
					{
						$('form#update_form input[name=\'action[]\'][value="default_' + aActionIDs[i] + '"]').prop("checked", sIsCheck);
					}
				}
			);
		});
		</script>
EOF;

		$aForm['inputs']['update_button'] = array(
			'type' => 'submit',
			'name' => 'update_form',
			'value' => _t('_sk_social_posting_button_update'),
		);
		$oForm = new BxTemplFormView($aForm);
		if($oForm->isSubmitted())
		{
			$aActionHandleds = bx_get('action');
			$aEnableActions = array();
			$aDefaultActions = array();
			if($aActionHandleds)
			{
				foreach($aActionHandleds as $sAction)
				{
					$aAction = explode('_', $sAction);
					if($aAction[0] == 'enable')
						$aEnableActions[] = $aAction[1];
					else if($aAction[0] == 'default')
						$aDefaultActions[] = $aAction[1];
				}
			}
			
			$this->_oDb->updateActionHandled($aEnableActions, $aDefaultActions);
			header('Location: ' . $this->_sModuleUri . 'administration/manage_handler?message=updated');
			exit;
		}
		
		if(bx_get('message') == 'updated')
			$sMessage = MsgBox(_t('_sk_social_posting_text_update_action_success'), 11);
		else
			$sMessage = MsgBox(_t('_sk_social_posting_text_choose_action_posted'));
		return $this->_oTemplate->parseHtmlByName('default_padding',array('content' => $sMessage . $oForm->getCode() . $sJsAdmin));
	}

	function getAdministrationSettings()
	{
		$iId = $this->_oDb->getSettingsCategory();
		if(empty($iId))
		return MsgBox(_t('_sys_request_page_not_found_cpt'));

		bx_import('BxDolAdminSettings');

		$mixedResult = '';
		if(isset($_POST['save']) && isset($_POST['cat']))
		{
			$oSettings = new BxDolAdminSettings($iId);
			$mixedResult = $oSettings->saveChanges($_POST);
		}

		$oSettings = new BxDolAdminSettings($iId);
		$sResult = $oSettings->getForm();
        
		if($mixedResult !== true && !empty($mixedResult))
		$sResult = $mixedResult . $sResult;
		echo DesignBoxAdmin(_t('_sk_social_posting_setting_info'), $this->_oTemplate->parseHtmlByName('default_padding.html', array(
			'content' => _t('_sk_social_posting_api_setting_info_content')
		)));
		return $this->_oTemplate->parseHtmlByName('default_padding',array('content'=>$sResult));
	}
	function actionSocialPostingSetting($sUrl = ''){
		$this->checkLogged();
		bx_import('PageSettings', $this->_aModule);
		$sClass = $this->_aModule['class_prefix'] . 'PageSettings';
		$oPage = new $sClass($this, $sUrl);
		$this->_oTemplate->pageStart();
		echo $oPage->getCode();
		$this->_oTemplate->pageCode(_t('_sk_social_posting_page_setting'), false, false);
	}
	function genBlockSettings($sUrl){
		//$this->getScriptNoPopup();
		switch($sUrl){
			case 'network_settings':
				$sTitle = _t('_sk_social_posting_block_caption_network_settings');
				$sContent = $this->getNetworkSettings();
			break;
			default:
				$sTitle = _t('_sk_social_posting_block_caption_connecting_networks');
				$sContent = $this->getNetworkConnect();
			break;
		}
		
		$aTopMenu = array(
			'Connecting Networks' => array(
				'href' => $this->_sModuleUri . 'social_posting_setting/network_connect', 
				'active' => $sTitle == 'Connecting Networks' ? 1 : 0, 
				'dynamic' => false),
			'Network Settings' => array(
				'href' => $this->_sModuleUri . 'social_posting_setting/network_settings', 
				'active' => $sTitle == 'Network Settings' ? 1 : 0, 
				'dynamic' => false)
		);
		
		return array($sContent,$aTopMenu);
	}
	function getNetworkConnect(){
		$aNetworks = $this->_oDb->getSupportedNetworks();
		$sImgSrc = $this->_oTemplate->getImageUrl('loading.gif');
		
		foreach($aNetworks as $key=>$value){
			$aNetworks[$key]['img_src'] = $this->_oTemplate->getImageUrl($value['logo']);
			$aNetworkUser = $this->_oDb->getNetworkUserBy(array('network' => $value['name'] , 'profile_id' => $this->_iVisitorID));
			$login_url = $this->getLoginUrl($value['name']);
			if(!empty($aNetworkUser)){
				if($this->isTokenExpired($aNetworkUser[0]['expired_time'])){
					$_SESSION['reconnect'][$value['name']] = true;
					$aNetworks[$key]['connect_link'] = 'Your session is expired (<a id="sk_connect_'.$value['name'].'" href="javascript:void(popupPosting(\''.$login_url.'\',\''.$sImgSrc.'\',true))">Reconnect</a>)';
				}else
					$aNetworks[$key]['connect_link'] = 'Connected as '.$aNetworkUser[0]['username'].' (<a id="sk_connect_'.$value['name'].'" href="javascript:void(socialPostingDisconnect(\''.$value['name'].'\',\''.$sImgSrc.'\',true))">Disconnect</a>)';
			}
			else{
				$aNetworks[$key]['connect_link'] = '<a id="sk_connect_'.$value['name'].'" href="javascript:void(popupPosting(\''.$login_url.'\',\''.$sImgSrc.'\',true))">Connect</a>';
			}
		}
		
		$aVar = array(
			'bx_repeat:networks' => $aNetworks,
		);
		
		return $this->_oTemplate->parseHtmlByName('connecting_networks.html',$aVar);
	}
	function getNetworkSettings(){
		$aModuleInfos = $this->getHandlerSupportedInfoUser();
		if(!$aModuleInfos)
			return MsgBox(_t('_sk_social_posting_text_no_module'));
		bx_import('BxTemplFormView');
		
		$aSupportedNetworks = $this->_oDb->getSupportedNetworks();
	
		$aValues = array();
		
		$aActionIDs = array();
		$aUserConfigs = $this->_oDb->getUserConfig($this->_iVisitorID);
		
		foreach($aSupportedNetworks as $key=>$value){
			$aSupportedNetworks[$key]['default_tab'] = ($key == 0 && !bx_get('tab')) ? 'default_tab' : '';
			$aForm[$value['name']] = $this->genNetworkSettingForm($aModuleInfos,$value['name'],$aUserConfigs,$aActionIDs);
		}
		
		$aForm['other'] = $this->genSettingForm($aModuleInfos,$aUserConfigs);
		
		$sActionIDs = implode($aActionIDs, ',');
		
		$sFormOutput = array();
		$iCount = 0;
		foreach($aForm as $sNetwork => $aData){
			$oForm[$sNetwork] = new BxTemplFormView($aData);
			if($oForm[$sNetwork]->isSubmitted())
			{
				$sHandledNetwork = bx_get('type_setting');
				if($sHandledNetwork == 'other'){
					$aHandlers['autopublish'] = array();
					$aHandlers['noask'] = array();
				}else
					$aHandlers[$sHandledNetwork] = array();
				$aActionHandleds = bx_get('action');
				if($aActionHandleds)
				{					
					foreach($aActionHandleds as $sAction)
					{
						$aAction = explode('_', $sAction);
						$aHandlers[$aAction[0]][] = $aAction[1];
					}
				}

				
				$this->_oDb->updateActionUserSettings($this->_iVisitorID, $aHandlers, $aActionIDs);
				header('Location: ' . $this->_sModuleUri . 'social_posting_setting/network_settings?message=updated&tab=' . $sHandledNetwork);
				exit;
			}
			
			$aFormOutput[$iCount]['html_form'] = $oForm[$sNetwork]->getCode();
			$aFormOutput[$iCount]['network'] = $sNetwork;
			$iCount++;
		}
		
		if(bx_get('message') == 'updated')
			$sMessage = MsgBox(_t('_sk_social_posting_text_update_action_success'), 10);

		if(bx_get('tab')){
			foreach($aSupportedNetworks as $key=>$value){
				if(bx_get('tab') == $value['name']){
					$aSupportedNetworks[$key]['default_tab'] = 'default_tab';
					break;
				}
			}
		}
			
		$aVar = array(
			'bx_repeat:networks' => $aSupportedNetworks,
			'bx_repeat:forms' => $aFormOutput,
			'other_default_tab' => bx_get('tab') == 'other' ? 'default_tab' : '',
            'other_text' => _t('_sk_social_posting_form_other_settings')
		);
		
		$sHtmlOutput = $this->_oTemplate->parseHtmlByName('network_settings.html',$aVar);
		
		return $sMessage . $sHtmlOutput;
		
	}
    
	function actionDisconnect(){
		if($this->_iVisitorID > 0 && bx_get('network')){
			if($this->_oDb->deleteNetworkUserBy(array('network' => bx_get('network'),'profile_id' => $this->_iVisitorID))){
				$aResponse['network'] = bx_get('network');
				$aResponse['url'] = $this->getLoginUrl(bx_get('network'));
                
                if(bx_get('setconnect') == '1')
                    $_SESSION['connect'][bx_get('network')] = true;
			}
			else
				$aResponse['error'] = _t('_sk_social_posting_database_error');
			echo json_encode($aResponse);
		}else{
			header('location:'.BX_DOL_URL_ROOT);
			exit;
		}
	}
	function getLoginUrl($network){
		$aQueryParams = http_build_query(array(
			'app_id' => $this->_oConfig->_sApiId,
			'callback' => urlencode($this->_sModuleUri.'login'),
			'scope' => 'user,publish',
		));
		return $this->_oConfig->_sApiLoginUrl.'/'.$network.'?'.$aQueryParams;
	}
	function actionSaveSettings(){
		$sNoAsk = bx_get('no_ask');
		$sAutoPublish = bx_get('auto_publish');
		
		if(empty($sNoAsk) || empty($sNoAsk)){
			header('location:'.BX_DOL_URL_ROOT);
			exit;
		}
		
		$aData = array(
			'no_ask' => $sNoAsk,
			'auto_publish' => $sAutoPublish,
		);
		$result = $this->_oDb->saveSettings($aData);
		if($result)
			$aResponse['message'] = _t('_sk_social_posting_save_setting');
		else
			$aResponse['error'] = _t('_sk_social_posting_database_error');
		echo json_encode($aResponse);
	}
	function actionGenPopupMessage(){
	
		$sMessage = bx_get('message');
		$sCapt = bx_get('caption');
		
		if(empty($sMessage) || empty($sCapt)){
			header('location:'.BX_DOL_URL_ROOT);
			exit;
		}
	
		$sContent = MsgBox($sMessage);
		
		$sCaption = $sCapt == 'message' ? _t('_sk_social_posting_message_caption') : _t('_sk_social_posting_error_caption');
		
		$sCaptionItem = <<<BLAH
		<div class="dbTopMenu">
			<i class="login_ajx_close sys-icon remove"></i>
		</div>
BLAH;
		echo $GLOBALS['oFunctions']->transBox(DesignBoxContent($sCaption, $sContent, 11,$sCaptionItem),true);
	}
	function serviceUpdateHandlers()
	{
		$this->updateSpyHandlers();
		$this->updateUnitHandlers();
		BxDolAlerts::cache();
	}

	function getAlertsByUnit($sUnit)
	{
		$sUnitAction = '_a' . str_replace(' ', '', ucwords(str_replace('_', ' ', $sUnit))) . 'Actions';
		$aAlert = array();
		foreach($this->_oConfig->{$sUnitAction} as $sAction)
		{
			$aAlert[] = array(
				'unit' => $sUnit,
				'action' => $sAction
			);
		}
		return $aAlert;
	}
	function updateUnitHandlers($bInstall = true)
	{
		foreach($this->_oConfig->_aUnits as $sUnit)
		{
			$aSystemAlerts = $this->getAlertsByUnit($sUnit);
			if(count($aSystemAlerts) == 0)
			continue;
			$aData = array(
				'handlers' => array(),
				'alerts' => array()
			);
			foreach($aSystemAlerts as $aSystemAlert)
			{
				$aData['handlers'][] = array(
					'alert_unit' => $aSystemAlert['unit'], 
					'alert_action' => $aSystemAlert['action'], 
					'module_uri' => $sUnit, 
					'module_method' => ''
				);
				$aData['alerts'][] = array(
					'unit' => $aSystemAlert['unit'], 
					'action' => $aSystemAlert['action']
				);
			}

			if($bInstall) {
				$this->_oDb->insertData($aData);
			}
			else {
				$this->_oDb->deleteData($aData);
			}
		}
	}
	function updateSpyHandlers($sModuleUri = 'all', $bInstall = true)
	{
		$aModules = $sModuleUri == 'all' ? $this->_oDb->getModules(): array( $this->_oDb->getModuleByUri($sModuleUri) );
		foreach($aModules as $aModule)
		{
            if(in_array($aModule['uri'], $this->_oConfig->_aModulesNotSupportedBySpy) == true)
                continue;
			if(!BxDolRequest::serviceExists($aModule, 'get_spy_data'))
				continue;
			$aData = @BxDolService::call($aModule['uri'], 'get_spy_data');
			if(empty($aData))
				continue;
			if(array_search($aData['handlers'][0]['alert_unit'], $this->_oConfig->_aModulesSupportedBySpy) == NULL)
				continue;
			if($bInstall) {
				$this->_oDb->insertData($aData);
			}
			else {
				$this->_oDb->deleteData($aData);
			}
		}
	}
	function serviceGetSettingLink()
	{
		$oMemberMenu = bx_instance('BxDolMemberMenu');
		$aLinkInfo = array(
                'item_img_src'  => null,
                'item_img_alt'  => null,
                'item_link'     => $this->_sModuleUri . 'social_posting_setting', 
                'item_onclick'  => null,
                'item_title'    => _t('_sk_social_posting_link_setting'),
                'extra_info'    => null,
		);
		return $oMemberMenu -> getGetExtraMenuLink($aLinkInfo);
	}
	function getHandlerSupportedInfo()
	{
		$aHandledModules = $this->_oDb->getHandledModules();
		
		$aModuleInfos = array();
		foreach($aHandledModules as $sModule)
		{
			$aActions = $this->_oDb->getActionByModule($sModule);
			$aModuleInfos[] = array(
				'module' => $sModule,
				'action' => $aActions,
			);
		}
		return $aModuleInfos;
	}
	
	function getHandlerSupportedInfoUser()
	{
		$aHandledModules = $this->_oDb->getHandledModulesUser();
		
		$aModuleInfos = array();
		foreach($aHandledModules as $sModule)
		{
			$aActions = $this->_oDb->getActionUserByModule($sModule);
			$aModuleInfos[] = array(
				'module' => $sModule,
				'action' => $aActions,
			);
		}
		return $aModuleInfos;
	}
	function getScriptNoPopup()
	{
		echo '
		<script type="text/javascript">
			var skpostingskip = true;
		</script>
		';
	}
	function isAdmin () {
		return isAdmin($this->_iVisitorID) || isModerator($this->_iVisitorID);
	}
	private function genNetworkSettingForm($aModuleInfos,$sNetworkName,$aUserConfigs,&$aActionIDs){
		$aValues['all_enable'] = _t('_sk_social_posting_form_select_all_label');
		
		$aForm = array(
			'form_attrs' => array(
				'name' => 'update_form_'.$sNetworkName,
				'action' => '',
				'method' => 'post',
			),
			'params' => array (
				'db' => array(
					'submit_name' => 'update_form_'.$sNetworkName,
				),
			),
			'inputs' => array(
				'all_header' => array(
					'type' => 'block_header',
					'caption' => ucfirst($sNetworkName),
					'collapsable' => false,
					'collapsed' => false,
				),
				'all' => array(
					'type' => 'checkbox_set',
					'name' => 'action_all',
					'dv' => '   ',
					'values' => $aValues,
				),
				'all_end' => array(
					'type' => 'block_end',
				),
				'type_setting' => array(
					'type' => 'hidden',
					'name' => 'type_setting',
					'value' => $sNetworkName,
				),
			),
		);
		$bFlag = false;
		if(!empty($aActionIDs))
			$bFlag = true;
		
		foreach($aModuleInfos as $aModuleInfo)
		{
			$sModuleName = $aModuleInfo['module'];
			$aActions = $aModuleInfo['action'];
			
			$aInputAction = array();
            $iCount = 1;
			foreach($aActions as $aAction)
			{
				if(!$bFlag)
					$aActionIDs[] = $aAction['id'];
				$aValue = array();
				$aValues = array();
				$sMethodName = ucwords(str_replace('_', ' ', $aAction['alert_action']));
				
				$aValues[$sNetworkName.'_'.$aAction['id']] = $sMethodName;
				
				if($aUserConfigs[$sNetworkName][$aAction['id']])
					$aValue[] = $sNetworkName.'_'.$aAction['id'];
				
				$aInputAction[$sModuleName . '_' . $aAction['alert_action']] = array(
					'type' => 'checkbox_set',
					'caption' => ($iCount === 1) ? _t('_sk_social_posting_form_method_caption') : '',
					'name' => 'action',
					'dv' => '   ',
					'values' => $aValues,
					'value' => $aValue,
				);
                $iCount++;
			}
			$aInputHeader = array(
				$sModuleName . '_header' => array(
					'type' => 'block_header',
					'caption' => 'Module ' .ucwords(str_replace('_',' ',$sModuleName)),
					'collapsable' => true,
                    'collapsed' => false,
				)
			);
			$aInputEnd = array(
				$sModuleName . '_end' => array(
					'type' => 'block_end',
				)
			);
			$aInputModule = array_merge($aInputHeader, $aInputAction, $aInputEnd);
			$aForm['inputs'] = array_merge($aForm['inputs'], $aInputModule);
		}
		$aForm['inputs']['update_button'] = array(
			'type' => 'submit',
			'name' => 'update_form_'.$sNetworkName,
			'value' => _t('_sk_social_posting_button_update'),
		);
		
		return $aForm;
		
	}
    
	private function genSettingForm($aModuleInfos,$aUserConfigs){
		$aValues['all_auto_publish'] = _t('_sk_social_posting_form_auto_post_caption');
		$aValues['all_no_ask'] = _t('_sk_social_posting_form_no_ask_caption');
		
		$aForm = array(
			'form_attrs' => array(
				'name' => 'update_form_setting',
				'action' => '',
				'method' => 'post',
			),
			'params' => array (
				'db' => array(
					'submit_name' => 'update_form_setting',
				),
			),
			'inputs' => array(
				'all_header' => array(
					'type' => 'block_header',
					'caption' => _t('_sk_social_posting_form_other_settings'),
					'collapsable' => false,
					'collapsed' => false,
				),
				'all' => array(
					'type' => 'checkbox_set',
					'caption' => _t('_sk_social_posting_form_select_all_label'),
					'name' => 'action_all',
					'dv' => '   ',
					'values' => $aValues,
				),
				'all_end' => array(
					'type' => 'block_end',
				),
				'type_setting' => array(
					'type' => 'hidden',
					'name' => 'type_setting',
					'value' => 'other',
				),
			),
		);
		
		foreach($aModuleInfos as $aModuleInfo)
		{
			$sModuleName = $aModuleInfo['module'];
			$aActions = $aModuleInfo['action'];
			
			$aInputAction = array();
			
			foreach($aActions as $aAction)
			{
				$aValue = array();
				$aValues = array();
				$sMethodName = ucwords(str_replace('_', ' ', $aAction['alert_action']));
				
				$aValues['autopublish_' . $aAction['id']] = _t('_sk_social_posting_form_auto_post_caption');
				$aValues['noask_' . $aAction['id']] = _t('_sk_social_posting_form_no_ask_caption');
				
				if($aUserConfigs['auto_publish'][$aAction['id']])
					$aValue[] = 'autopublish_' . $aAction['id'];
				if($aUserConfigs['no_ask'][$aAction['id']])	
					$aValue[] = 'noask_' . $aAction['id'];
				
				$aInputAction[$sModuleName . '_' . $aAction['alert_action']] = array(
					'type' => 'checkbox_set',
					'caption' => _t('_sk_social_posting_form_method_caption_other_settings') . $sMethodName . ":",
					'name' => 'action',
					'dv' => '   ',
					'values' => $aValues,
					'value' => $aValue,
				);
			}
			$aInputHeader = array(
				$sModuleName . '_header' => array(
					'type' => 'block_header',
					'caption' => 'Module ' .ucwords(str_replace('_',' ',$sModuleName)),
					'collapsable' => true,
                    'collapsed' => false,
				)
			);
			$aInputEnd = array(
				$sModuleName . '_end' => array(
					'type' => 'block_end',
				)
			);
			$aInputModule = array_merge($aInputHeader, $aInputAction, $aInputEnd);
			$aForm['inputs'] = array_merge($aForm['inputs'], $aInputModule);
		}
		$aForm['inputs']['update_button'] = array(
			'type' => 'submit',
			'name' => 'update_form_setting',
			'value' => _t('_sk_social_posting_button_update'),
		);
		
		return $aForm;
		
	}
    
	private function checkLogged(){
		if($this->_iVisitorID <= 0)
		{
			$sUrl = BX_DOL_URL_ROOT;
			header("Location: {$sUrl}");
			exit;
		}
	}
    
	private function genNetUsername($aData){
		if(!empty($aData['full_name']))
			return $aData['full_name'];
		if(!empty($aData['display_name']))
			return $aData['display_name'];
		if(!empty($aData['email']))
			return strstr($aData['email'],'@',true);
		return 'no_name';
	}
	function serviceGetActionData($iActionID)
	{
		$aAction = $this->_oDb->getActionByActionID($iActionID);
		if($aAction)
		{
			$aParams = unserialize($aAction['params']);
			$sMessage = $this->_parseParameters(_t($aAction['action_key']), $aParams);
			$sMessage = strip_tags($sMessage);
			$sLink = $aAction['link'];
			$aData = array(
				'message' => $sMessage,
				'link' => $sLink
			);
			return $aData;
		}
		return NULL;
	}
	function _parseParameters($sKey, &$aParameters)
	{
		if( $aParameters and is_array($aParameters) ) 
			foreach($aParameters as $sArrayKey => $aItems)
				$sKey = str_replace('{' . $sArrayKey . '}', $aParameters[$sArrayKey], $sKey);
		return $sKey;
	}
	function setUserSetting($iMemberID, $sSettingName, $iHandleID, $iConfigValue)
	{
		$aConfigs = $this->_oDb->getAllUserSettings($iMemberID);
		$aConfigs[$sSettingName][$iHandleID] = (int)$iConfigValue;
		$this->_oDb->setAllUserSettings($iMemberID, $aConfigs);
	}
    
	private function postMessage($aData, $sMessage, $sLink = false){
		$aQueryParam = array(
			'token' => $aData['token'],
			'message' =>  $sMessage . (($sLink !== false) ? ". " . $sLink : "")
		);
        
        if($sLink !== false)
            $aQueryParam['link'] = $sLink;
		
		$sSig = $this->signRequest($aQueryParam);
		$aQueryParam['sig'] = $sSig;
		
		$aContextParam = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($aQueryParam),
			)
		);
		$sUrl = $this->_oConfig->_sApiServiceUrl.'/publish';
		
		$context = stream_context_create($aContextParam);
        
        $jResult = json_decode(file_get_contents($sUrl,false,$context),true);
        
        return isset($jResult['success']) ? true : $jResult['error'];
	}
    
	private function isTokenExpired($iExpiredTime){
		if($iExpiredTime - time() <= 0)
			return true;
		return false;
	}
    
	private function isLongMessage($sMessage,$sNetwork){
		switch($sNetwork){
			case 'plurk':
				if(strlen($sMessage) >= 210)
					return true;
			case 'mailru':
				if(strlen($sMessage) >= 400)
					return true;
			case 'tumblr':
				return false;
			case 'lastfm':
				if(strlen($sMessage) >= 1000)
					return true;
			case 'twitter':
				if(strlen($sMessage) >= 140)
					return true;
			case 'linkedin':
				if(strlen($sMessage) >= 700)
					return true;
			case 'facebook':
				if(strlen($sMessage) >= 420)
					return true;
		}
		return false;
	}
	private function signRequest($data){
	
		ksort($data);
	
		$str_data = '';
		
		foreach($data as $key=>$value)
			$str_data .= "$key=$value";
	
		return md5($this->_oConfig->_sSecretKey.$str_data);
	}
    
    public function actionShowLoadingImg() {
        $sLoadingImg = bx_get('img_src');
        echo '<div id="sk_login_loading"><img src="' . $sLoadingImg . '"/></div>'; 
    }
    
    public function serviceDeleteProfileConnected($iProfileId) {
        if (!(int)$iProfileId)
            return false;
            
        $this->_oDb->deleteProfileData($iProfileId);
        return true;
    }
}

?>
