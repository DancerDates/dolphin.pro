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

bx_import('BxDolPrivacy');

class BxSchoolsPrivacy extends BxDolPrivacy {

    var $oModule;

	/**
	 * Constructor
	 */
    function __construct(&$oModule) {
        $this->oModule = $oModule;
	    parent::__construct($oModule->_oDb->getPrefix() . 'main', 'id', 'author_id');
    }

	/**
	 * Check whethere viewer is a member of dynamic school.
	 *
	 * @param mixed $mixedSchoolId dynamic school ID.
	 * @param integer $iObjectOwnerId object owner ID.
	 * @param integer $iViewerId viewer ID.
	 * @return boolean result of operation.
	 */
    function isDynamicGroupMember($mixedSchoolId, $iObjectOwnerId, $iViewerId, $iObjectId) {

        $aDataEntry = array ('id' => $iObjectId, 'author_id' => $iObjectOwnerId);
        if ('f' == $mixedSchoolId)  // fans only                       
            return $this->oModule->isFan ($aDataEntry, $iViewerId, true); 
        elseif ('a' == $mixedSchoolId) // admins only
            return $this->oModule->isEntryAdmin ($aDataEntry, $iViewerId); 
	    return false;
	}        
}
