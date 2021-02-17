<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

bx_import('BxDolModuleDb');

class AqbEVUDb extends BxDolModuleDb {	
	/*
	 * Constructor.
	 */
	
	function __construct(&$oConfig) {
		parent::__construct($oConfig);
		$this -> _oConfig = &$oConfig;
	}
	  	
	
	function getAllPrivacy(){
		return $this -> getAll("SELECT * FROM `sys_privacy_actions` WHERE `module_uri` = 'aqb_cprivacy'");
	}
	
	function deleteMemberPrivacy($iAuthorID){
		if (!(int)$iAuthorID) return false;
		
		return $this -> query("DELETE FROM `{$this->_sPrefix}privacy` WHERE `author_id` = '{$iAuthorID}'");
	}	
	
	function savePrivacySettings(&$aPrivacy, $iAuthorID){
		if (empty($aPrivacy) || !(int)$iAuthorID) return false;
		
		unset($aPrivacy['csrf_token']);
		
		$this -> deleteMemberPrivacy($iAuthorID);
		foreach($aPrivacy as $sKey => $sValue){
			if ($sKey == 'contact_im'){ 
				$this -> setIMPrivacy($iAuthorID, $sValue);
			}
			
			$sType = str_replace('contact_', '', $sKey);	
			
			if ($sType){
				$this -> query("REPLACE INTO `{$this->_sPrefix}privacy` SET `allow_contact_to` = '{$sValue}', `type` = '$sType', `author_id` = '{$iAuthorID}'");
			}	
		}
		
		return true;
	}
	
	function getPrivacyValue($iProfileID, $sActionName){
		if (!(int)$iProfileID || !$sActionName) return false;
		
		if ($sActionName == 'contact_im' ) return $this -> getIMPrivacy($iProfileID);
		
		return $this -> getOne("SELECT `allow_contact_to` FROM `{$this->_sPrefix}privacy` WHERE `author_id` = '{$iProfileID}' AND `type` = '" . str_replace('contact_', '', $sActionName) . "' LIMIT 1");
	}
	
	function getObjectInfo($iObjectId, $sType){
		return $this -> getRow("SELECT `allow_contact_to` as `group_id` FROM `{$this->_sPrefix}privacy` WHERE `author_id` = '{$iObjectId}' AND `type` = '{$sType}' LIMIT 1");
    }
	
	function getIMPrivacy($iAuthorID){
		if (!$this -> isModuleInstalled()) return '';
		return $this -> getOne("SELECT `allow_contact_to` FROM `bx_simple_messenger_privacy` WHERE `author_id` = '{$iAuthorID}' LIMIT 1");
	}
	
	function setIMPrivacy($iAuthorID, $iValue){
		if (!$this -> isModuleInstalled() || !(int)$iAuthorID || !(int)$iValue) return '';
		return $this -> query("REPLACE INTO `bx_simple_messenger_privacy` SET `allow_contact_to` = '{$iValue}', `author_id` = '{$iAuthorID}'");
	}
	
	function isModuleInstalled($sModule = 'simple_messenger'){
		return (int)$this -> getOne("SELECT COUNT(*) FROM `sys_modules` WHERE `uri` = '{$sModule}' LIMIT 1") == 1;
	}
	
	function checkIfModuleInstalled($sName){
		switch($sName){
			case 'contact_schat': return $this -> isModuleInstalled('aqb_simple_chat');
			case 'contact_im': return $this -> isModuleInstalled();
			case 'contact_vim': return $this -> isModuleInstalled('messenger');
		}		
		
		return true;
	}
}
?>