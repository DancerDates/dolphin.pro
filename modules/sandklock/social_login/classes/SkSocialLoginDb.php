<?php
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolDb.php' );

class SkSocialLoginDb extends BxDolModuleDb {

	var $_sTablePrefix;
	var $_sTableNetworks;
	var $_sTableUsers;
	/*
	 * Constructor.
	 */
	function SkSocialLoginDb(&$oConfig) {
		parent::BxDolDb();
		$this->_oConfig = $oConfig;
		$this->_sTablePrefix = $this->_oConfig->getDbPrefix();
		$this->_sTableNetworks = $this->_sTablePrefix.'networks';
		$this->_sTableUsers = $this->_sTablePrefix.'users';
	}
	
	function getSettings(){
		return (int) $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock Social Login Setting' LIMIT 1");
	}
	
	function getApiSettings()
    {
        return (int) $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock Social Login Api Setting' LIMIT 1");
    }
	
	function getAllNetworks(){
		$sql = " SELECT * FROM `{$this->_sTableNetworks}` ORDER BY `order` ASC";
		return $this->getAll($sql);
	}
	
	function getEnabledNetworks(){
		$sql = " SELECT * FROM `{$this->_sTableNetworks}` WHERE `status` = 'enabled' ORDER BY `order` ASC";
		return $this->getAll($sql);
	}
	
	function getNetworkUserByIdentity($sIdentity){
		$sIdentity = process_db_input($sIdentity);
		$sql = " SELECT * FROM `{$this->_sTableUsers}` WHERE `identity` = '{$sIdentity}' ";
		$result = $this->getRow($sql);
		
		return !empty($result) ? $result : false;
	}
	
	function deleteNetworkUserBy($sNetwork,$iId){
		$iId = process_db_input($iId);
		$sNetwork = process_db_input($sNetwork);
		$sql = " DELETE FROM `{$this->_sTableUsers}` WHERE `network` = '{$sNetwork}' AND `profile_id` = '{$iId}' ";
		return $this->query($sql);
	}
	
	function getNetworkUsername($sNetwork,$iId){
		$iId = process_db_input($iId);
		$sNetwork = process_db_input($sNetwork);
		$sql = " SELECT `username` FROM `{$this->_sTableUsers}` WHERE `network` = '{$sNetwork}' AND `profile_id` = '{$iId}' ";
		$result = $this->getOne($sql);
		
		return !empty($result) ? $result : false;
	}
	
	function getNetworkUserBy($sNetwork,$iId){
		$iId = process_db_input($iId);
		$sNetwork = process_db_input($sNetwork);
		$sql = " SELECT * FROM `{$this->_sTableUsers}` WHERE `network` = '{$sNetwork}' AND `profile_id` = '{$iId}' ";
		$result = $this->getRow($sql);
		
		return !empty($result) ? $result : false;
	}
	
	function getNetworkUserById($iId){
		$iId = process_db_input($iId);
		$sql = " SELECT * FROM `{$this->_sTableUsers}` WHERE `profile_id` = '{$iId}' ";
		$result = $this->getAll($sql);
		
		return !empty($result) ? $result : false;
	}
	
	function getProfileByEmail($sEmail){
		$sEmail = process_db_input($sEmail);
		$sql = " SELECT * FROM `Profiles` where `Email` = '{$sEmail}' ";
		$result = $this->getRow($sql);
		
		return !empty($result) ? $result : false;
	}
	
	function getProfileByName($sName){
		$sName = process_db_input($sName);
		$sql = " SELECT * FROM `Profiles` where `NickName` = '{$sName}' ";
		$result = $this->getRow($sql);
		
		return !empty($result) ? $result : false;
	}
	
	function createProfile($aParam){
		$sField = implode("`,`",array_keys($aParam));
		$sValue = implode("','",array_map("process_db_input",array_values($aParam)));
		
		$sql = " INSERT INTO `Profiles`(`{$sField}`) values('{$sValue}') ";
		
		$this->query($sql);
		
		return $this->getAffectedRows();
	}
	
	function createNetworkUser($aParam){
		$sField = implode("`,`",array_keys($aParam));
		$sValue = implode("','",array_map("process_db_input",array_values($aParam)));
		
		$sql = " INSERT INTO `{$this->_sTableUsers}`(`{$sField}`) values('{$sValue}') ";
		$this->query($sql);
		
		$iAffectedRow = $this->getAffectedRows();
		
		if( null !== BxDolModule::getInstance('SkSocialPostingModule') &&  null !== BxDolModule::getInstance('SkSocialSettingModule')){
			$sField = str_replace('sk_token','token',$sField);
			$sql = " INSERT INTO `sk_social_posting_users`(`{$sField}`) values('{$sValue}') ";
			$this->query($sql);
		}
		
		return $iAffectedRow;
	}
	
	function updateNetworkOrder($aNetwork){
		$order = 1;
		$sWhenSql = '';
		foreach($aNetwork as $network){
			$network = process_db_input($network);
			$sWhenSql .= " WHEN '{$network}' THEN {$order} ";
			$order++;
		}
		$sql = "UPDATE `{$this->_sTableNetworks}` SET `order` = CASE `name` {$sWhenSql} END";
				
		return $this->query($sql);
	}
	function updateNetworkStatus($sNetwork,$sStatus){
		$sNetwork = process_db_input($sNetwork);
		$sStatus = process_db_input($sStatus);
		$sql = " UPDATE `{$this->_sTableNetworks}` SET `status` = '{$sStatus}' WHERE `name` = '{$sNetwork}' ";
		return $this->query($sql);
	}

	function deleteNetworkUser($iProfileId) {
		$sql = "DELETE FROM `{$this->_sTableUsers}` WHERE `profile_id` = {$iProfileId}";
		return $this->query($sql);
	}
    
    function checkNetworksExists($networks) {
        $sql = "SELECT `name` FROM `{$this->_sTableNetworks}` WHERE `name` in ({$networks})";
        return $this->getAll($sql);
    }
    
    function insertNetwork($sNetwork, $sNetUrl) {
        $iMaxOrder = (int)$this->getOne("SELECT MAX(`order`) FROM `{$this->_sTableNetworks}`") + 1;
        $sql = "INSERT INTO `{$this->_sTableNetworks}`(`name`, `logo`, `status`, `description`, `profile_url`, `order`) VALUES 
            ('{$sNetwork}','{$sNetwork}.png','disabled','{$sNetwork} logo','{$sNetUrl}',{$iMaxOrder})";
        return $this->query($sql);
    }
    
    function applyTheme($sTheme, $sSize) {
        $iSettingsId = (int) $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock Social Login Theme'");
        if(!$iSettingsId) {
            $iMax = (int)$this->getOne("SELECT MAX(`menu_order`) + 1 FROM `sys_options_cats`");
            $sResult = $this->query("INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Sandklock Social Login Theme', {$iMax});");
        
            if($sResult)
                $iSettingsId = $this->lastId();
            if(!$iSettingsId)
                return false;
        }
            
        $sThemeSetting = $this->getRow("SELECT * FROM `sys_options` WHERE `Name` = 'sk_social_login_theme_applied'");
        if(!empty($sThemeSetting)) {
            setParam('sk_social_login_theme_applied', $sTheme);
        } else {
            return $this->query("INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`)  VALUES
                    ('sk_social_login_theme_applied', '{$sTheme}', {$iSettingsId}, 'Theme Applied of icons', 'text', '', '', 1, '')");
        }
        
        if((int)$sSize) {
            $sThemeResize = $this->getRow("SELECT * FROM `sys_options` WHERE `Name` = 'sk_social_login_theme_resize'");
            if(!empty($sThemeResize)) {
                setParam('sk_social_login_theme_resize', $sSize);
            } else {
                return $this->query("INSERT IGNORE INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`)  VALUES
                        ('sk_social_login_theme_resize', '{$sSize}', {$iSettingsId}, 'Theme size of icons', 'text', '', '', 2, '')");
            }
        }
        
        return 1;
    }
    
    function getOptionsByName( $sName ) {
        return $this->getRow("SELECT * FROM `sys_options` WHERE `Name` = '{$sName}'");
    }
    
    function removeApiUrl() {
        $this->query("DELETE FROM `sys_options` WHERE `Name` in ('sk_social_login_api_login_url', 'sk_social_login_api_service_url')");
    }
}
?>