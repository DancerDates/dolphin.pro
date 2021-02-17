<?php

bx_import('BxDolModule');

class SkSocialLoginModule extends BxDolModule {

	var $_iVisitorID;
	var $_sModuleUri;
	
    function SkSocialLoginModule(&$aModule) {    
        parent::BxDolModule($aModule);
		$this->_iVisitorID = (isMember()) ? (int) $_COOKIE['memberID'] : 0;
		$this->_sModuleUri = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
        
        // Remove socialall url login from db
        $aCheckApiLoginUrl = $this->_oDb->getOptionsByName('sk_social_login_api_login_url');
        $aCheckApiUrl = $this->_oDb->getOptionsByName('sk_social_login_api_service_url');
        if(!empty($aCheckApiLoginUrl) || !empty($aCheckApiUrl))
            $this->_oDb->removeApiUrl();
    }  
	
	function actionAdministration($sUrl = ''){
		if(!$this->isAdmin())
		{
			$this->_oTemplate->displayAccessDenied();
			return;
		}
		$this->_oTemplate->pageStart();
		$this->_oTemplate->addAdminCss(array('jquery-ui.css','manage_network.css','socl_login_theme.css','manage_theme.css'));
		$this->_oTemplate->addAdminJs(array('jquery-ui.js','manage_network.js'));
		$aMenu = array(
			'settings' => array(
				'title' => _t('_sk_social_login_caption_settings'),
				'href' => $this->_sModuleUri . 'administration/settings',
				'_func' => array(
					'name' => 'getAdministrationSettings', 
					'params' => array()
				),
			),
			'api_settings' => array(
				'title' => _t('_sk_social_login_caption_api_settings'),
				'href' => $this->_sModuleUri . 'administration/api_settings',
				'_func' => array(
					'name' => 'getAdministrationApiSettings',
					'params' => array()
				),
			),
			'manage_network' => array(
				'title' => _t('_sk_social_login_caption_manage_network'),
				'href' => $this->_sModuleUri . 'administration/manage_network',
				'_func' => array(
					'name' => 'actionAdministrationManageNetwork', 
					'params' => array()
				),
			),
            'manage_theme' => array(
				'title' => _t('_sk_social_login_caption_manage_theme'),
				'href' => $this->_sModuleUri . 'administration/manage_theme',
				'_func' => array(
					'name' => 'actionAdministrationManageTheme', 
					'params' => array()
				),
			)
		);
		if(empty($aMenu[$sUrl]))
			$sUrl = 'settings';
		$aMenu[$sUrl]['active'] = 1;
		$sContent = call_user_func_array(array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);
		echo $this->_oTemplate->adminBlock($sContent, _t('_sk_social_login_title_admin'), $aMenu);
		$this->_oTemplate->pageCodeAdmin(_t('_sk_social_login_page_admin'));
	}
	
	function actionPopupLoginForm(){
		$sFormCode = getMemberLoginFormCode('login_box_form', true);
		
        $iDesignBox = 1;
        $sCode = $sFormCode;
    	$sJoinFormContent = getMemberJoinFormCode();
    	if(!empty($sJoinFormContent)) {
    		$iDesignBox = 3;
			$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('login_join_popup.html', array(
				'login_form' => $sCode,
				'join_form' => $sJoinFormContent
			));
    	}
        
        $sCode .= <<<EOF
        <script type='text/javascript'>
            $('#tabs-login .bx-form-element:eq(0)').before("<div id='sk_login_popup_login_form' class='bx-form-element bx-form-element-text bx-def-margin-top clearfix'></div>");
            $('#tabs-join .bx-form-element:eq(0)').before("<div id='sk_login_popup_join_form' class='bx-form-element bx-form-element-text bx-def-margin-top clearfix'></div>");
            $.post( site_url + 'm/social_login/get_login_block_popup', function( data ) {
                $('#sk_login_popup_login_form').html(data);
                $('#sk_login_popup_join_form').html(data);
            });
        </script>
EOF;

		$sCaption = _t('_Login');
    	$sCaptionItems = '<div class="dbTopMenu"><i class="bx-popup-element-close sys-icon times"></i></div>';
        $sMemberLoginFormAjx = $GLOBALS['oFunctions']->transBox(
            DesignBoxContent($sCaption, $sCode, $iDesignBox, $sCaptionItems), true
        );

        header('Content-Type: text/html; charset=utf-8');
        echo $sMemberLoginFormAjx;
        exit;
	}
    
    function actionGetLoginBlockPopup(){
        echo $this->serviceGetBlockLogin('form');
    }
	
	function actionHome(){
		$this->checkLogged();
		$this->_oTemplate->pageStart();
		echo $this->serviceGetBlockLogin('module');
		$this->_oTemplate->pageCode(_t('_sk_social_login'), true);
	}
	
	function actionLogin(){
        if(bx_get('error')) {
            echo $this->_oTemplate->parseHtmlByName('login.html',array(
				'js_content' => "window.opener.showSocialLoginError('".bx_get('error')."','error');
								window.close();"
				)
			);
			exit;
        }
        
		if(!bx_get('token') || !bx_get('network')){
			echo $this->_oTemplate->parseHtmlByName('login.html',array(
				'js_content' => "window.opener.showSocialLoginError('"._t('_sk_social_login_form_error')."','error');
								window.close();"
				)
			);
			exit;
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
		
		$sUrl = $this->_oConfig->getApiServiceUrl().'user';
		
		$oContext = stream_context_create($aRequestHeader);
		
		$oResult = json_decode(file_get_contents($sUrl,false,$oContext), true);
        
        if(isset($oResult['error'])){
            echo $this->_oTemplate->parseHtmlByName('login.html',array(
				'js_content' => "window.opener.showSocialLoginError('".$oResult['error']."','error');
								window.close();"
				)
			);
			exit;
        }
        
        if(isset($oResult['success']))
		  $aUserInfo = $oResult['result'];	
			
		$sIdentity = ($sNetwork=='tumblr') ? $aUserInfo['profile_url'] : $aUserInfo['id'];
		
		$aUser = $this->_oDb->getNetworkUserByIdentity($sIdentity);
		
		if($this->_iVisitorID > 0){
			if(!$this->isConnectedNetwork($sNetwork)){
				if($aUser){
					$sName = getNickName($aUser['profile_id']);
					echo $this->_oTemplate->parseHtmlByName('login.html',array(
						'js_content' => "
										window.opener.showSocialLoginError('"._t('_sk_social_login_already_connected',$sNetwork,$sName,$sNetwork)."','error');
										window.close();
										"
						)
					);
					exit;
				}else{
					$sNetworkName = $aUserInfo['username'];
					
					if(empty($sNetworkName))
						$sNetworkName = $this->genNetUsername($aUserInfo,FALSE);
				
					$aUserParam = $this->escape(array(
						'sk_token' => $sToken,
						'identity' => $sIdentity,
						'network' => $sNetwork,
						'username' => $sNetworkName,
						'profile_id' => $this->_iVisitorID,
						'expired_time' => $sExpiredTime
					));
					
					if(!$this->_oDb->createNetworkUser($aUserParam))
						exit(_t('_sk_social_login_database_error'));
					echo $this->_oTemplate->parseHtmlByName('login.html',array('js_content' => "window.close();opener.location.reload();"));
					exit;
				}
			}
		}
		
		
		
		if($aUser){
			if(isLoggedBanned($aUser['profile_id'])){
                echo $this->_oTemplate->parseHtmlByName('login.html',array('js_content' => "
													window.close();
													window.opener.showSocialLoginMes('"._t('_sk_social_login_banned_account',$sNetwork)."');"));
				exit;
			}
                
			$profile = getProfileInfo($aUser['profile_id']);
			
			if(!$profile || empty($profile)) {
				$this->_oDb->deleteNetworkUser($aUser['profile_id']);
				echo $this->_oTemplate->parseHtmlByName('login.html',array('js_content' => "
													window.close();
													window.opener.showSocialLoginMes('"._t('_sk_social_login_deleted_account')."');"));
				exit;
			}
			
			$this->login($aUser['profile_id']);
			echo $this->_oTemplate->parseHtmlByName('login.html',array('js_content' => "
													window.close();
													window.opener.showSocialLoginMes('"._t('_sk_social_login_login',$sNetwork)."');
												"));
			exit;
		}
		
		$error = array();
		
		if(!$this->isValidUsername($aUserInfo['username']))
			$error['username'] = 'incorrect';
		elseif($this->isNickNameExisted($aUserInfo['username']))
			$error['username'] = 'existed';
		else
			$error['username'] = 'valid';
		
		if(!$this->isValidEmail($aUserInfo['email']))		
			$error['email'] = 'incorrect';
		elseif($this->isEmailExisted($aUserInfo['email']))
			$error['email'] = 'existed';
		else
			$error['email'] = 'valid';
		
		if( $error['username'] != 'valid' || $error['email'] != 'valid' )
		{
			$aSessionData = $this->escape(array(
				'sk_token' => $sToken,
				'network' => $sNetwork,
				'identity' => $sIdentity,
				'profile_data' => $aUserInfo,
				'expired_time' => $sExpiredTime
			));
			$sEncodedData = base64_encode(serialize($aSessionData));
			$session = BxDolSession::getInstance();
			$session->setValue('sl_data',$sEncodedData);
			
			echo $this->_oTemplate->parseHtmlByName('login.html',array(
				'js_content' => "window.opener.showSocialLoginForm('{$error['username']}','{$error['email']}');
								window.close();"
				)
			);
		}
		else
		{
			$sPwd = $this->genRandPassword();
			$aProfileParam['Salt'] = $this->genSalt();
			$aProfileParam['Password'] = encryptUserPwd($sPwd,$aProfileParam['Salt']);
			$aProfileParam = array_merge($aProfileParam,array(
				'NickName' 	=>	$aUserInfo['username'],
				'Email'		=>	$aUserInfo['email'],
				'Status'	=> 	$this->getProfileStatus()
			));
			
			$aProfileParam = array_merge($aProfileParam,$this->mapData($aUserInfo));
			
			if(!$this->_oDb->createProfile($aProfileParam))
				exit(_t('_sk_social_login_database_error'));
			
			$iProfileId = $this->_oDb->lastId();
			
			$aUserParam = array(
				'sk_token' => $sToken,
				'identity' => $sIdentity,
				'network' => $sNetwork,
				'username' => $aUserInfo['username'],
				'profile_id' => $iProfileId,
				'expired_time' => $sExpiredTime
			);
			
			if(!$this->_oDb->createNetworkUser($aUserParam))
				exit(_t('_sk_social_login_database_error'));
			
			$oZ = new BxDolAlerts('profile', 'join', $iProfileId);
            $oZ -> alert();
			
			$this->login($iProfileId);
			$this->postStatus($aSessionData);
			$this->sendMail($aUserInfo['email'],$aUserInfo['username'],$sPwd,$sNetwork);
			
			bx_import('BxDolProfilesController');
			$oProfile = new BxDolProfilesController();
			if(getParam('autoApproval_ifNoConfEmail') == 'on'){
				if('Active' ==  $aProfileParam['Status'])
					$oProfile->sendActivationMail($iProfileId);
			}else
				$oProfile->sendConfMail($iProfileId);
				
			echo $this->_oTemplate->parseHtmlByName('login.html',array('js_content' => "
													window.close();
													window.opener.showSocialLoginMes('"._t('_sk_social_login_login',$sNetwork)."');
													"));
		}
	}
	
	function actionDisconnect(){
		if($this->_iVisitorID > 0 && bx_get('network')){
			header('Content-type:application/json');
			if($this->_oDb->deleteNetworkUserBy(bx_get('network'),$this->_iVisitorID)){
                $sQueryParam = http_build_query(array(
        			'app_id' => $this->_oConfig->getAppId(),
        			'callback' => urlencode($this->_sModuleUri.'login'),
        			'scope' => 'user,publish',
        		));
				$aResponse['network'] = bx_get('network');
				$aResponse['url'] = $this->_oConfig->getApiLoginUrl().bx_get('network').'?'.$sQueryParam;
			}
			else
				$aResponse['error'] = _t('_sk_social_login_database_error');
			echo json_encode($aResponse);
		} else {
			header('location:'.BX_DOL_URL_ROOT);
			exit;
		}
	}
	
	function actionGenError(){
		$sContent = MsgBox(bx_get('message'));
		$sCaptionItem = <<<BLAH
		<div class="dbTopMenu" onclick="socialLoginClosePopup()" style="cursor: pointer;">
			<i class="login_ajx_close sys-icon remove"></i>
		</div>
BLAH;
		if(bx_get('caption') == 'error')
			echo $GLOBALS['oFunctions']->transBox(DesignBoxContent(_t('_sk_social_login_form_error_title'), $sContent, 11,$sCaptionItem),true);
		else
			echo $GLOBALS['oFunctions']->transBox(DesignBoxContent(_t('_sk_social_login_form_message_title'), $sContent, 11,$sCaptionItem),true);
	}
	
	function actionGenForm(){
	
		$session = BxDolSession::getInstance();

		$aData = unserialize(base64_decode($session->getValue('sl_data')));
		
		if($this->_iVisitorID > 0 || !isset($aData['profile_data']))
		{
			$sUrl = BX_DOL_URL_ROOT;
			header("Location: {$sUrl}");
			exit;
		}
		
		if($this->isActiveCaptcha()){
			bx_import('BxDolCaptcha');
			$oCaptcha = BxDolCaptcha::getObjectInstance();
		}
		
		if(bx_get('email') == 'existed'){
			$sInfo = _t('_sk_social_login_email_mapping');
			$sFlag = 'email';
		}
		elseif(bx_get('email') != 'existed' && bx_get('username') == 'existed'){
			$sInfo = _t('_sk_social_login_username_mapping');
			$sFlag = 'username';
		}
		else{
			$sInfo = '';
			$sFlag = 'not_existed';
		}
		
		$sSuggestName = $aData['profile_data']['username'] ;
		
		if(bx_get('username') == 'incorrect'){
			$sSuggestName = $this->genNetUsername($aData['profile_data']);
		}
		
		$aForm = array(
			'form_attrs' => array(
				'id' => 'sk_login_form',
				'action' => $this->_sModuleUri.'add_info',
				'method' => 'post',
			),
			'inputs' => array(
				'mapping_info' =>array(
					'type' => 'custom',
					'colspan' => true,
					'attrs_wrapper' => array('style' =>'width:100%;'),
					'content' => '<div id="sk_mapping_info" style="margin-top:10px;text-align:center;">
									'._t('_sk_social_login_mapping_intro',$sInfo,ucfirst($aData['network']),getParam('site_title')).'
								</div>',
				),
				'mapping' => array(
					'type' 	=> 'radio_set',
					'name'	=>	'opt',
					'attrs' => array('onclick' => 'socialLoginDisplayInput(\''.$sFlag.'\')'),
					'caption' => '',
					'values' => array(
						'map' => _t('_sk_social_login_opt_map'),
						'new' => _t('_sk_social_login_opt_new'),
					),
				),
				'username' => array(
					'caption' => _t('_NickName'),
					'attrs' => array('id' => 'username'),
					'type' => 'text',
					'name' => 'username',
					'value' => $sSuggestName,
				),
				'email' => array(
					'caption' => _t('_Email'),
					'attrs' => array('id' => 'email'),
					'type' => 'text',
					'name' => 'email',
					'value' => $aData['profile_data']['email'],
				),
				'password' => array(
					'caption' => getParam('site_title')."'s "._t('_Password'),
					'attrs' => array('id' => 'pwd'),
					'type' => 'password',
					'name' => 'password',
				),
				'relocate' => array(
					'type' => 'hidden',
					'name' => 'relocate',
					'value' => bx_get('relocate'),
				),
			),
		);

		if($oCaptcha){
			$aForm['inputs']['captcha'] = array(
				'type' => 'custom',
				'content' => '<div id="sk_captcha_container">'.$oCaptcha->display(true).'</div>'
			);
			$aForm['inputs']['confirm'] = array(
					'attrs' => array('id' => 'confirm','onclick' => 'socialLoginCheckInfo()'),
					'type' => 'button',
					'value' => 'Confirm'
			);
		}else{
			$aForm['inputs']['confirm'] = array(
					'attrs' => array('id' => 'confirm','onclick' => 'socialLoginCheckInfo()'),
					'type' => 'button',
					'value' => 'Confirm'
			);
		}
		
		$oForm = new BxTemplFormView($aForm);
		$sFormCode = $oForm->getCode();
		
		$sFormInfo = '<div id="sk_guide" style="font-weight:bold;margin-bottom:5px;display:none;">'._t('_sk_social_login_form_guide').'</div>';
		
		 $sCaptionItem = <<<BLAH
		<div class="dbTopMenu" onclick="socialLoginClosePopup()" style="cursor: pointer;">
			<i class="login_ajx_close sys-icon remove"></i>
		</div>
BLAH;
		
		$sMemberLoginFormAjx = $GLOBALS['oFunctions']->transBox(
            DesignBoxContent(_t('_sk_social_login_form'), $sFormInfo.$sFormCode, 11, $sCaptionItem), true
        );
		
		echo $this->_oTemplate->parseHtmlByName('login_form.html',array('content'=>$sMemberLoginFormAjx,'email_error'=>bx_get('email'),'username_error'=>bx_get('username')));
	}
	
	function actionCheckInfo()
    {
		$this->checkLogged();
		$sUserName = bx_get('username');
		$sEmail = bx_get('email');
		$sPwd = bx_get('password');
		$aResponse = array();
		header("Content-type:application/json");
		
		if(bx_get('map') == 1){
			
			if($sEmail)
				$aProfile = $this->_oDb->getProfileByEmail($sEmail);
			if($sUserName){
				if(	!isset($aProfile) || (isset($aProfile) && $aProfile == false))
					$aProfile = $this->_oDb->getProfileByName($sUserName);
			}
			
			if($this->isActiveCaptcha()){
				bx_import('BxDolCaptcha');
				$oCaptcha = BxDolCaptcha::getObjectInstance();
				if($oCaptcha && $oCaptcha->check())
					$aResponse['captcha']['error'] = 'valid';
				else{
					$aResponse['captcha']['error'] = $oCaptcha->display(true);
					$aResponse['captcha']['mess'] = _t('_sk_social_login_incorrect_captcha');
				}
			}
			
			if($aProfile && encryptUserPwd($sPwd,$aProfile['Salt']) == $aProfile['Password'])
				$aResponse['password']['error'] = 'valid';
			else{
				$aResponse['password']['error'] = 'incorrect';
				$aResponse['password']['mess'] = _t('_sk_social_login_incorrect_password');
			}
			
			if($this->isEmailExisted($sEmail))
				$aResponse['email']['error'] = 'valid';
			elseif($this->isNickNameExisted($sUserName))
				$aResponse['username']['error'] = 'valid';
			else{
				$aResponse['email']['error'] = 'incorrect';
				$aResponse['email']['mess'] = _t('_sk_social_login_not_existed_email');
				$aResponse['username']['error'] = 'incorrect';
				$aResponse['username']['mess'] = _t('_sk_social_login_not_existed_username');
			}
		
		}else{
		
			if($this->isNickNameExisted($sUserName)){
				$aResponse['username']['error'] = 'existed';
				$aResponse['username']['label'] = _t('_sk_social_login_username_mapping');
				$aResponse['username']['mess'] = _t('_sk_social_login_existed_username');
			}
			elseif(!$this->isValidUsername($sUserName)){
				$aResponse['username']['error'] = 'incorrect';
				$aResponse['username']['mess'] = _t('_sk_social_login_incorrect_username');
			}
			else
				$aResponse['username']['error'] = 'valid';
				
			if(!$this->isValidEmail($sEmail)){
				$aResponse['email']['error'] = 'incorrect';
				$aResponse['email']['mess'] = _t('_sk_social_login_incorrect_email');
			}
			elseif($this->_oDb->getProfileByEmail($sEmail)){
				$aResponse['email']['error'] = 'existed';
				$aResponse['email']['label'] = _t('_sk_social_login_email_mapping');
				$aResponse['email']['mess'] = _t('_sk_social_login_existed_email');
			}
			else
				$aResponse['email']['error'] = 'valid';
			
		}
		
		echo json_encode($aResponse);
	}
	
	function actionAddInfo(){
	
		$this->checkLogged();
	
		$session = BxDolSession::getInstance();
		$aData = unserialize(base64_decode($session->getValue('sl_data')));
	
		if(bx_get('opt') == 'map'){
						
			if(bx_get('email'))
				$aProfile = $this->_oDb->getProfileByEmail(bx_get('email'));
			if(bx_get('username')){
				if(	!isset($aProfile) || (isset($aProfile) && $aProfile == false))
					$aProfile = $this->_oDb->getProfileByName(bx_get('username'));
			}
			
			if($this->isConnectedNetwork($aData['network'],$aProfile['ID'])){
				header('Content-type:application/json');
				$aResponse['error'] =_t('_sk_social_login_connected_account',getParam('site_title'),$aData['network']);
				echo json_encode($aResponse);
				exit;
			}
					
			if(isLoggedBanned($aProfile['ID'])){
				header('Content-type:application/json');
				$aResponse['error'] =_t('_sk_social_login_banned_account',$aData['network']);
				echo json_encode($aResponse);
				exit;
			}
			
			$sNetworkName = $aData['profile_data']['username'];
			
			if(empty($aData['profile_data']['username']))
				$sNetworkName = $this->genNetUsername($aData['profile_data'],FALSE);
			
			$aUserParam = array(
				'sk_token' => $aData['sk_token'],
				'network' => $aData['network'],
				'identity' => $aData['identity'],
				'expired_time' => $aData['expired_time'],
				'username' => $sNetworkName,
				'profile_id' => $aProfile['ID']
			);
			
			if(!$this->_oDb->createNetworkUser($aUserParam))
				exit(_t('_sk_social_login_database_error'));
			
			$oZ = new BxDolAlerts('profile', 'join', $aProfile['profile_id']);
            $oZ -> alert();
			
			$session->unsetValue('sl_data');
			$this->login($aProfile['ID']);
			
			echo _t('_sk_social_login_network_to_account',$aData['network'],getParam('site_title'));
            
		} else {
			
			$sPwd = $this->genRandPassword();
			$aProfileParam['Salt'] = $this->genSalt();
			$aProfileParam['Password'] = encryptUserPwd($sPwd,$aProfileParam['Salt']);
			$aProfileParam['Status'] = $this->getProfileStatus();
			
			if(bx_get('username'))
				$aProfileParam['NickName'] = bx_get('username');
			if(bx_get('email'))
				$aProfileParam['Email'] = bx_get('email');
				
			$aProfileParam = array_merge($aProfileParam, $this->mapData($aData['profile_data']));
			
            $createProfile = $this->_oDb->createProfile($aProfileParam);
			if(!$createProfile)
				exit(_t('_sk_social_login_database_error'));
			
			$iProfileId = $this->_oDb->lastId();

			
			$sNetworkName = $aData['profile_data']['username'];
			
			if(empty($aData['profile_data']['username']))
				$sNetworkName = $this->genNetUsername($aData['profile_data'],FALSE);
			
			$aUserParam = array(
				'sk_token' => $aData['sk_token'],
				'identity' => $aData['identity'],
				'network' => $aData['network'],
				'expired_time' => $aData['expired_time'],
				'username' => $sNetworkName,
				'profile_id' => $iProfileId
			);
			
			if(!$this->_oDb->createNetworkUser($aUserParam))
				exit(_t('_sk_social_login_database_error'));
			
			$oZ = new BxDolAlerts('profile', 'join', $iProfileId);
            $oZ -> alert();
			
			$session->unsetValue('sl_data');
			$this->login($iProfileId);
			$this->postStatus($aData);
			$this->sendMail($aProfileParam['Email'],$aProfileParam['NickName'],$sPwd,$aData['network']);
			
			bx_import('BxDolProfilesController');
			$oProfile = new BxDolProfilesController();
			if(getParam('autoApproval_ifNoConfEmail') == 'on'){
				if('Active' ==  $aProfileParam['Status'])
					$oProfile->sendActivationMail($iProfileId);
			}else
				$oProfile->sendConfMail($iProfileId);
			
			echo _t('_sk_social_login_login',$aData['network']);
			//echo "You have just login via your {$aData['network']} account";
			//echo $this->_oTemplate->parseHtmlByName('login.html',array('js_content' => 'window.location="'.bx_get('relocate').'";'));
		}
	}
	
	function actionAdministrationManageNetwork(){
		// 25-12-2015: update Instagram, Pinterest, Amazon, Ebay networks
        $aNetworks = $this->_oDb->checkNetworksExists("'instagram','pinterest','amazon','ebay'");
        $aNets = array();
        if($aNetworks) {
            foreach($aNetworks as $net)
                $aNets[] = $net['name'];
        }
        
        if(!in_array('instagram', $aNets))
            $this->_oDb->insertNetwork('instagram', 'https://www.instagram.com/');
            
        if(!in_array('pinterest', $aNets))
            $this->_oDb->insertNetwork('pinterest', 'https://www.pinterest.com/');
            
        if(!in_array('amazon', $aNets))
            $this->_oDb->insertNetwork('amazon', 'http://www.amazon.com/');
            
        if(!in_array('ebay', $aNets))
            $this->_oDb->insertNetwork('ebay', 'http://www.ebay.com/');
        // END update
        
		$aNetwork = $this->_oDb->getAllNetworks();
		
		if(!$aNetwork){
			$aVar['content'] = MsgBox(_t('_sk_social_login_db_error'));
			return $this->_oTemplate->parseHtmlByName('default_padding',$aVar);
		}
		
		foreach($aNetwork as $key=>$network){
			$aNetwork[$key]['loading_image'] = $this->_oTemplate->getImageUrl('updating.gif');
		}
		$aVar = array(
			'bx_repeat:networks' => $aNetwork,
		);
		
		$sContent = $this->_oTemplate->parseHtmlByName('block_network_admin',$aVar);
		
		$aContent = array(
			'content' => $sContent
		);
		
		return $this->_oTemplate->parseHtmlByName('default_padding',$aContent);
	}
	
	function actionAjaxMode($sAction){
	
		$sRetHtml = '';
		if(!bx_get('ajaxmode'))
			$sRetHtml = 'Access denied';
		else
		{
			switch($sAction)
			{
				case 'order_network':
					$sRetHtml = $this->updateNetworkOrder();
					break;
				case 'status_network':
					$sRetHtml = $this->updateNetworkStatus();
					break;
				case 'apply_theme':
                    $sRetHtml = $this->applyTheme();
                    break;
			}
		}
		echo $sRetHtml;
		
	}
	
	private function postStatus($aData){
		if(!$this->isSupportedNetwork($aData['network']))
			return false;
		$aQueryParam = array(
			'token' => $aData['sk_token'],
			'message' =>  getParam('sk_social_login_status_content')
		);
		
		$sSig = $this->signRequest($aQueryParam);
		$aQueryParam['sig'] = $sSig;
		
		$aContextParam = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($aQueryParam),
			)
		);
		$sUrl = $this->_oConfig->getApiServiceUrl().'publish';
		
		$context = stream_context_create($aContextParam);
		$aResult = json_decode(file_get_contents($sUrl,false,$context), true);	
        
        return isset($aResult['success']) ? true : false;
	}
	
	function updateNetworkOrder(){
		$aNetwork = bx_get('network');
		return $this->_oDb->updateNetworkOrder($aNetwork);
	}
	
	function updateNetworkStatus(){
		$sNetwork = bx_get('network');
		$sStatus = bx_get('status');
		return $this->_oDb->updateNetworkStatus($sNetwork,$sStatus);
	}
	
	function getAdministrationSettings()
	{
		$iId = $this->_oDb->getSettings();
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
            
        $sResult .= <<<EOF
        <style>
            .sk_login_checkbox {
                display: inline-block !important;
                margin-left: 15px;
            }
        </style>
        <script type='text/javascript'>
            $('form#adm-settings-form input[type=\'checkbox\']').each(function() {
                $(this).parents('.bx-form-value:first').addClass('sk_login_checkbox').parent().find('.bx-form-caption').css('display', 'inline-block');
            });
        </script>
EOF;
		echo DesignBoxAdmin(_t('_sk_social_login_setting_info'), $this->_oTemplate->parseHtmlByName('default_padding.html', array(
			'content' => _t('_sk_social_login_setting_info_content')
		)));
		return $this->_oTemplate->parseHtmlByName('default_padding',array('content' => $sResult));
	}
	
	function getAdministrationApiSettings()
	{
		$iId = $this->_oDb->getApiSettings();
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
		echo DesignBoxAdmin(_t('_sk_social_login_setting_info'), $this->_oTemplate->parseHtmlByName('default_padding.html', array(
			'content' => _t('_sk_social_login_api_setting_info_content')
		)));
		return $this->_oTemplate->parseHtmlByName('default_padding',array('content'=>$sResult));
	}
	
	function serviceGetMemberBlockLogin(){
	   $this->_oTemplate->addCss(array('socl_login_theme.css'));
       
		$aNetwork = $this->_oDb->getEnabledNetworks();
		
		$sQueryParam = http_build_query(array(
			'app_id' => $this->_oConfig->getAppId(),
			'callback' => urlencode($this->_sModuleUri.'login'),
			'scope' => 'user,publish',
		));
		
		foreach($aNetwork as $key=>$row){
			$aNetwork[$key]['info'] = $this->isConnectedNetwork($row['name']) ? _t('_sk_social_login_connected_as',$this->getNetworkProfileUrl($row['name'],$row['profile_url']),$this->_oDb->getNetworkUsername($row['name'],$this->_iVisitorID),$row['name']) : '<a href="javascript:void(popup(\''.$this->_oConfig->getApiLoginUrl().$row['name'].'?'.$sQueryParam.'\'))">'._t('_sk_social_login_connect').'</a>';
		}
		$aVar = array(
			'bx_repeat:networks' => $aNetwork
		);
		echo $this->_oTemplate->parseHtmlByName('connecting_network_block.html',$aVar);
	}
	
	function serviceGetBlockLogin($popup = 'block'){
		$aNetwork = $this->_oDb->getEnabledNetworks();
		if($this->isActiveCaptcha() && $popup != 'join_page')
			$this->_oTemplate->addInjection('injection_footer','text','<script type="text/javascript">$.getScript("//www.google.com/recaptcha/api/js/recaptcha_ajax.js");</script>');
        
        $this->_oTemplate->addCss(array('socl_login_theme.css'));
            
		$output= "
				<script type='text/javascript'>
				  function popup(pageURL){
						var oPopupOptions = {closeOnOuterClick: false};
						
						var w = 800;
						var h = 500;
						var title ='socialloginwindow';
						var left = (screen.width/2)-(w/2);
						var top = (screen.height/2)-(h/2);
						var newwindow =  window.open (pageURL, title, 'toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top='+top+',left='+left);
						showSocialLoginLoadingImg();
						if (window.focus) {newwindow.focus();}
						
						var interval = setInterval(function(){
								
								if (newwindow.closed && $('#sk_login_loading').length) {
									$('#sk_login_loading').parent().dolPopupHide(oPopupOptions);
									clearInterval(interval);
								}

								try{
									if(newwindow.location.href == site_url){
										newwindow.close();
										showSocialLoginError('"._t('_sk_social_login_form_error')."','error');
										clearInterval(interval);
									}
								}catch(e){}
								
							},500);
					};
				 </script>
				 ";

		if($popup == 'form')
			$output .="<div style='margin:auto;white-space:normal'>";
		elseif($popup == 'module')
			$output .="<div>";
		else
			$output .="<div class='bx-def-bc-padding'>";
            
        $sThemeApplied = (!getParam('sk_social_login_theme_applied')) ? 'default' : getParam('sk_social_login_theme_applied');
        $output .= "<div class='sa-" .$sThemeApplied. "'>";
			
		$aQueryParams = http_build_query(array(
			'app_id' => $this->_oConfig->getAppId(),
			'callback' => urlencode($this->_sModuleUri.'login'),
			'scope' => 'user,publish',
		));
        
        $aThemesResize = array('no5','no6','no7');
        if(in_array($sThemeApplied, $aThemesResize))
            $sThemeSize = " sa-" . ((getParam('sk_social_login_theme_resize')) ? getParam('sk_social_login_theme_resize') : '100');
        else $sThemeSize = '';
			
		foreach($aNetwork as $network){
			$sLoginUrl = $this->_oConfig->getApiLoginUrl().$network['name'].'?'.$aQueryParams;
			$output .='<a href="javascript:void(popup(\''.$sLoginUrl.'\'));" class="sa' .$sThemeSize. ' sa-' .$network['name']. '" style="text-decoration: none;"></a>';
		}
		$output .='<div class="clear_both"></div></div></div>';
		
		return $output;
	}
	
	private function getNetworkProfileUrl($sNetwork,$sProfileUrl){
		$aUser = $this->_oDb->getNetworkUserBy($sNetwork,$this->_iVisitorID);
		if($sNetwork == 'twitter' || $sNetwork == 'plurk' || $sNetwork == 'reddit' || $sNetwork == 'lastfm' || $sNetwork == 'github' || $sNetwork == 'disqus')
			return $sProfileUrl.$aUser['username'];
		elseif($sNetwork == 'facebook' || $sNetwork == 'google' || $sNetwork == 'foursquare')
			return $sProfileUrl.$aUser['identity'];
		elseif($sNetwork == 'tumblr')
			return $aUser['identity'];
		elseif($sNetwork == 'vkontakte')
			return $sProfileUrl.'id'.$aUser['identity'];
		else
			return $sProfileUrl;
	}
	
	private function isConnectedNetwork($sNetworkName,$iProfileId = null){
		$flag = false;
		if(empty($iProfileId))
			$aNetworkUser = $this->_oDb->getNetworkUserById($this->_iVisitorID);
		else
			$aNetworkUser = $this->_oDb->getNetworkUserById($iProfileId);
		if($aNetworkUser){
			foreach($aNetworkUser as $row){
				if($row['network'] == $sNetworkName){
					$flag = true;
					break;
				}
			}
		}
		return $flag;
	}
	private function isSupportedNetwork($sNetwork){
		$flag = true;
		$aNetwork = array('google','facebook','reddit','live','vkontakte','github','wordpress','foursquare','disqus');
		
		$aSupportedNetwork = array('plurk','tumblr','twitter','linkedin','mailru','lastfm');
		
		foreach($aSupportedNetwork as $value){
			if(getParam('sk_social_login_post_'.$value) != 'on')
				$aNetwork[] = $value;
		}
		
		foreach($aNetwork as $network){
			if($sNetwork == $network){
				$flag = false;
				break;
			}
		}
		return $flag;
	}
	
	private function isActiveCaptcha(){
		if(getParam('sk_social_login_display_captcha') == 'on' && getParam('sys_recaptcha_key_public') && getParam('sys_recaptcha_key_private'))
			return true;
		return false;
	}
	
	function login($iProfileId)
	{
		bx_login($iProfileId, 0);
		send_headers_page_changed();
	}
	
	private function checkLogged(){
		if($this->_iVisitorID > 0)
		{
			$sUrl = BX_DOL_URL_ROOT;
			header("Location: {$sUrl}");
			exit;
		}
	}
	
	private function escape($aData){
		foreach($aData as $key=>$value)
			$aData[$key] = process_db_input($value);
		return $aData;
	}
	
	private function isEmailExisted($sEmail){
		if($this->_oDb->getProfileByEmail($sEmail))
			return true;
		return false;
	}
	
	private function isNickNameExisted($sName){
		if($this->_oDb->getProfileByName($sName))
			return true;
		return false;
	}
	private function sendMail($sEmail,$sUserName,$sPwd,$sNetwork){
		bx_import('BxDolEmailTemplates');
		$oEmailTemplates = new BxDolEmailTemplates();
		$aMessage = $oEmailTemplates->parseTemplate('sk_social_login_information', array(
				'Username' => $sUserName,
				'Password' => $sPwd,
				'Network' => $sNetwork
		));
		sendMail($sEmail,$aMessage['subject'],$aMessage['body']);
	}
	private function genUniqueName($sName){
		$sName = $this->removeAccents($sName);
		if(!preg_match('/^[a-zA-Z0-9_-]+$/',$sName))
			$sName = preg_replace('/[^a-zA-Z0-9_-]+/','',$sName);			

		if(!$this->isNickNameExisted($sName))
			return $sName;
		
		$num = mt_rand(1,1000);
		$sName = $sName.$num;
		return $this->genUniqueName($sName);
	}
	private function genRandPassword(){
		return genRndPwd(8,false);
	}
	private function getProfileStatus(){
		if (getParam('autoApproval_ifNoConfEmail') == 'on') {
			if (getParam('autoApproval_ifJoin') == 'on' && !(getParam('sys_dnsbl_enable') && 'approval' == getParam('sys_dnsbl_behaviour') && bx_is_ip_dns_blacklisted('', 'join')))
				return 'Active';
			else
				return 'Approval';
		} else
			return 'Unconfirmed';
	}
	private function genSalt(){
		//return base_convert(floor(rand() * 99999999999999), 10, 36);
		return genRndSalt();
	}
	private function isValidUsername($name){
		$regex = "/^[a-zA-Z0-9_-]+$/";
		if(strlen($name)>= 4 && strlen($name) <=255 && preg_match($regex,$name))
			return true;
		return false;
	}
	private function isValidEmail($email){
		$regex = "/^([a-z0-9\+\_\-\.]+)@([a-z0-9\+\_\-\.]+)$/i";
		if(preg_match($regex,$email))
			return true;
		return false;
	}
	
	private function isAdmin()
	{
		return isAdmin($this->_iVisitorID) || isModerator($this->_iVisitorID);
	}
	
	private function removeAccents($str)
	{
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ',
		"à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă", 
          "ằ","ắ","ặ","ẳ","ẵ", 
          "è","é","ẹ","ẻ","ẽ","ê","ề"     ,"ế","ệ","ể","ễ", 
          "ì","í","ị","ỉ","ĩ", 
          "ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ" 
          ,"ờ","ớ","ợ","ở","ỡ", 
          "ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ", 
          "ỳ","ý","ỵ","ỷ","ỹ", 
          "đ", 
          "À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă" 
          ,"Ằ","Ắ","Ặ","Ẳ","Ẵ", 
          "È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ", 
          "Ì","Í","Ị","Ỉ","Ĩ", 
          "Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ" 
          ,"Ờ","Ớ","Ợ","Ở","Ỡ", 
          "Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ", 
          "Ỳ","Ý","Ỵ","Ỷ","Ỹ", 
          "Đ","ê","ù","à");
		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o',
		"a","a","a","a","a","a","a","a","a","a","a" 
          ,"a","a","a","a","a","a", 
          "e","e","e","e","e","e","e","e","e","e","e", 
          "i","i","i","i","i", 
          "o","o","o","o","o","o","o","o","o","o","o","o" 
          ,"o","o","o","o","o", 
          "u","u","u","u","u","u","u","u","u","u","u", 
          "y","y","y","y","y", 
          "d", 
          "A","A","A","A","A","A","A","A","A","A","A","A" 
          ,"A","A","A","A","A", 
          "E","E","E","E","E","E","E","E","E","E","E", 
          "I","I","I","I","I", 
          "O","O","O","O","O","O","O","O","O","O","O","O" 
          ,"O","O","O","O","O", 
          "U","U","U","U","U","U","U","U","U","U","U", 
          "Y","Y","Y","Y","Y", 
          "D","e","u","a");
	  return str_replace($a, $b, $str);
	}
    
	private function mapData($aUserData){
	
		$aProfileParam = array();
        
		if(!empty($aUserData['full_name']))
            $aProfileParam['FullName'] = $aUserData['full_name'];
		else if(!empty($aUserData['first_name']) || !empty($aUserData['last_name']))
			$aProfileParam['FullName'] = $aUserData['first_name'] . ((!empty($aUserData['first_name']) && !empty($aUserData['last_name'])) ? " " : "") . $aUserData['last_name'];
			
		if(!empty($aUserData['date_of_birth']))
			$aProfileParam['DateOfBirth'] = $aUserData['date_of_birth'];
			
		if(!empty($aUserData['gender']))
			$aProfileParam['Sex'] = $aUserData['gender'];
		
		if(!empty($aUserData['location']))
			$aProfileParam['Country'] = $aUserData['location'];
		
		return $aProfileParam;
	
	}
    
	private function genNetUsername($aData,$bUnique = TRUE){
		if(!empty($aData['full_name']))
			return $bUnique ? $this->genUniqueName($aData['full_name']) : $aData['full_name'];
		if(!empty($aData['display_name']))
			return $bUnique ? $this->genUniqueName($aData['display_name']) : $aData['display_name'];
		if(!empty($aData['email']))
			return  $bUnique ? $this->genUniqueName(strstr($aData['email'],'@',true)) : strstr($aData['email'],'@',true);
		return '';
	}
	
	private function signRequest($data){
	
		ksort($data);
	
		$str_data = '';
		
		foreach($data as $key=>$value)
			$str_data .= "$key=$value";
	
		return md5($this->_oConfig->getSecretKey().$str_data);
	}
    
    public function actionShowLoadingImg() {
        $sLoadingImg = $this->_oTemplate->getImageUrl('loading.gif');
        echo '<div id="sk_login_loading"><img src="' . $sLoadingImg . '"/></div>'; 
    }
    
    public function serviceDeleteProfileConnected($iProfileId) {
        if (!(int)$iProfileId)
            return false;
            
        $this->_oDb->deleteNetworkUser($iProfileId);
        return true;
    }
    
    public function actionAdministrationManageTheme() {
        $aNetwork = $this->_oDb->getAllNetworks();
		
		if(!$aNetwork){
			$aVar['content'] = MsgBox(_t('_sk_social_login_db_error'));
			return $this->_oTemplate->parseHtmlByName('default_padding',$aVar);
		}
        
        $aTheme = array('default','core','no3','no4','no5','no6','no7','no8');
        $aThemesResize = array('no5','no6','no7');
        $aThemeSizes = array(
            array('name' => _t('_sk_social_login_large_size_text'), 'size' => '100'),
            array('name' => _t('_sk_social_login_medium_size_text'), 'size' => '75'),
            array('name' => _t('_sk_social_login_small_size_text'), 'size' => '50')
        );
        
        $aThemes = array();
        $iNo = 1;
        $sThemeApplied = (!getParam('sk_social_login_theme_applied')) ? 'default' : getParam('sk_social_login_theme_applied');
        $sThemeSize = (!getParam('sk_social_login_theme_resize')) ? '100' : getParam('sk_social_login_theme_resize');
        foreach($aTheme as $sItem) {
            foreach($aNetwork as $key => $value) {
                if(in_array($sItem, $aThemesResize))
                    $aNetwork[$key]['size_class'] = 'sa-' . (($sItem == $sThemeApplied && getParam('sk_social_login_theme_resize')) ? getParam('sk_social_login_theme_resize') : 100);
                else $aNetwork[$key]['size_class'] = ''; 
            }
            
            if(in_array($sItem, $aThemesResize)) {
                foreach($aThemeSizes as $key => $value) {
                    if($sItem == $sThemeApplied && $aThemeSizes[$key]['size'] == $sThemeSize)
                        $aThemeSizes[$key]['content'] = '<option value="' .$aThemeSizes[$key]['size']. '" selected="selected">' .$aThemeSizes[$key]['name']. '</option>';
                    else
                        $aThemeSizes[$key]['content'] = '<option value="' .$aThemeSizes[$key]['size']. '">' .$aThemeSizes[$key]['name']. '</option>';
                }
            }
            
            $aThemes[] = array(
                'title' => _t('_sk_social_login_title_theme') . $iNo,
                'code' => $sItem,
                'bx_repeat:networks' => $aNetwork,
                'apply' => _t('_sk_social_login_apply_text'),
                'theme_applied' => $sThemeApplied,
                'bx_if:check_applied' => array(
                    'condition' => ($sItem == $sThemeApplied),
                    'content' => array()
                ),
                'bx_if:check_resize' => array(
                    'condition' => (in_array($sItem, $aThemesResize)),
                    'content' => array(
                        'custom_size' => _t('_sk_social_login_custom_size_text'),
                        'bx_repeat:theme_size' => $aThemeSizes
                    )
                )
            );
            $iNo++;
        }
        
        $aVar = array(
			'bx_repeat:theme' => $aThemes,
            'wating_text' => _t('_sk_social_login_wating_text'),
            'apply_text' => _t('_sk_social_login_apply_text'),
            'error_text' => _t('_sk_social_login_error_text')
		);
		
		$sContent = $this->_oTemplate->parseHtmlByName('block_manage_theme', $aVar);
		
		$aContent = array(
			'content' => $sContent
		);
		
		return $this->_oTemplate->parseHtmlByName('default_padding', $aContent);
    }
    
    private function applyTheme() {
        $sTheme = bx_get('theme');
        $sSize = bx_get('resize');
        
        if($this->_oDb->applyTheme($sTheme, $sSize))
            return 1;
            
        return 0;
    }
}

?>
