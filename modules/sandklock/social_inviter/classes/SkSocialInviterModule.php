<?php

bx_import('BxDolModule');

class SkSocialInviterModule extends BxDolModule{

	var $_iVisitorID;
	var $_sModuleUri;

    function SkSocialInviterModule(&$aModule){
        parent::__construct($aModule);
		$this->_iVisitorID = (isMember()) ? (int) $_COOKIE['memberID'] : 0;
		$this->_sModuleUri = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
    }

	function actionAdministration($sUrl = ''){
		if(!$this->isAdmin())
		{
			$this->_oTemplate->displayAccessDenied();
			return;
		}
		$this->_oTemplate->pageStart();
		$this->_oTemplate->addAdminCss(array('manage_network.css'));
		$this->_oTemplate->addAdminJs(array('jquery-ui.js','manage_network.js'));
		$aMenu = array(
			'user_statis' => array(
				'title' => 'User statistic',
				'href' => $this->_sModuleUri . 'administration/user_statis',
				'_func' => array(
					'name' => 'getAdministrationUserStatistic', 'params' => array()
				),
			),
			'statis' => array(
				'title' => 'Statistic',
				'href' => $this->_sModuleUri . 'administration/statis',
				'_func' => array(
					'name' => 'getAdministrationStatistic', 'params' => array()
				),
			),
			'settings' => array(
				'title' => _t('_sk_social_inviter_caption_settings'),
				'href' => $this->_sModuleUri . 'administration/settings',
				'_func' => array(
					'name' => 'getAdministrationSettings',
					'params' => array()
				),
			),
			/*'api_settings' => array(
				'title' => _t('_sk_social_inviter_caption_api_settings'),
				'href' => $this->_sModuleUri . 'administration/api_settings',
				'_func' => array(
					'name' => 'getAdministrationApiSettings',
					'params' => array()
				),
			),*/
			'manage_network' => array(
				'title' => _t('_sk_social_inviter_caption_manage_network'),
				'href' => $this->_sModuleUri . 'administration/manage_network',
				'_func' => array(
					'name' => 'actionAdministrationManageNetwork',
					'params' => array()
				),
			),
		);
		if(empty($aMenu[$sUrl]))
			$sUrl = 'settings';
		$aMenu[$sUrl]['active'] = 1;

		$sAdminHtml = call_user_func_array(array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

		if($sUrl == 'user_statis' || $sUrl == 'statis')
			$sContent = $sAdminHtml['form'];
		else
			$sContent = $sAdminHtml;
		echo $this->_oTemplate->adminBlock($sContent, _t('_sk_social_inviter_title_admin'), $aMenu);

		if(!empty($sAdminHtml['table']))
			echo DesignBoxAdmin(_t('_sk_social_inviter_data_table'),$this->_oTemplate->parseHtmlByName('default_padding',array('content'=>$sAdminHtml['table'])),1);

		$this->_oTemplate->pageCodeAdmin(_t('_sk_social_inviter_page_admin'));
	}

	function actionAdministrationManageNetwork(){

		$aNetwork = $this->_oDb->getAllNetworks();

		if(!$aNetwork){
			$aVar['content'] = MsgBox(_t('_sk_social_inviter_db_error'));
			return $this->_oTemplate->parseHtmlByName('default_padding',$aVar);
		}

		foreach($aNetwork as $key=>$network){
			$aNetwork[$key]['image_url'] = $this->_oTemplate->getImageUrl($network['logo']);
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

	function actionInvite($sExpired = ''){
		if(bx_get('error') == 'login_failed'){
			exit($this->_oTemplate->parseHtmlByName('invite.html',array(
				'js_content' => "window.opener.showSocialInviterMes('"._t('_sk_social_inviter_login_failed')."','error');
								window.close();"
				)
			));
		}

		$this->checkLogged();

		$sToken = bx_get('token');
		$sNetwork = bx_get('network');
		$sExpiredTime = bx_get('expired_time')-60;

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

		$sUrl = $this->_oConfig->getApiServiceUrl().'friends';

		$oContext = stream_context_create($aRequestHeader);

		$oResult = json_decode(file_get_contents($sUrl,false,$oContext), true);
        
        if(isset($oResult['error'])){
            exit($this->_oTemplate->parseHtmlByName('invite.html',array(
				'js_content' => "window.opener.showSocialInviterMes('".$oResult['error']."','error');
								window.close();"
				)));
        }
        
        if(isset($oResult['success']))
		  $aFriendList = $oResult['result'];

		//Fixed show no friend message temporary
		if(count($aFriendList) > 0){

			$session = BxDolSession::getInstance();
			$session->setValue('si_data',array('network' => $sNetwork,'sk_token' => $sToken,'expired_time' => $sExpiredTime));

			$aEmails = array();

			foreach($aFriendList as $key=>$value){
				if($sNetwork == 'google' || $sNetwork == 'live'){
					if(empty($value['email'])){
						unset($aFriendList[$key]);
						continue;
					}
				}
				$aEmails[$key] = $value['email'];
				$aFriendList[$key]['username'] = !empty($value['username']) ? $value['username'] : (!empty($value['full_name']) ? $value['full_name'] : $value['email']);
				$aFriendList[$key]['identity'] = $sNetwork == 'tumblr' ? $value['profile_url'] : $value['id'];
				$aFriendList[$key]['receiver'] = $this->getReceiver($sNetwork,$value);
				$aFriendList[$key]['num']	   = $key;
			}

			if($sNetwork == 'google'){
				$aEmails = array_unique($aEmails,SORT_REGULAR);

				foreach($aEmails as $ekey=>$evalue)
					$aFixedIndexList[] = $aFriendList[$ekey];
			}else
				$aFixedIndexList=array_values($aFriendList);

			$aVar = array(
				'js_content' => "
								window.opener.showSocialInviterBlock('".addslashes(json_encode($aFixedIndexList))."');
								window.close();
								"
			);

			echo $this->_oTemplate->parseHtmlByName('invite.html',$aVar);

		}else{
			echo $this->_oTemplate->parseHtmlByName('invite.html',array(
				'js_content' => "window.opener.showSocialInviterMes(\""._t('_sk_social_inviter_no_friend',$sNetwork)."\");
								window.close();"
				));
		}
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
				case 'friend_list_block':
					$sRetHtml = $this->genFriendListBlock(bx_get('list'),bx_get('other'));
					break;
				case 'message_form':
					$sRetHtml = $this->genMessageForm(bx_get('list_id'),bx_get('username'));
					break;
				case 'send_invitation':
					$sRetHtml = $this->sendInvitation(bx_get('list'),bx_get('name_list'),bx_get('message'));
					break;
				case 'popup_message':
					$sRetHtml = $this->genPopupMessage(bx_get('message'),bx_get('caption'));
					break;
				case 'import_form':
					$sRetHtml = $this->genImportForm();
					break;
				default:
					break;
			}
		}
		//header('Content-type:application/json');
		echo $sRetHtml;

	}
	function actionGenDataTable($type){
		if(!$this->isAdmin())
		{
			$this->_oTemplate->displayAccessDenied();
			return;
		}

		if(empty($type))
			exit('bad ajax request');
		$sTable = 'sk_social_inviter_users';
		$sPrimaryKey = 'id';
		$sql_details = array(
				'user' => DATABASE_USER,
				'pass' => DATABASE_PASS,
				'db'   => DATABASE_NAME,
				'host' => DATABASE_HOST
		);
		if($type == 'statis'){
			$aColumns = array(
				array( 'db' => 'Profiles`.`NickName', 'dt' => 0 ),
				array( 'db' => 'sk_social_inviter_users`.`network',  'dt' => 1 ),
				array( 'db' => 'sk_social_inviter_users`.`invitation',   'dt' => 2 ),
			);

			require_once(BX_DIRECTORY_PATH_MODULES.'sandklock/social_inviter/lib/ssp.class.php');
			$mySQL['where'] = "`sk_social_inviter_users`.`date` >= '".$_GET['from']."' AND `sk_social_inviter_users`.`date` <= '".$_GET['to']."'";
			$mySQL['group'] = 'GROUP BY `sk_social_inviter_users`.`profile_id`,`sk_social_inviter_users`.`network`';
			$mySQL['column'] = '`Profiles`.`NickName`, `sk_social_inviter_users`.`network`, COUNT(*) as `invitation`';
			$aResponse = SSP::simple($_GET, $sql_details, $sTable, $sPrimaryKey, $aColumns,$mySQL);

		}
		if($type == 'user_statis'){

			$aColumns = array(
				array( 'db' => 'Profiles`.`NickName', 'dt' => 0 ),
				array( 'db' => 'sk_social_inviter_users`.`friend_identity','dt' => 1 ),
				array( 'db' => 'sk_social_inviter_users`.`network','dt' => 2 ),
				array( 'db' => 'sk_social_inviter_users`.`date','dt' => 3)
			);

			require_once(BX_DIRECTORY_PATH_MODULES.'sandklock/social_inviter/lib/ssp.class.php');
			if($_GET['id'] && $_GET['network'])
				$mySQL['where'] = "`sk_social_inviter_users`.`profile_id` = '".$_GET['id']."' AND `sk_social_inviter_users`.`network` = '".$_GET['network']."'";
			elseif($_GET['id'])
				$mySQL['where'] = "`sk_social_inviter_users`.`profile_id` = '".$_GET['id']."'";
			elseif($_GET['network'])
				$mySQL['where'] = "`sk_social_inviter_users`.`network` = '".$_GET['network']."'";
			else
				$mySQL['where'] = "";
			$mySQL['group'] = '';
			$mySQL['column'] = '`Profiles`.`NickName`,`sk_social_inviter_users`.`friend_identity`,`sk_social_inviter_users`.`network`,`sk_social_inviter_users`.`date`';
			$aResponse = SSP::simple($_GET, $sql_details, $sTable, $sPrimaryKey, $aColumns,$mySQL);

		}



		if(!empty($aResponse['data'])){
			// foreach($aResponse['data'] as $key=>$row)
				// $aResponse['data'][$key][0] = getNickName($row[0]);

			//echoDbg($aResponse);exit;

			echo json_encode($aResponse);
			exit;
		}else
			exit('Something wrong happened');
	}

	function actionParseCsv()
	{
		header('Content-type:application/json');
		foreach($_FILES as $file){
			if($file['size'] == 0)
			{
				exit(json_encode(array(
					'error' => 1,
					'err_info' => 'You did not upload a CSV file',
				)));
			}
			else if($file['error'] > 0)
			{
				exit(json_encode(array(
					'error' => 1,
					'err_info' => 'error code: ' . $file['error'],
				)));
			}
			else
			{
				$permit_file_types = array(
					'text/csv' => 'csv', 'text/x-csv' => 'csv',
					'text/comma-separated-values' => 'csv',
					'application/csv' => 'csv',
					'application/excel' => 'csv',
					'application/vnd.ms-excel' => 'csv',
					'application/vnd.msexcel' => 'csv',
					'text/anytext' => 'csv',
					'text/x-vcard' => 'vcf', 'application/vcard' => 'vcf',
					'text/anytext' => 'vcf', 'text/directory' => 'vcf',
					'text/x-vcalendar' => 'vcf', 'application/x-versit' => 'vcf',
					'text/x-versit' => 'vcf', 'application/octet-stream' => 'ldif',
				);

				$uploaded_file = $file['tmp_name'];
				$filetype = $file['type'];
				$filename = $file['name'];
				// Check file types
				if(!array_key_exists($filetype, $permit_file_types))
				{
					exit(json_encode(array(
						'error' => 1,
						'err_info' => 'File type is not supported',
					)));
				}
				$aContact = $this->getContactsFromCSVFile($uploaded_file, $filename);
				$aValidContacts = array();
				if($aContact['is_error'] == 0)
				{
					foreach($aContact['contacts'] as $sEmail => $sName)
					{
						if($this->isValidEmail($sEmail))
						{
							$aValidContacts[] = array(
								'email' => $sEmail,
								'receiver' => $sEmail,
								'username' => $sName ? $sName : $sEmail,
							);
						}
					}
				}
				exit(json_encode(array(
					'contacts' => $aValidContacts,
					'error' => $aContact['is_error'],
					'err_info' => $aContact['error_message'],
				)));
			}
		}
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
		echo DesignBoxAdmin(_t('_sk_social_inviter_setting_info'), $this->_oTemplate->parseHtmlByName('default_padding.html', array(
			'content' => _t('_sk_social_inviter_setting_info_content')
		)));
		return $this->_oTemplate->parseHtmlByName('default_padding',array('content'=>$sResult));
	}

	function getAdministrationStatistic()
	{
		$sDateFrom = date('Y-m-d', mktime(00, 00, 00, date('m'), 01));
		$sDateTo = date('Y-m-d', mktime(23, 59, 59, date('m')+1, 00));
		if(isset($_POST['view_from_date']) && $this->isValidDateTime($_POST['view_from_date']))
			$sDateFrom = $_POST['view_from_date'];
		if(isset($_POST['view_to_date']) && $this->isValidDateTime($_POST['view_to_date']))
			$sDateTo = $_POST['view_to_date'];
		$aForm = array(
			'form_attrs' => array(
				'name' => 'filter',
				'action' => '',
				'method' => 'post',
			),
			'inputs' => array(
				'view_from_date' => array(
					'type' => 'date',
					'name' => 'view_from_date',
					'caption' => _t('_sk_social_inviter_view_from_date'),
					'value' => $sDateFrom,
				),
				'view_to_date' => array(
					'type' => 'date',
					'name' => 'view_to_date',
					'caption' => _t('_sk_social_inviter_view_to_date'),
					'value' => $sDateTo,
				),
				'button' => array(
					'type' => 'submit',
					'value' => 'View',
				)
			),
		);
		$oForm = new BxTemplFormView($aForm);

		$sAjaxUrl = $this->_sModuleUri.'gen_data_table/statis?from='.$sDateFrom.'&to='.$sDateTo;
		$sJsDataTable = BX_DOL_URL_MODULES . 'sandklock/social_inviter/js/jquery.dataTables.js';
		$sCssFolderLink = BX_DOL_URL_MODULES . 'sandklock/social_inviter/templates/base/css/jquery.dataTables.min.css';

		$aVar = array(
					'ajax_url' => $sAjaxUrl ,
					'data_table' => $sJsDataTable,
					'css_url' => $sCssFolderLink,
		);

		$sTable = $this->_oTemplate->parseHtmlByName('table_statistic',$aVar);

		return array('form' => $this->_oTemplate->parseHtmlByName('default_padding',array('content'=>$oForm->getCode())) , 'table' => $sTable);
	}
	
	function getAdministrationUserStatistic()
	{
		$iUser = isset($_POST['user']) ? $_POST['user'] : '0';
		$sProvider = isset($_POST['provider']) ? $_POST['provider'] : '0';
		$aListUser = $this->_oDb->getAllSystemUser();
		$aComboUser = array('0' => 'All');
		foreach($aListUser as $aUser)
		{
			$aComboUser[$aUser['ID']] = $aUser['NickName'];
		}
		$aListProvider = $this->_oDb->getEnabledNetworks();
		$aComboProvider = array('0' => 'All');
		foreach($aListProvider as $aProvider)
		{
			$aComboProvider[$aProvider['name']] = $aProvider['name'];
		}
		$aForm = array(
			'form_attrs' => array(
				'name' => 'filter',
				'action' => '',
				'method' => 'post',
			),
			'inputs' => array(
				'user' => array(
					'type' => 'select',
					'name' => 'user',
					'caption' => _t('_sk_social_inviter_user_caption'),
					'values' => $aComboUser,
					'value' => $iUser
				),
				'provider' => array(
					'type' => 'select',
					'name' => 'provider',
					'caption' => _t('_sk_social_inviter_network_caption'),
					'values' => $aComboProvider,
					'value' => $sProvider
				),
				'button' => array(
					'type' => 'submit',
					'value' => 'View',
				)
			),
		);
		$oForm = new BxTemplFormView($aForm);

		$sAjaxUrl = $this->_sModuleUri.'gen_data_table/user_statis?id='.$iUser.'&network='.$sProvider;
		$sJsDataTable = BX_DOL_URL_MODULES . 'sandklock/social_inviter/js/jquery.dataTables.js';
		$sCssFolderLink = BX_DOL_URL_MODULES . 'sandklock/social_inviter/templates/base/css/jquery.dataTables.min.css';

		$aVar = array(
			'ajax_url' => $sAjaxUrl ,
			'data_table' => $sJsDataTable,
			'css_url' => $sCssFolderLink,
		);

		$sTable = $this->_oTemplate->parseHtmlByName('table_user_statistic',$aVar);

		return array('form' => $this->_oTemplate->parseHtmlByName('default_padding',array('content'=>$oForm->getCode())) , 'table' => $sTable);
	}

	function serviceGenInviterBlock($iId = 0){
		$aNetwork = $this->_oDb->getEnabledNetworks();

		$isOther = false;


		$aQueryParams = http_build_query(array(
			'app_id' => $this->_oConfig->getAppId(),
			'callback' => urlencode($this->_sModuleUri.'invite'),
			'scope' => 'user,friends,message'
		));


		foreach($aNetwork as $key=>$value){

			if($value['name'] == 'other'){
				$isOther = true;
				unset($aNetwork[$key]);
				continue;
			}
			$aNetwork[$key]['img_src'] = $this->_oTemplate->getImageUrl($value['logo']);
			//$aNetwork[$key]['login_url'] = $this->_oConfig->getApiLoginUrl().$value['name'].'?app_id='.$this->_oConfig->getAppId();
			$aNetwork[$key]['login_url'] = $this->_oConfig->getApiLoginUrl().$value['name'].'?'.$aQueryParams;
			$aNetwork[$key]['tooltip'] = $value['caption'];
		}

		$aVar = array(
			'bx_if:other' => array(
				'condition' => $isOther,
				'content' => array(
					'other_img' => $this->_oTemplate->getImageUrl('other.png'),
					'tooltip' => _t('_sk_social_inviter_other_tooltip')
				)
			),
			'loading_img' => $this->_oTemplate->getImageUrl('loading.gif'),
			'bx_repeat:networks' => $aNetwork,
		);

		echo $this->_oTemplate->parseHtmlByName('block_social_inviter.html',$aVar);
	}

	function sendInvitation($jReceiverList,$jNameList,$sMessage){
		$iMaxInvi = (int)getParam('sk_social_inviter_quantity_invitation');
		if(empty($jReceiverList))
			return _t('_sk_social_inviter_missing_friend');

		if( $iMaxInvi != 0){
			$iNumRequest = count(json_decode($jReceiverList,true));
			if(($iMaxInvi - $this->_oDb->getTotalInvitations($this->_iVisitorID)) - $iNumRequest < 0 )
				return _t('_sk_social_inviter_limited_invitation');
		}

		if(!$sMessage)
			return _t('_sk_social_inviter_missing_content');

		$session = BxDolSession::getInstance();
		$aData = $session->getValue('si_data');

		if($aData['expired_time'] - time() <= 0 && $aData['network'] != 'other')
			return _t('_sk_social_inviter_token_expired');

		if(getParam('reg_by_inv_only') == 'on')
			$sLinkAdd = 'idFriend='.$this->_iVisitorID;

		$sMessage = trim($sMessage);

		$sTitle= getParam('sk_social_inviter_invitation_title');

		switch($aData['network']){
			case 'google':
			case 'live':
			case 'other':
				$aEmail = json_decode($jReceiverList);
				$aRes = array();

				$sSiteUrl = !empty($sLinkAdd) ? BX_DOL_URL_ROOT.'?'.$sLinkAdd : getParam('sk_social_inviter_invitation_link');

				bx_import('BxDolEmailTemplates');
				$oEmailTemplates = new BxDolEmailTemplates();
				$sAvatar = $GLOBALS['oFunctions']->getMemberAvatar($this->_iVisitorID,'medium');
				$sNickName = getNickName($this->_iVisitorID);
				$aFriendName = json_decode($jNameList);
				$sProfileUrl = getProfileLink($this->_iVisitorID);
				
				foreach($aEmail as $key=>$sEmail){
					if(!$this->isValidEmail($sEmail))
						continue;
					
					$aFriend = $this->_oDb->getProfileByEmail($sEmail);
					
					$aMessage = $oEmailTemplates->parseTemplate('sk_social_inviter_email', array(
						'SiteUrl' => $sSiteUrl,
						'Message' => $sMessage,
						'Avatar' => $sAvatar,
						'FriendName' => $this->isValidEmail($aFriendName[$key]) ? '' : ' '.$aFriendName[$key],
						'NickName' => $sNickName,
						'ProfileUrl' => $sProfileUrl,
					));
					
					$aRes[] = sendMail($sEmail,$aMessage['subject'],$aMessage['body']);
				}
				$this->_oDb->createNetworkFriend($jNameList,$aData['network'],$this->_iVisitorID);
				$session->unsetValue('si_data');
				return 'email';
			break;
			case 'tumblr':
			/*case 'linkedin':
				if(strlen($sMessage) > 600)
					return _t('_sk_social_inviter_linkedin_long_message');
				$aPostData['title'] = $sTitle;*/
			case 'lastfm':
				if(strlen($sMessage) > 900)
					return _t('_sk_social_inviter_lastfm_long_message');
			case 'plurk':
				if(strlen($sMessage) > 178)
					return _t('_sk_social_inviter_plurk_long_message');
			case 'twitter':
				if(strlen($sMessage) > 117)
					return _t('_sk_social_inviter_twitter_long_message');
			case 'mailru':

				$sMessage = !empty($sLinkAdd) ? $sMessage.' '.BX_DOL_URL_ROOT.'?'.$sLinkAdd : $sMessage.' '.getParam('sk_social_inviter_invitation_link');

				$aPostData['message'] = $sMessage;
				$aPostData['friend_id'] = implode(',',json_decode($jReceiverList,true));

				$bResult = $this->makeSendMessageRequest($aData['network'],$aData['sk_token'],$aPostData);
				
                if($bResult === true){
					$this->_oDb->createNetworkFriend($jNameList,$aData['network'],$this->_iVisitorID);
					$session->unsetValue('si_data');
					return _t('_sk_social_inviter_send_message');
				}
				else return $bResult;
			break;
		}
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

	function genPopupMessage($sMessage,$sCapt){
		$sContent = MsgBox($sMessage);

		$sCaption = $sCapt == 'message' ? _t('_sk_social_inviter_message_caption') : _t('_sk_social_inviter_error_caption');

		$sCaptionItem = <<<BLAH
		<div class="dbTopMenu">
			<i class="bx-popup-element-close sys-icon times"></i>
		</div>
BLAH;
		return $GLOBALS['oFunctions']->transBox(DesignBoxContent($sCaption, $sContent, 11,$sCaptionItem),true);
	}

	function genMessageForm($jFriendId,$jFriendList){

		$aFriendList = json_decode($jFriendList,true);
		$aParsedList = array();

		foreach($aFriendList as $username){
			$aParsedList[]['username'] = $username;
		}

		$session = BxDolSession::getInstance();
		$aData = $session->getValue('si_data');

		$aVar = array(
			'send_invitation' => _t('_sk_social_inviter_send_invitation'),
			'error_missing_content' => _t('_sk_social_inviter_missing_content'),
			'error_twitter_long_message' => _t('_sk_social_inviter_twitter_long_message'),
			'error_plurk_long_message' => _t('_sk_social_inviter_plurk_long_message'),
			'error_long_message' => _t('_sk_social_inviter_long_message'),
			'lab_selected_friend' => _t('_sk_social_inviter_selected_friend'),
			'lab_message' =>  _t('_sk_social_inviter_message_header'),
			'lab_content' =>  _t('_sk_social_inviter_content_caption'),
			'friend_id' => $jFriendId,
			'friend_name' => $jFriendList,
			'bx_repeat:friends' => $aParsedList,
			'network' => $aData['network'],
			'send_message' => _t('_sk_social_inviter_send_message'),
			'max_length' => '',
			'content' => getParam('sk_social_inviter_invitation_message'),
			'back' => _t('_sk_social_inviter_back_button'),
		);

		switch($aData['network']){
			case 'plurk':
				$aVar['max_length'] = _t('_sk_social_inviter_message_length',178-strlen(trim($aVar['content'])));
			break;
			case 'twitter':
				$aVar['max_length'] = _t('_sk_social_inviter_message_length',117-strlen(trim($aVar['content'])));
			break;
			/*case 'linkedin':
				$aVar['max_length'] = _t('_sk_social_inviter_message_length',600-strlen(trim($aVar['content'])));
			break;*/
			case 'lastfm':
				$aVar['max_length'] = _t('_sk_social_inviter_message_length',900-strlen(trim($aVar['content'])));
			break;
		}

		$sHtmlMessageForm = $this->_oTemplate->parseHtmlByName('message_form.html',$aVar);

		$sCaptionItem = <<<BLAH
		<div class="dbTopMenu">
			<i class="bx-popup-element-close sys-icon times"></i>
		</div>
BLAH;

		$sMessageFormAjx = $GLOBALS['oFunctions']->transBox(DesignBoxContent(_t('_sk_social_inviter_block'), $sHtmlMessageForm, 11, $sCaptionItem), true);

		return $sMessageFormAjx;
	}

	function genImportForm(){

		$sHtmlMessageForm = $this->_oTemplate->parseHtmlByName('import_form.html',array('file_size' => ini_get('upload_max_filesize')));

		$sCaptionItem = <<<BLAH
		<div class="dbTopMenu">
			<i class="bx-popup-element-close sys-icon times"></i>
		</div>
BLAH;

		$sMessageFormAjx = $GLOBALS['oFunctions']->transBox(DesignBoxContent(_t('_sk_social_inviter_block'), $sHtmlMessageForm, 11, $sCaptionItem), true);

		return $sMessageFormAjx;

	}

	function genFriendListBlock($jFriendList,$isOther = false){

		$session = BxDolSession::getInstance();
		$aData = $session->getValue('si_data');

		if($isOther){
			$aData['network'] = 'other';
			$session->setValue('si_data',$aData);
		}

		if($isOther == 'typing'){
			$aFriendList = explode(',',$jFriendList);
			foreach($aFriendList as $key=>$value){
				if(empty($value)){
					unset($aFriendList[$key]);
					continue;
				}
				$aFriendList[$key] = array('email' => $value, 'username' => '', 'receiver' => $value);
			}
			$aFriendList = array_values(array_unique($aFriendList,SORT_REGULAR));
		}
		else
			$aFriendList = json_decode($jFriendList,true);

		$aExistedFriend = array();
		$sEmail = '';
		$sIdentity = '';

		switch($aData['network']){
			case 'google':
			case 'other':
			case 'live':
				$count = 0;
                $sEmail = '';
                if(!empty($aFriendList)) {
                    foreach($aFriendList as $key=>$value){
    					if(!empty($value['email'])){
    						if(empty($sEmail))
    							$sEmail = process_db_input($value['email']);
    						else
    							$sEmail = $sEmail."','".process_db_input($value['email']);
    					}
    				}
                }
				
				$aProfiles = $this->_oDb->getAllProfileByEmail($sEmail);
				if(!empty($aProfiles)){
					foreach($aProfiles as $aProfile){
						if($aProfile['ID'] == $this->_iVisitorID)
							continue;
						$aExistedFriend[$count]['e_id'] = $aProfile['ID'];
						$aExistedFriend[$count]['e_link'] = BX_DOL_URL_ROOT.$aProfile['NickName'];
                        
                        $avatar = $GLOBALS['oFunctions']->getMemberAvatar($aProfile['ID'],'small');
						$aExistedFriend[$count]['e_avatar'] = !empty($avatar)? '<img src="' . $avatar . '" width="21" height="21"/>' : '<i class="sys-icon user" style="font-size: 20px;"></i>';
						$aExistedFriend[$count]['e_email'] = $aProfile['Email'];
						$aExistedFriend[$count]['e_username'] = $aProfile['NickName'];
						if($this->isFriend($aProfile['ID'],1))
							$aExistedFriend[$count]['e_display_btn']= '<label class="sk_status" style="line-height: 38px;"><a href="'.getProfileLink($aProfile['ID']).'">'._t('_sk_social_inviter_your_friend').'</a></label>';
						elseif($this->isFriend($aProfile['ID'],0))
							$aExistedFriend[$count]['e_display_btn']= '<label class="sk_status" style="line-height: 38px;">'._t('_sk_social_inviter_friend_request').'</label>';
						else
							$aExistedFriend[$count]['e_display_btn']= '<input type="button" class="bx-btn bx-btn-small" value="'._t('_sk_social_inviter_add_friend').'" onclick="add_friend(\''.$aProfile['ID'].'\')"/>';
							//$aExistedFriend[$count]['e_display_btn'] = !isFriendRequest($this->_iVisitorID,$aProfile['ID']) ? 'display:none':'';
						$count++;
					}
				}
			break;
			default:
				if($this->getInstance('SkSocialLoginModule')){
					$count = 0;
                    if(!empty($aFriendList)) {
                        foreach($aFriendList as $key=>$value){
    						if(!empty($value['identity'])){
    							if(empty($sIdentity))
    								$sIdentity = process_db_input($value['identity']);
    							else
    								$sIdentity = $sIdentity."','".process_db_input($value['identity']);
    						}
    					}
                    }
					
					$aProfiles = $this->_oDb->getAllProfileByIdentity($sIdentity);
					if(!empty($aProfiles)){
						foreach($aProfiles as $aProfile){
							if($aProfile['ID'] == $this->_iVisitorID)
								continue;
							$aExistedFriend[$count]['e_id'] = $aProfile['ID'];
							$aExistedFriend[$count]['e_link'] = BX_DOL_URL_ROOT.$aProfile['NickName'];
                            
                            $avatar = $GLOBALS['oFunctions']->getMemberAvatar($aProfile['ID'],'small');
							$aExistedFriend[$count]['e_avatar'] = !empty($avatar)? '<img src="' . $avatar . '" width="21" height="21"/>' : '<i class="sys-icon user" style="font-size: 20px;"></i>';
							$aExistedFriend[$count]['e_email'] = $aProfile['Email'];
							$aExistedFriend[$count]['e_username'] = $aProfile['NickName'];
							if($this->isFriend($aProfile['ID'],1))
								$aExistedFriend[$count]['e_display_btn']= '<label class="sk_status" style="line-height: 38px;"><a href="'.getProfileLink($aProfile['ID']).'">'._t('_sk_social_inviter_your_friend').'</a></label>';
							elseif($this->isFriend($aProfile['ID'],0))
								$aExistedFriend[$count]['e_display_btn']= '<label class="sk_status" style="line-height: 38px;">'._t('_sk_social_inviter_friend_request').'</label>';
							else
								$aExistedFriend[$count]['e_display_btn']= '<input type="button" class="bx-btn bx-btn-small" value="'._t('_sk_social_inviter_add_friend').'" onclick="add_friend(\''.$aProfile['ID'].'\')"/>';
								//$aExistedFriend[$count]['e_display_btn'] = !isFriendRequest($this->_iVisitorID,$aProfile['ID']) ? 'display:none':'';
							$count++;
						}
					}
				}
			break;
		}

		$iTotalInvit = $this->_oDb->getTotalInvitations($this->_iVisitorID);
		$iDefaultInvit = intval(getParam('sk_social_inviter_quantity_invitation'));

		if($iDefaultInvit !== 0)
			$iNumInvitation = $iDefaultInvit - $iTotalInvit;
		else
			$iNumInvitation = 'unlimited';

		if($iNumInvitation != 'unlimited')
			$iNumInvitation = $iNumInvitation < 0 ? 0 : $iNumInvitation;

		$aVar = array(
			'friend_request' => _t('_sk_social_inviter_friend_request'),
			'lab_friend_list' => _t('_sk_social_inviter_friend_list'),
			'lab_existed_member' => _t('_sk_social_inviter_existed_member'),
			'lab_select_all' => _t('_sk_social_inviter_select_all'),
			'lab_next' => _t('_sk_social_inviter_next'),
			'lab_filter' => _t('_sk_social_inviter_filter'),
			'form_note' => _t('_sk_social_inviter_form_note'),
			'sfriend_note' => _t('_sk_social_inviter_limited_mess',$iNumInvitation),
			'total_invitations' =>_t('_sk_social_inviter_total_invitations',$iTotalInvit,$iNumInvitation),
			'total_friends' => _t('_sk_social_inviter_total_friends',$this->_oDb->getTotalFriends($this->_iVisitorID)),
			'network' => $aData['network'],
			'bx_repeat:friends' => $aFriendList,
			'num_invitation' => $iNumInvitation,
			'limit_error' => _t('_sk_social_inviter_limited_invitation'),
			'bx_repeat:e_friends' => array(),
            'waiting_text' => _t('_sk_social_inviter_waiting_text')
		);
		if(!empty($aExistedFriend)){
			$aVar['bx_repeat:e_friends'] = $aExistedFriend;
			$aVar['display_e_friend'] = '';
			$aVar['display_e_message'] = 'display:none;';
			$aVar['e_message'] = '';
		}else{
			$aVar['display_e_friend'] = 'display:none;';
			$aVar['display_e_message'] = '';
			$aVar['e_message'] = $isOther ? _t('_sk_social_inviter_no_friend',getParam('site_title')) : _t('_sk_social_inviter_no_network_friend',ucfirst($aData['network']),getParam('site_title'));
			$aVar['loading_img'] = '';
			$aVar['display_loading_img'] = '';
		}

		//echoDbgLog($aExistedFriend);

		$sHtmlFriendList = $this->_oTemplate->parseHtmlByName('friend_list.html',$aVar);

		$sCaptionItem = <<<BLAH
		<div class="dbTopMenu">
			<i class="bx-popup-element-close sys-icon times"></i>
		</div>
BLAH;

		$sFriendListBlockAjx = $GLOBALS['oFunctions']->transBox(DesignBoxContent(_t('_sk_social_inviter_block'), $sHtmlFriendList, 11, $sCaptionItem), true);

		return $sFriendListBlockAjx;
	}

	function serviceUpdateInvitationLink(){
		$bResult = $this->_oDb->updateInvitationLink();
		return $bResult;
	}

	private function checkLogged(){
		if($this->_iVisitorID == 0)
		{
			$sUrl = BX_DOL_URL_ROOT;
			header("Location: {$sUrl}");
			exit;
		}
	}

	private function getReceiver($sNetwork,$aFriendInfo){
		switch($sNetwork){
			case 'lastfm':
				return $aFriendInfo['username'];
			//case 'linkedin':
			case 'tumblr':
			case 'mailru':
			case 'twitter':
			case 'plurk':
				return $aFriendInfo['id'];
			case 'other':
			case 'google':
			case 'live':
				return $aFriendInfo['email'];
		}
	}

	private function makeSendMessageRequest($sNetwork,$sToken,$aPostData){
		$aQueryParam = array(
			'token' => $sToken,
		);

		$aQueryParam = array_merge($aQueryParam,$aPostData);

		$sSig = $this->signRequest($aQueryParam);
		$aQueryParam['sig'] = $sSig;

		$aContextParam = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($aQueryParam),
			)
		);
		$sUrl = $this->_oConfig->getApiServiceUrl().'message';

		$context = stream_context_create($aContextParam);
		$aResult = json_decode(file_get_contents($sUrl,false,$context), true);
        
        return isset($aResult['success']) ? true : $aResult['error'];
	}

	private function isFriend($iFriendId,$iCheck){
		return $this->_oDb->getFriendRelationship($this->_iVisitorID,$iFriendId,$iCheck);
	}

	private function isAdmin()
	{
		return isAdmin($this->_iVisitorID) || isModerator($this->_iVisitorID);
	}

	private function isValidDateTime($date)
	{
	    if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches))
	    {
	        if (checkdate($matches[2], $matches[3], $matches[1])) {
	            return true;
	        }
	    }
	    return false;
	}

	private function getContactsFromCSVFile($uploaded_file, $filename)
	{
		$contacts = array();
		$friends = array();
		$is_error = 0;
		$message = '';
		$ci_contacts = array();
		// list the permitted file type
		for(;;)
		{
			if(is_uploaded_file($uploaded_file))
			{
				$fh = fopen($uploaded_file, "r");
				if($this->EndsWith(mb_strtolower($filename), 'csv'))
				{
					// Process CSV file type
					$i = 0;
					$row = fgetcsv($fh, 1024, ',');
					$first_name_pos = -1;
					$email_pos = -1;
					$first_display_name = -1;
					$count = count($row);
					for($i = 0; $i < $count; $i = $i + 1)
					{
						if($row[$i] == "First" || $row[$i] == "First Name")
						{
							$first_name_pos = $i;
						}
						elseif($row[$i] == "E-mail Address" || $row[$i] == "Email")
						{
							$email_pos = $i;
						}
						elseif($row[$i] == "First Name" || $row[$i] == "First")//yahoo format oulook
						{
							$first_display_name = $i;
						}
						else
						{
							// do nothing
						}
					}
					if(($email_pos == -1) || ($first_name_pos == -1))
					{
						$is_error = 1;
						$message = "Invalid file format!";
						break;
					}
					else
					{
						if($first_display_name == -1)
							$first_display_name = $first_name_pos;
					}
					while(($row = fgetcsv($fh)) != false)
					{
						if(!empty($row[$email_pos]))
						{
							$contacts[] = array(
								'email' => $row[$email_pos],
								'name' => empty($row[$first_name_pos])
									? (empty($row[$first_display_name]) ? $row[$email_pos] : $row[$first_display_name])
									: $row[$first_name_pos]
							);
						}
					}
					fclose($fh);
				}
				elseif($this->EndsWith(mb_strtolower($filename), 'vcf'))
				{
					$file_size = filesize($uploaded_file);
					if($file_size == 0)
					{
						$is_error = 1;
						$message = 'Empty file!';
						break;
					}
					$vcf = fread($fh, filesize($uploaded_file));
					fclose($fh);
					$vCard = new VCardTokenizer($vcf);
					$contacts = array();
					$result = $vCard->next();
					$contact = array();
					while($result)
					{
						if(mb_strtolower($result->name) == 'email')
						{
							$contact['email'] = $result->getStringValue();
						}
						else if(mb_strtolower($result->name) == 'n')
						{
							$name = $result->getStringValue();
							$parts = explode(";", $name, 2);
							if($parts[1] == '')
							{
								$contact['first_name'] = $parts[0];
								$contact['name'] = $contact['first_name'];
							}
							else
							{
								$contact['last_name'] = $parts[0];
								$contact['first_name'] = $parts[1];
								$contact['name'] = $contact['first_name'] . ' ' . $contact['last_name'];
							}
						}
						else if(mb_strtolower($result->name) == 'org')
						{
							$contact['company'] = $result->getStringValue();
						}
						else if(mb_strtolower($result->name) == 'title')
						{
							$contact['position'] = $result->getStringValue();
						}
						$result = $vCard->next();
					}
					if((!isset($contact['email'])) || (!isset($contact['name'])))
					{
						$is_error = 1;
						$message = "Invalid file format!";
						break;
					}
					if(isset($contact['email']))
					{
						if($this->validEmail($contact['email']))
						{
							$contacts[] = array(
								'email' => $contact['email'],
								'name' => $contact['name']
							);
						}
						else
						{
							$is_error = 0;
							$message = "There's some error in your contact file";
						}
					}
				}
				elseif($this->EndsWith(mb_strtolower($filename), 'ldif'))//thunderbirth
				{
					$thunder_data = fread($fh, filesize($uploaded_file));
					$rows = explode(PHP_EOL, $thunder_data);
					$name = "";
					$email = "";
					$contacts = array();
					foreach($rows as $index => $row)
					{
						try
						{
							@list($key, $data) = @explode(':', $row);
							if($key == 'cn')
								$name = $data;
							if($key == 'mail')
								$email = trim($data);
							if($name != "" && $email != "")
							{
								$contacts[] = array(
									'email' => $email, 'name' => $name
								);
								$name = "";
								$email = "";
							}
						}
						catch(Exception $ex)
						{}
					}
				}
				else
				{
					$is_error = 1;
					$message = "Unknown file type!";
				}
			}
			if(empty($contacts))
			{
				$is_error = 1;
				$message = "There is no contact in your address book";
				break;
			}
			foreach($contacts as $value)
			{
				$ci_contacts["{$value["email"]}"] = $value["name"];
			}
			break;
		}
		$returns['contacts'] = $ci_contacts;
		$returns['is_error'] = $is_error;
		$returns['error_message'] = $message;
		return $returns;
	}

	private function endsWith($FullStr, $EndStr)
    {
		// Get the length of the end string
		$StrLen = strlen($EndStr);

		// Look at the end of FullStr for the substring the size of EndStr

		$FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
		// If it matches, it does end with EndStr
		return $FullStrEnd == $EndStr;
	}

	private function isValidEmail($email){
		$regex = "/^([a-z0-9\+\_\-\.]+)@([a-z0-9\+\_\-\.]+)$/i";
		if(preg_match($regex,$email))
			return true;
		return false;
	}

	private function signRequest($data){

		ksort($data);

		$str_data = '';

		foreach($data as $key=>$value)
			$str_data .= "$key=$value";

		return md5($this->_oConfig->getSecretKey().$str_data);
	}
    
    public function actionApiSettings() {
        if(!$this->isAdmin())
		{
			$this->_oTemplate->displayAccessDenied();
			return;
		}
		$this->_oTemplate->pageStart();
        
        $iId = $this->_oDb->getApiSettings();
		if(empty($iId))
			return MsgBox(_t('_sys_request_page_not_found_cpt'));
		bx_import('BxDolAdminSettings');
		$mixedResult = '';
        $oSettings = new BxDolAdminSettings($iId);
		if(isset($_POST['save']) && isset($_POST['cat']))
			$mixedResult = $oSettings->saveChanges($_POST);
		
		$sResult = $oSettings->getForm();
		if($mixedResult !== true && !empty($mixedResult))
			$sResult = $mixedResult . $sResult;
		echo DesignBoxAdmin(_t('_sk_social_inviter_setting_info'), $this->_oTemplate->parseHtmlByName('default_padding.html', array(
			'content' => _t('_sk_social_inviter_api_setting_guide_content')
		)));
		
        $aVars = array('content' => $sResult);

        echo $this->_oTemplate->adminBlock($this->_oTemplate->parseHtmlByName('default_padding',
            $aVars), _t('_sk_social_inviter_api_setting_caption'));
        $this->_oTemplate->pageCodeAdmin(_t('_sk_social_inviter_api_setting_caption'));
    }
}

?>
