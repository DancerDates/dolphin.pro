<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx School
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxTemplCmtsView');

class BxSchoolsCmts extends BxTemplCmtsView {
    
	/**
	 * Constructor
	 */
	function __construct($sSystem, $iId) {
	    parent::__construct($sSystem, $iId);
    }

    //[begin] - ultimate schools mod from modzzz  
    function actionCmtPost ()
    {    	
    	if (!$this->isEnabled()) return '';
    	if (!$this->isPostReplyAllowed ()) return '';

        $iCmtParentId = (int)$_REQUEST['CmtParent'];

        $sText =  $this->_prepareTextForSave ($_REQUEST['CmtText']);
    		
    	$iMood = (int)$_REQUEST['CmtMood'];

    	$iCmtNewId = $this->_oQuery->addComment ($this->getId(), $iCmtParentId, $this->_getAuthorId(), $sText, $iMood);

        if(false === $iCmtNewId)
            return '';

        $oMain = $this->getMain();   
		$oMain->_oDb->flagActivity('commentPost', $this->getId(), $this->_getAuthorId(), array('comment_id' => $iCmtNewId));		

    	bx_import('BxDolAlerts');
    	$oZ = new BxDolAlerts($this->_sSystem, 'commentPost', $this->getId(), $this->_getAuthorId(), array('comment_id' => $iCmtNewId));
    	$oZ->alert();
 
        $this->_triggerComment();

    	return $iCmtNewId;
    }
    //[end] - ultimate schools mod from modzzz 



    function getMain() {
        return BxDolModule::getInstance('BxSchoolsModule');
    }

    function isPostReplyAllowed () {
        if (!parent::isPostReplyAllowed())
            return false;
        $oMain = $this->getMain();        
        $aDataEntry = $oMain->_oDb->getEntryById($this->getId ());
        return $oMain->isAllowedComments($aDataEntry);
    }

    function isEditAllowedAll () {
        $oMain = $this->getMain();
        $aDataEntry = $oMain->_oDb->getEntryById($this->getId ());
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit ($aDataEntry))
            return true;
        return parent::isEditAllowedAll ();
    }

    function isRemoveAllowedAll () {
        $oMain = $this->getMain();
        $aDataEntry = $oMain->_oDb->getEntryById($this->getId ());
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit ($aDataEntry))
            return true;
        return parent::isRemoveAllowedAll ();
    }    
}
