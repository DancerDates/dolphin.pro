<?php
/**
 * @version 1.0
 * @copyright Copyright (C) 2014 rayzzz.com. All rights reserved.
 * @license GNU/GPL2, see LICENSE.txt
 * @website http://rayzzz.com
 * @twitter @rayzzzcom
 * @email rayzexpert@gmail.com
 */
require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

global $sIncPath;
require_once($sIncPath . "db.inc.php");

$sFile = dirname(__FILE__) . '/../include/init.php';
if(file_exists($sFile))
	require_once($sFile);
else
	die("Init file is not found");

class RzRadioInstaller extends BxDolInstaller
{
    function RzRadioInstaller($aConfig)
    {
        parent::__construct($aConfig);
		$this->_aActions['execute_sql_queries'] = array(
			'title' => 'Executing SQL queries'
		);
    }
	
	function actionExecuteSqlQueries($bInstall = true) {
		$bResult = true;
		if($bInstall)
		{
			foreach(RzradioInit::$aDBTables as $sName => $aTable)
			{
				getResult("DROP TABLE IF EXISTS `" . $sName . "`;");
				$sql_main = "CREATE TABLE IF NOT EXISTS `" . $sName . "` (";
				foreach($aTable['fields'] as $sField => $aField)
				{
					$sql_main .= "`" . $sField . "` " . $aField['type'] . (isset($aField['length']) ? "(" . $aField['length'] . ") " : " ") . ($aField['not null'] ? "NOT NULL " : " ") . (isset($aField['auto_increment']) && $aField['auto_increment'] ? "auto_increment " : " ");
					if(isset($aField['default']))
					{
						if(is_int($aField['default']))
							$sql_main .= "default " . $aField['default'];
						else
							$sql_main .= "default '" . $aField['default'] . "'";
					}
					$sql_main .= ",";
				}
				$sql_main .= "PRIMARY KEY (`" . implode("`,`", $aTable['primary key']) . "`)) ENGINE=MyISAM ROW_FORMAT=DEFAULT";
				$rRes = getResult($sql_main);
				$bResult = $bResult && $rRes != null;
			}
			for($i=0; $i<count(RzradioInit::$aDBInserts); $i++)
			{
				$rRes = getResult("INSERT INTO `" . RzradioInit::$aDBInserts[$i]['table'] . "`(`" . implode("`,`", RzradioInit::$aDBInserts[$i]['columns']) . "`) VALUES('" . implode("','", RzradioInit::$aDBInserts[$i]['values']) . "');");
				$bResult = $bResult && $rRes != null;
			}
			
			$iOrder = getValue("SELECT MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`='0'");
			$rRes = getResult("INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
			(0, '" . RzradioInit::$aRzInfo['title'] . "', '_rzradio_top_menu_item', 'modules/?r=rzradio/home/|modules/?r=rzradio/', " . ($iOrder+1) . ", 'non,memb', '', '', '', 1, 1, 1, 'top', 'comments-alt', 0, '')");
			$bResult = $bResult && $rRes != null;

			$rRes = getResult("INSERT INTO `sys_acl_actions` SET `Name`='use rzradio'");
			$bResult = $bResult && $rRes != null;
			$iAction = (int)getLastInsertId();
			$rRes = getResult("INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (1, " . $iAction . "), (2, " . $iAction . "), (3, " . $iAction . ")");
			$bResult = $bResult && $rRes != null;
			$rRes = getResult("INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES('modules/?r=rzradio/', 'm/rzradio/', 'permalinks_module_rzradio')");
			$bResult = $bResult && $rRes != null;
			$rRes = getResult("INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES('permalinks_module_rzradio', 'on', 26, 'Enable friendly rzradio permalink', 'checkbox', '', '', 0)");
			$bResult = $bResult && $rRes != null;
		}
		else
		{
			foreach(RzradioInit::$aDBTables as $sName => $aTable)
				getResult("DROP TABLE IF EXISTS `" . $sName . "`;");
				
			getResult("DELETE FROM `sys_menu_top` WHERE `Name`='" . RzradioInit::$aRzInfo['title'] . "'");
			getResult("DELETE FROM `sys_acl_actions` WHERE `Name`='use rzradio' LIMIT 1");
			getResult("DELETE FROM `sys_permalinks` WHERE `check`='permalinks_module_rzradio'");
			getResult("DELETE FROM `sys_options` WHERE `Name`='permalinks_module_rzradio'");
		}	
		
        return $bResult ? BX_DOL_INSTALLER_SUCCESS : BX_DOL_INSTALLER_FAILED;
    }
}
