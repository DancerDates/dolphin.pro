<?php

bx_import("BxDolInstaller");

class SkSocialInviterInstaller extends BxDolInstaller {
    function SkSocialInviterInstaller($aConfig) {
        parent::__construct($aConfig);
    }
    
	function install($aParams) {
        $oModuleDb = new BxDolModuleDb();
        $iCheckSocialAll = $oModuleDb->getOne("SELECT `id` FROM `sys_menu_admin` WHERE `name` = 'Sandklock SocialAll API Settings'");
        
        if(empty($iCheckSocialAll)) {
            $iMax = (int)$oModuleDb->getOne("SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2'") + 1;
            $oModuleDb->query("INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
            (2, 'Sandklock SocialAll API Settings', 'SocialAll API Settings', '" . BX_DOL_URL_ROOT . "modules/?r=social_inviter/api_settings/', 'SocialAll API settings by Sandklock Developments','modules/sandklock/social_inviter/|socialall_logo.png', {$iMax});");
        }
       
		$aResult = parent::install($aParams);
        
		if($aResult['result']) {
			BxDolService::call($this->_aConfig['home_uri'], 'update_invitation_link');
		}
		return $aResult;
	}
    
    function uninstall($aParams) {
        $oModuleDb = new BxDolModuleDb();
        
        $oSocialLogin = $oModuleDb->getOne("SELECT `id` FROM `sys_modules` WHERE `uri` = 'social_login'");
        $oSocialPosting = $oModuleDb->getOne("SELECT `id` FROM `sys_modules` WHERE `uri` = 'social_posting'");
        
        if(empty($oSocialPosting) && empty($oSocialLogin)) {
            $oModuleDb->query("DELETE FROM `sys_menu_admin` WHERE `name` = 'Sandklock SocialAll API Settings'");
            
            $iCateID = $oModuleDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock SocialAll API Settings' LIMIT 1");
            $oModuleDb->query("DELETE FROM `sys_options_cats` WHERE `ID` = {$iCateID}");
            $oModuleDb->query("DELETE FROM `sys_options` WHERE `kateg` = {$iCateID}");
        } else {
            if(!empty($oSocialLogin)) {
                $oModuleDb->query("UPDATE `sys_menu_admin` SET `url`='" . BX_DOL_URL_ROOT . "modules/?r=social_login/api_settings/', `icon`='modules/sandklock/social_login/|socialall_logo.png' WHERE `name` = 'Sandklock SocialAll API Settings'");
            } else if(!empty($oSocialPosting)) {
                $oModuleDb->query("UPDATE `sys_menu_admin` SET `url`='" . BX_DOL_URL_ROOT . "modules/?r=social_posting/api_settings/', `icon`='modules/sandklock/social_posting/|socialall_logo.png' WHERE `name` = 'Sandklock SocialAll API Settings'");
            }
        }
        
        return parent::uninstall($aParams);
    }
}

?>
