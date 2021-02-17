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

class ABirthBlockDb extends BxDolModuleDb {	
    var $_oConfig;

    /**
     * Constructor
     */
    function ABirthBlockDb(&$oConfig) {
        parent::__construct();
        $this->_oConfig = $oConfig;
    }

    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'a_birth_block' LIMIT 1");
    }

}