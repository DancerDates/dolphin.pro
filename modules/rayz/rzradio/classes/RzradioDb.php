<?php
/**
 * @version 1.0
 * @copyright Copyright (C) 2014 rayzzz.com. All rights reserved.
 * @license GNU/GPL2, see LICENSE.txt
 * @website http://rayzzz.com
 * @twitter @rayzzzcom
 * @email rayzexpert@gmail.com
 */
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );

class RzradioDb extends BxDolModuleDb
{
    var $_oConfig;
    /*
     * Constructor.
     */
    function RzradioDb(&$oConfig)
    {
        parent::__construct(); 
        $this->_oConfig = $oConfig;
    }
    function getMembershipActions()
    {
        $sSql = "SELECT `ID` AS `id`, `Name` AS `name` FROM `sys_acl_actions` WHERE `Name`='use rzradio'";
        return $this->getAll($sSql);
    }
}
