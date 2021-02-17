<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

bx_import('BxDolPrivacy');

class AqbPFPrivacyPrivacy extends BxDolPrivacy
{
	var $_oModule;

    function __construct(&$oModule)
    {
        parent::__construct($oModule->_oConfig->getDbPrefix() . 'entries', 'field_id', 'user_id');

        $this->_oModule = $oModule;
    }

    function check($sAction, $iObjectId, $iViewerId = 0)
    {
        if(empty($iViewerId))
            $iViewerId = getLoggedId();

        $aObject = $this->_oDb->getObjectInfo($this->getFieldAction($sAction), $iObjectId);
        if(empty($aObject) || !is_array($aObject))
            return true;

		return parent::check($sAction, $iObjectId, $iViewerId);
    }
}
