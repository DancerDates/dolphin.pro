<?php

require_once(BX_DIRECTORY_PATH_INC . 'profiles.inc.php');

check_logged();

bx_import('BxDolRequest');
BxDolRequest::processAsAction($GLOBALS['aModule'], $GLOBALS['aRequest']);


/*bx_import('BxDolRequest');
class SkSocialLoginRequest extends BxDolRequest {

    function SkSocialLoginRequest() {
        parent::BxDolRequest();
    }

    function processAsAction($aModule, &$aRequest, $sClass = "Module") {
        $sClassRequire = $aModule['class_prefix'] . $sClass;
        $oModule = BxDolRequest::_require($aModule, $sClassRequire);
        return BxDolRequest::processAsAction($aModule, $aRequest, $sClass);
    }
}

SkSocialLoginRequest::processAsAction($GLOBALS['aModule'], $GLOBALS['aRequest']);
*/
?>