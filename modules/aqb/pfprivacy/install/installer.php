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

require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

class AqbPFPrivacyInstaller extends BxDolInstaller {
    function __construct($aConfig) {
        parent::__construct($aConfig);

        $this->_aActions['update_blocks'] = array(
            'title' => '_aqb_pfprivacy_installer_update_blocks',
        );
    }
    
	function actionUpdateBlocks($bInstall = true) {
		$sDescPrefix = 'AQB Profile Fields Block: ';

		$this->_aActions['update_blocks']['title'] = _t($this->_aActions['update_blocks']['title']);

		if($bInstall) {
			$aBlocks = $GLOBALS['MySQL']->getAll("SELECT * FROM `sys_page_compose` WHERE (`Page` IN ('profile', 'profile_info') AND `Func`='PFBlock') OR (`Page`='profile_info' AND `Func` IN ('GeneralInfo', 'AdditionalInfo'))");

			foreach($aBlocks as $aBlock) {
				$aBlockReplace = $aBlock;
				$aBlockReplace['Desc'] = $sDescPrefix . $aBlock['ID'];
				$aBlockReplace['Func'] = "PHP";
				$aBlockReplace['Content'] = "return BxDolService::call(''" . $this->_aConfig['home_uri'] . "'', ''get_block_profile_fields'', array(\$this->oProfileGen->_iProfileID, \$iBlockID, " . $this->_getPFBlockId($aBlock) . "));";
				$aBlockReplace['DesignBox'] = 11;
				$this->_insertBlock($aBlockReplace);

	    		$sSql = "UPDATE `sys_page_compose` SET `Column`='0', `Order`='0' WHERE `ID`='" . $aBlock['ID'] . "' LIMIT 1";
	    		$GLOBALS['MySQL']->query($sSql);
			}
		}
		else {
			$aBlocks = $GLOBALS['MySQL']->getAll("SELECT * FROM `sys_page_compose` WHERE `Page` IN ('profile', 'profile_info') AND `Desc` LIKE '" . $sDescPrefix . "%'");

			foreach($aBlocks as $aBlock) {
				$iId = (int)str_replace($sDescPrefix, '', $aBlock['Desc']);

				$sSql = "UPDATE `sys_page_compose` 
					SET 
						`Column`='" . $aBlock['Column'] . "', 
						`Order`='" . $aBlock['Order'] . "' 
					WHERE 
						`ID`='" . $iId . "'
					LIMIT 1";
				$GLOBALS['MySQL']->query($sSql);

				$sSql = "DELETE FROM `sys_page_compose` WHERE `ID`='" . $aBlock['ID'] . "' LIMIT 1";
	    		$GLOBALS['MySQL']->query($sSql);
			}
		}

    	return BX_DOL_INSTALLER_SUCCESS;
    }

    function _getPFBlockId($aBlock) {
    	$iPFBlockId = 0;

    	switch($aBlock['Func']) {
    		case 'PFBlock':
    			$iPFBlockId =(int)$aBlock['Content'];
    			break;
    		case 'GeneralInfo':
    			$iPFBlockId = 17;
    			break;
    		case 'AdditionalInfo':
    			$iPFBlockId = 20;
    			break;
    	}

    	return $iPFBlockId;
    }

    function _insertBlock($aBlock) {
    	$sSql = "INSERT INTO `sys_page_compose` 
			SET
				`Page`='" . $aBlock['Page'] . "', 
				`PageWidth`='" . $aBlock['PageWidth'] . "',
				`Desc`='" . $aBlock['Desc'] . "', 
				`Caption`='" . $aBlock['Caption'] . "',
				`Column`='" . $aBlock['Column'] . "',
				`Order`='" . $aBlock['Order'] . "',
				`Func`='" . $aBlock['Func'] . "', 
				`Content`='" . $aBlock['Content'] . "',
				`DesignBox`='" . $aBlock['DesignBox'] . "', 
				`ColWidth`='" . $aBlock['ColWidth'] . "', 
				`Visible`='" . $aBlock['Visible'] . "', 
				`MinWidth`='" . $aBlock['MinWidth'] . "'";
		return (int)$GLOBALS['MySQL']->query($sSql) > 0;
    }
}
?>