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

class BxSchoolsCoursesVoting extends BxTemplVotingView {
    
	/**
	 * Constructor
	 */
	function __construct($sSystem, $iId) {
	    parent::__construct($sSystem, $iId); 
    }
 
    function getMain() {
        return BxDolModule::getInstance('BxSchoolsModule');
    }

    function checkAction () {
        if (!parent::checkAction())
            return false;
        $oMain = $this->getMain();        
       
		$aCourseEntry = $oMain->_oDb->getCoursesEntryById($this->getId ());
 
        return $oMain->isAllowedRateSubProfile($oMain->_oDb->_sTableCourses, $aCourseEntry);
    }    
}
