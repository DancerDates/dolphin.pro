<?php
/**
*                            Orca Interactive Forum Script
*                              ---------------
*     Started             : Mon Mar 23 2006
*     Copyright           : (C) 2007 BoonEx School
*     Website             : http://www.boonex.com
* This file is part of Orca - Interactive Forum Script
* GPL
**/


// select menu items and set title header

require_once(BX_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'params.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php');

$aForum = array ();
if (isset($_GET['action']) && 'goto' == $_GET['action'] && $_GET['forum_id']) {    
    $aForum = $GLOBALS['f']->fdb->getForumByUri ($_GET['forum_id']);    
    $GLOBALS['oTopMenu']->setCustomVar('modzzz_schools_view_uri', $aForum['forum_uri']);
    $GLOBALS['oTopMenu']->setCustomSubHeader($aForum['forum_title']);
}
elseif (isset($_GET['action']) && 'goto' == $_GET['action'] && $_GET['topic_id']) {    
    $aTopic = $GLOBALS['f']->fdb->getTopicByUri ($_GET['topic_id']);
    $aForum = $GLOBALS['f']->fdb->getForum ($aTopic['forum_id']);
    $GLOBALS['oTopMenu']->setCustomVar('modzzz_schools_view_uri', $aTopic['forum_uri']);
    $GLOBALS['oTopMenu']->setCustomSubHeader($aTopic['forum_title']);
} else {
    $GLOBALS['oTopMenu']->setCustomVar('modzzz_schools_view_uri', '../');
}

if ((isset($_GET['action']) && 'goto' == $_GET['action'] && $_GET['forum_id']) || (isset($_GET['action']) && 'goto' == $_GET['action'] && $_GET['topic_id'])) { 
    $oModuleMain = BxDolModule::getInstance('BxSchoolsModule'); 
    if ($oModuleMain && $aForum) {
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $oModuleMain->_oConfig->getBaseUri() . 'view/' . $aForum['forum_uri']);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_modzzz_schools') => BX_DOL_URL_ROOT . $oModuleMain->_oConfig->getBaseUri() . 'home/',
            $aForum['forum_title'] => BX_DOL_URL_ROOT . $oModuleMain->_oConfig->getBaseUri() . 'view/' . $aForum['forum_uri'],
            _t('_modzzz_schools_menu_view_forum') => '',
        ));  
	}
}

// use default dolphin design

$aPathInfo = pathinfo(__FILE__);
require_once ($aPathInfo['dirname'] . '/../base/design.php');

// do not show forum index page - always select Schools category at least 

if (!isset($_GET['action']) && !isset($_POST['action'])) {
    $_GET['action'] = 'goto';
    $_GET['cat_id'] = 'Schools';
}

?>
