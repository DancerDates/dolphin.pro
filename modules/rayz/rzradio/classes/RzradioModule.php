<?php
/**
 * @version 1.0
 * @copyright Copyright (C) 2014 rayzzz.com. All rights reserved.
 * @license GNU/GPL2, see LICENSE.txt
 * @website http://rayzzz.com
 * @twitter @rayzzzcom
 * @email rayzexpert@gmail.com
 */
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolModule.php');

$sFile = dirname(__FILE__) . '/../include/init.php';
if(file_exists($sFile))
	require_once($sFile);
else
	die("Init file is not found");

class RzradioModule extends BxDolModule
{
	var $iActionId = 0;
    /**
     * Constructor
     */
    function RzradioModule($aModule)
    {
        parent::__construct($aModule);

        //--- Define Membership Actions ---//
        $aActions = $this->_oDb->getMembershipActions();
		if(count($aActions) == 1)
		{
			foreach($aActions as $aAction) {
				$this->iActionId = $aAction['id'];
			}
		}
    }
    function getContent($iId)
    {
        $sPassword = $iId > 0 ? $_COOKIE['memberPassword'] : "";
		$sApp = $GLOBALS['logged']['admin'] ? "admin" : "user";
		
        $aResult = checkAction($iId, $this->iActionId, true);
        if($aResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED)
		{
			$sBase = BX_DOL_URL_MODULES . $this->_aModule['path'];
			$sResult = '<script src="' . $sBase . 'js/rzpopup.js" type="text/javascript"></script>';
			$sResult .= '<div id="rz_app"></div><script type="text/javascript">swfobject.embedSWF("' . $sBase . 'app/user.swf", "rz_app", "' . RzradioInit::$aRzInfo['width'] . '", "' . RzradioInit::$aRzInfo['height'] . '", "10", "' . $sBase . 'app/expressInstall.swf", {app:"' . $sApp . '",url:"' . $sBase . 'XML.php",id:"' . $iId . '",password:"' . $sPassword . '"}, {allowScriptAccess:"always",allowFullScreen:"true",base:"' . $sBase . '",wmode:"opaque"}, {style:"display:block;"});</script>';
		}            
        else
            $sResult = MsgBox($aResult[CHECK_ACTION_MESSAGE]);

        $sResult = DesignBoxContent(_t('_rzradio_box_caption'), $sResult, 11);

        return $sResult;
    }
}
