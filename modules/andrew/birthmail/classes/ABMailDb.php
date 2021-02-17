<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by AndrewP. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
* This product cannot be redistributed for free or a fee without written permission from AndrewP.
* This notice may not be removed from the source code.
*
***************************************************************************/
bx_import('BxDolModuleDb');

class ABMailDb extends BxDolModuleDb {
    var $_oConfig;

    /**
     * Constructor
     */
    function ABMailDb(&$oConfig) {
        parent::__construct($oConfig); 
        $this->_oConfig = $oConfig;
    }

    function getMembers() {
        return $this->getAll("SELECT * FROM `Profiles` WHERE MONTH(`DateOfBirth`) = MONTH(CURDATE()) AND DAY(`DateOfBirth`) = DAY(CURDATE())");
    }
}
