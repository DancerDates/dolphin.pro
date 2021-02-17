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

bx_import('BxTemplVotingView');

class BxSchoolsVoting extends BxTemplVotingView {
    
	/**
	 * Constructor
	 */
	function __construct($sSystem, $iId) {
	    parent::__construct($sSystem, $iId);
    }

    //[begin] - ultimate schools mod from modzzz  
    function makeVote ($iVote) 
    {	
	if (!$this->isEnabled()) return false;
	if ($this->isDublicateVote()) return false;
	if (!$this->checkAction()) return false;
		
        if ($this->_oQuery->putVote ($this->getId(), $_SERVER['REMOTE_ADDR'], $iVote)) 
        {
            $this->_triggerVote();
	        
			$oMain = $this->getMain();  
			$oMain->_oDb->flagActivity('rate', $this->getId(), $_COOKIE['memberID'], array ('rate' => $iVote));		
			
			require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
        	$oZ = new BxDolAlerts($this->_sSystem, 'rate', $this->getId(), $_COOKIE['memberID'], array ('rate' => $iVote));
        	$oZ->alert();
            return true;
        }
        return false;
    }
    //[end] - ultimate schools mod from modzzz 
 

    function getMain() {
        return BxDolModule::getInstance('BxSchoolsModule');
    }

    function checkAction () {
        if (!parent::checkAction())
            return false;
        $oMain = $this->getMain();        
        $aDataEntry = $oMain->_oDb->getEntryById($this->getId ());
        return $oMain->isAllowedRate($aDataEntry);
    }    


}
