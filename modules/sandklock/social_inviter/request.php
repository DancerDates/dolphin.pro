<?php

require_once(BX_DIRECTORY_PATH_INC . 'profiles.inc.php');

check_logged();

bx_import('BxDolRequest');

class SkSocialInviterRequest extends BxDolRequest {

    function SkSocialInviterRequest() {
        parent::BxDolRequest();
    }

    static function processAsAction($aModule, &$aRequest, $sClass = "Module") {
        $sClassRequire = $aModule['class_prefix'] . $sClass;
        $oModule = BxDolRequest::_require($aModule, $sClassRequire);
        if($oModule->_iVisitorID > 0)
        {
            $aOpt = array(
    			 	'module_url' => $oModule->_sModuleUri,
    		);
    		$GLOBALS['oTopMenu']->setCustomSubActions($aOpt, 'sk_social_inviter_', true);
        }
        return BxDolRequest::processAsAction($aModule, $aRequest, $sClass);
    }
}

SkSocialInviterRequest::processAsAction($GLOBALS['aModule'], $GLOBALS['aRequest']);

?>