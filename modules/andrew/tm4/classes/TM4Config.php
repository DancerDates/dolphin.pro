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
bx_import('BxDolConfig');

class TM4Config extends BxDolConfig {

    var $_skin;
    var $_defEditor;

    /**
    * Constructor
    */
    function TM4Config($aModule) {
        parent::__construct($aModule);
        $this->_skin = getParam('tm4_skin');
        $this->_defEditor = getParam('sys_editor_default');
    }
}
