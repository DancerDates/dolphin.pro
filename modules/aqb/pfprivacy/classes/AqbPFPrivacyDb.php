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

bx_import('BxDolModuleDb');

class AqbPFPrivacyDb extends BxDolModuleDb {
	var $_oConfig;

	function __construct(&$oConfig) {
		parent::__construct($oConfig);

		$this->_oConfig = &$oConfig;
	}

	function getProfileFieldsBlocks() {
		$sSql = "SELECT `ID` AS `id`, `Name` AS `name` FROM `sys_profile_fields` WHERE `Type`='block' AND (`ViewMembOrder`>0 OR `ViewVisOrder`>0)";
		return $this->getAll($sSql);
	}

	function getProfileFieldsByBlock($iBlockId) {
		$sSql = "SELECT `ID` AS `id`, `Name` AS `name` FROM `sys_profile_fields` WHERE `ViewMembBlock`='" . $iBlockId . "' OR `ViewVisBlock`='" . $iBlockId . "'";
		return $this->getAll($sSql);
	}

	function replaceEntry($iUserId, $iFieldId, $iValue) {
		$sSql = "REPLACE INTO `" . $this->_sPrefix . "entries` SET `user_id`='" . $iUserId . "', `field_id`='" . $iFieldId . "', `allow_view_to`='" . $iValue . "'";
		return (int)$this->query($sSql) > 0;
	}

	function getEntries($aParams, &$aSnippets, $bReturnCount = false) {
		$sDbDateFormat = getLocaleFormat(BX_DOL_LOCALE_DATE_SHORT, BX_DOL_LOCALE_DB);

		$aMethod = array('name' => 'getAll', 'params' => array(0 => 'query'));
		$sSelectClause = $sJoinClause = $sWhereClause = $sOrderClause = $sLimitClause = '';
		switch($aParams['type']) {
			case 'id':
				$aMethod['name'] = 'getRow';
	            $sWhereClause = "AND `te`.`id`='" . $aParams['value'] . "'";
				break;

			case 'all_by_user':
				$aMethod['name'] = 'getAllWithKey';
				$aMethod['params'][1] = 'field_id';
				$sWhereClause = "AND `te`.`user_id`='" . $aParams['value'] . "'";
	            $sOrderClause = "ORDER BY `te`.`field_id`";
	            $sLimitClause = isset($aParams['start']) && isset($aParams['per_page']) ? "LIMIT " . (int)$aParams['start'] . ', ' . (int)$aParams['per_page'] : "";
				break;
		}

		$aMethod['params'][0] = "SELECT " . ($bReturnCount ? "SQL_CALC_FOUND_ROWS" : "") . "
				`te`.`user_id` AS `user_id`,
				`te`.`field_id` AS `field_id`,
				`te`.`allow_view_to` AS `allow_view_to`" . $sSelectClause . "
			FROM `" . $this->_sPrefix . "entries` AS `te` " . $sJoinClause . "
			WHERE 1 " . $sWhereClause . " " . $sOrderClause . " " . $sLimitClause;
		$aSnippets = call_user_func_array(array($this, $aMethod['name']), $aMethod['params']);

		if(!$bReturnCount)
            return !empty($aSnippets);

		return (int)$this->getOne("SELECT FOUND_ROWS()");
	}
}
?>