<?php
bx_import('BxDolModuleDb');

class SkSocialPostingDb extends BxDolModuleDb
{

    var $_oConfig;
    var $sTablePrefix;

    function __construct(&$oConfig)
    {
        parent::__construct();
        $this->_oConfig = $oConfig;
        $this->sTablePrefix = $this->_oConfig->getDbPrefix();
    }

    function getSettingsCategory()
    {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock Social posting' LIMIT 1");
    }

    function getSupportedNetworks()
    {
        $sQuery = "
			SELECT * 
			FROM `{$this->sTablePrefix}networks` 
			WHERE `status` = 'enabled'
		";
        return $this->getAll($sQuery);
    }

    function getNetworkUserBy($aCondition)
    {

        $sWhere = '';
        $iCount = 0;
        foreach ($aCondition as $sField => $sValue) {
            if ($iCount == 0)
                $sWhere .= "`{$sField}` = '{$sValue}' ";
            else
                $sWhere .= "AND `{$sField}` = '{$sValue}' ";
            $iCount++;
        }

        $sQuery = "
			SELECT * 
			FROM `{$this->sTablePrefix}users` 
			WHERE {$sWhere}
		";
        return $this->getAll($sQuery);
    }

    function updateNetworkUser($aData, $sIdentity)
    {
        if (!is_array($aData))
            return;
        $sUpdate = '';
        $iCount = 0;
        foreach ($aData as $sField => $sValue) {
            if ($iCount == 0)
                $sUpdate .= "`{$sField}` = '{$sValue}' ";
            else
                $sUpdate .= ", `{$sField}` = '{$sValue}' ";
            $iCount++;
        }

        $sQuery = "
			UPDATE `{$this->sTablePrefix}users` 
			SET {$sUpdate} 
			WHERE `identity` = '{$sIdentity}'
		";
        $this->query($sQuery);
        return $this->getAffectedRows();
    }

    function deleteNetworkUserBy($aCondition)
    {
        $sWhere = '';
        $iCount = 0;
        foreach ($aCondition as $sField => $sValue) {
            if ($iCount == 0)
                $sWhere .= "`{$sField}` = '{$sValue}' ";
            else
                $sWhere .= "AND `{$sField}` = '{$sValue}' ";
            $iCount++;
        }

        $sQuery = "
			DELETE 
			FROM `{$this->sTablePrefix}users` 
			WHERE {$sWhere}
		";
        $this->query($sQuery);
        return $this->getAffectedRows();
    }

    function saveSettings($aData)
    {
        $sUpdate = '';
        $iCount = 0;
        foreach ($aData as $sField => $sValue) {
            if ($iCount == 0)
                $sUpdate .= "`{$sField}` = '{$sValue}' ";
            else
                $sUpdate .= ", `{$sField}` = '{$sValue}' ";
            $iCount++;
        }

        $sQuery = "
			UPDATE `{$this->sTablePrefix}users` 
			SET {$sUpdate}
		";
        $this->query($sQuery);
        return $this->getAffectedRows();
    }

    function getUserSettings($iProfileID)
    {
        $sQuery = "
			SELECT `{$this->sTablePrefix}users`.*, `{$this->sTablePrefix}networks`.`logo`,`{$this->sTablePrefix}networks`.`name` 
			FROM `{$this->sTablePrefix}users`
			INNER JOIN `{$this->sTablePrefix}networks` 
			ON `{$this->sTablePrefix}networks`.`name` = `{$this->sTablePrefix}users`.`network` 
			WHERE `{$this->sTablePrefix}users`.`profile_id` = '{$iProfileID}'
		";

        return $this->getAll($sQuery);
    }

    function createNetworkUser($aParam)
    {
        $sField = implode("`,`", array_keys($aParam));
        $sValue = implode("','", array_map("process_db_input", array_values($aParam)));

        $sql = " INSERT INTO `{$this->sTablePrefix}users`(`{$sField}`) values('{$sValue}') ";
        $this->query($sql);

        return $this->getAffectedRows();
    }

    function getActionByModule($sModule)
    {
        $sQuery = "
			SELECT *
			FROM `{$this->sTablePrefix}handlers`
			WHERE `module_uri` = '{$sModule}'
		";
        return $this->getAll($sQuery);
    }

    function getActionUserByModule($sModule)
    {
        $sQuery = "
			SELECT *
			FROM `{$this->sTablePrefix}handlers`
			WHERE `module_uri` = '{$sModule}' 
			AND `enable` = '1'
		";
        return $this->getAll($sQuery);
    }

    function getHandledModules()
    {
        $sQuery = "
			SELECT DISTINCT(`module_uri`)
			FROM `{$this->sTablePrefix}handlers`
		";
        return $this->getColumn($sQuery);
    }

    function getHandledModulesUser()
    {
        $sQuery = "
			SELECT DISTINCT(`module_uri`)
			FROM `{$this->sTablePrefix}handlers`
			WHERE `enable` = '1'
		";
        return $this->getColumn($sQuery);
    }

    function updateActionHandled($aEnableActions, $aDefaultActions)
    {
        if ($aEnableActions)
            $sEnableCondition = "
				`enable` = IF(`id` IN (" . implode(',', $aEnableActions) . "), '1', '0')
			";
        else
            $sEnableCondition = "
				`enable` = '0'
			";
        if ($aDefaultActions)
            $sDefaultCondition = "
				`default_post` = IF(`id` IN (" . implode(',', $aDefaultActions) . "), '1', '0')
			";
        else
            $sDefaultCondition = "
				`default_post` = '0'
			";
        $sQuery = "
			UPDATE `{$this->sTablePrefix}handlers` 
			SET 
				{$sEnableCondition},
				{$sDefaultCondition}
			";

        //echoDbg($sQuery);exit;

        $this->query($sQuery);
    }

    function insertData(&$aData)
    {
        //--- Update Spy Handlers ---//
        foreach ($aData['handlers'] as $aHandler) {
            $aHandler['alert_unit'] = process_db_input($aHandler['alert_unit'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $aHandler['alert_action'] = process_db_input($aHandler['alert_action'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $aHandler['module_uri'] = process_db_input($aHandler['module_uri'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $aHandler['module_method'] = process_db_input($aHandler['module_method'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $sQuery = "
                    INSERT IGNORE INTO
                        `{$this->sTablePrefix}handlers`
                    SET
                        `alert_unit`    = '{$aHandler['alert_unit']}',
                        `alert_action`  = '{$aHandler['alert_action']}',
                        `module_uri`    = '{$aHandler['module_uri']}',
                        `module_method` = '{$aHandler['module_method']}'
                ";
            $this->query($sQuery);
        }
        $sAlertName = $this->_oConfig->getAlertSystemName();
        //--- Update System Alerts ---//
        $sQuery = "SELECT `id` FROM `sys_alerts_handlers` WHERE `name`= '{$sAlertName}' LIMIT 1";
        echoDbgLog($sQuery);
        $iHandlerId = (int)$this->getOne($sQuery);
        foreach ($aData['alerts'] as $aAlert) {
            $aAlert['unit'] = process_db_input($aAlert['unit'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $aAlert['action'] = process_db_input($aAlert['action'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $sQuery = "
                    INSERT IGNORE INTO 
                        `sys_alerts`
                    SET
                       `unit`       = '{$aAlert['unit']}',
                       `action`     = '{$aAlert['action']}',
                       `handler_id` = '{$iHandlerId}'
                ";
            $this->query($sQuery);
        }
    }

    function deleteData(&$aData)
    {
        //--- Update Wall Handlers ---//
        foreach ($aData['handlers'] as $aHandler) {
            $aHandler['alert_unit'] = process_db_input($aHandler['alert_unit'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $aHandler['alert_action'] = process_db_input($aHandler['alert_action'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $aHandler['module_uri'] = process_db_input($aHandler['module_uri'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $aHandler['module_method'] = process_db_input($aHandler['module_method'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $sQuery = "
                    DELETE FROM
                        `{$this->sTablePrefix}handlers`
                    WHERE
                        `alert_unit`    = '{$aHandler['alert_unit']}'
                            AND 
                        `alert_action`  = '{$aHandler['alert_action']}'
                            AND 
                        `module_uri`    = '{$aHandler['module_uri']}'
                            AND 
                        `module_method` = '{$aHandler['module_method']}'
                    LIMIT 1
                ";
            $this->query($sQuery);
        }
        // define system alert name;
        $sAlertName = $this->escape($this->_oConfig->getAlertSystemName());
        //--- Update System Alerts ---//
        $sQuery = "
                SELECT 
                    `id` 
                FROM 
                    `sys_alerts_handlers` 
                WHERE 
                   `name`= '{$sAlertName}'
                LIMIT 1
            ";
        $iHandlerId = (int)$this->getOne($sQuery);
        foreach ($aData['alerts'] as $aAlert) {
            $aAlert['unit'] = process_db_input($aAlert['unit'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $aAlert['action'] = process_db_input($aAlert['action'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
            $sQuery = "
                    DELETE FROM
                        `sys_alerts` 
                    WHERE 
                        `unit`       = '{$aAlert['unit']}' 
                            AND 
                        `action`     = '{$aAlert['action']}'
                            AND 
                        `handler_id` = '{$iHandlerId}'
                    LIMIT 1
                ";
            $this->query($sQuery);
        }
    }

    function getUserConfig($iMemberID)
    {
        $sQuery = "
			SELECT *
			FROM `{$this->sTablePrefix}settings`
			WHERE `profile_id` = '{$iMemberID}'
		";
        $aUserConfig = $this->getRow($sQuery);
        if ($aUserConfig) {
            $aUserConfig['mailru'] = unserialize($aUserConfig['mailru']);
            $aUserConfig['twitter'] = unserialize($aUserConfig['twitter']);
            $aUserConfig['linkedin'] = unserialize($aUserConfig['linkedin']);
            $aUserConfig['lastfm'] = unserialize($aUserConfig['lastfm']);
            $aUserConfig['tumblr'] = unserialize($aUserConfig['tumblr']);
            $aUserConfig['plurk'] = unserialize($aUserConfig['plurk']);
            $aUserConfig['facebook'] = unserialize($aUserConfig['facebook']);
            $aUserConfig['auto_publish'] = unserialize($aUserConfig['auto_publish']);
            $aUserConfig['no_ask'] = unserialize($aUserConfig['no_ask']);
        }

        $sQuery = "
			SELECT `id`, `default_post`
			FROM `{$this->sTablePrefix}handlers`
		";
        $aDefaultConfig = $this->getAll($sQuery);
        $aConfigs = array();
        foreach ($aDefaultConfig as $aConfig) {
            $aConfigs['mailru'][$aConfig['id']] = (!isset($aUserConfig['mailru'][$aConfig['id']]))
                ? $aConfig['default_post']
                : $aUserConfig['mailru'][$aConfig['id']];
            $aConfigs['twitter'][$aConfig['id']] = (!isset($aUserConfig['twitter'][$aConfig['id']]))
                ? $aConfig['default_post']
                : $aUserConfig['twitter'][$aConfig['id']];
            $aConfigs['linkedin'][$aConfig['id']] = (!isset($aUserConfig['linkedin'][$aConfig['id']]))
                ? $aConfig['default_post']
                : $aUserConfig['linkedin'][$aConfig['id']];
            $aConfigs['lastfm'][$aConfig['id']] = (!isset($aUserConfig['lastfm'][$aConfig['id']]))
                ? $aConfig['default_post']
                : $aUserConfig['lastfm'][$aConfig['id']];
            $aConfigs['tumblr'][$aConfig['id']] = (!isset($aUserConfig['tumblr'][$aConfig['id']]))
                ? $aConfig['default_post']
                : $aUserConfig['tumblr'][$aConfig['id']];
            $aConfigs['plurk'][$aConfig['id']] = (!isset($aUserConfig['plurk'][$aConfig['id']]))
                ? $aConfig['default_post']
                : $aUserConfig['plurk'][$aConfig['id']];
            $aConfigs['facebook'][$aConfig['id']] = (!isset($aUserConfig['facebook'][$aConfig['id']]))
                ? $aConfig['default_post']
                : $aUserConfig['facebook'][$aConfig['id']];
            $aConfigs['auto_publish'][$aConfig['id']] = ($aUserConfig['auto_publish'][$aConfig['id']] === NULL)
                ? 0
                : $aUserConfig['auto_publish'][$aConfig['id']];
            $aConfigs['no_ask'][$aConfig['id']] = ($aUserConfig['no_ask'][$aConfig['id']] === NULL)
                ? 0
                : $aUserConfig['no_ask'][$aConfig['id']];
        }
        return $aConfigs;
    }

    function updateActionUserSettings($iMemberID, $aHandlers, $aListActionIDs)
    {
        $iSettingID = $this->getUserSettingID($iMemberID);
        foreach ($aHandlers as $sNetworkName => $aHandledIDs) {
            foreach ($aListActionIDs as $iActionID)
                $aNetworkHandle[$iActionID] = in_array($iActionID, $aHandledIDs) ? 1 : 0;
            $sNetworkHandle[$sNetworkName] = serialize($aNetworkHandle);
        }

        $sSqlValue = '';
        $count = 0;
        foreach ($sNetworkHandle as $sField => $sValue) {

            if ($sField == 'autopublish')
                $sField = 'auto_publish';
            if ($sField == 'noask')
                $sField = 'no_ask';

            if ($count == 0)
                $sSqlValue .= "`{$sField}` = '{$sValue}'";
            else
                $sSqlValue .= ",`{$sField}` = '{$sValue}'";
            $count++;
        }

        if (empty($sSqlValue))
            return;

        if (!$iSettingID) {
            $sQuery = "
				INSERT INTO `{$this->sTablePrefix}settings`
				SET
					`profile_id` = '{$iMemberID}',
					{$sSqlValue}
			";
        } else {
            $sQuery = "
				UPDATE `{$this->sTablePrefix}settings`
				SET
					{$sSqlValue}
				WHERE
					`profile_id` = '{$iMemberID}'
			";
        }

        return $this->query($sQuery);
    }

    function getUserSettingID($iMemberID)
    {
        $sQuery = "
			SELECT `id`
			FROM `{$this->sTablePrefix}settings`
			WHERE `profile_id` = '{$iMemberID}'
		";
        return $this->getOne($sQuery);
    }

    function insertEvent($iSenderID, $aAction)
    {
        $aParameters = isset($aAction['params'])
            ? serialize(process_db_input($aAction['params'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION))
            : '';
        $sActionKey = process_db_input($aAction['lang_key'], BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
        $sLink = process_db_input($aAction['link']);
        $sQuery = "
                INSERT INTO
                    `{$this->sTablePrefix}actions`
                SET
                	`sender_id` 	= '{$iSenderID}',
                    `action_key`    = '{$sActionKey}',
                    `params`        = '{$aParameters}',
                    `link`			= '{$sLink}',
                    `date`          = TIMESTAMP( NOW() )
            ";
        $this->query($sQuery);
        return $this->lastId();
    }

    function getInternalHandlers()
    {
        $sQuery = "SELECT * FROM `{$this->sTablePrefix}handlers`";
        return $this->getAll($sQuery);
    }

    function getHandledByUnitAction($sUnit, $sAction)
    {
        $sQuery = "
				SELECT `id`, `enable`
				FROM `{$this->sTablePrefix}handlers`
				WHERE `alert_unit` = '{$sUnit}'
					AND `alert_action` = '{$sAction}'
		";
        return $this->getRow($sQuery);
    }

    function getAllUserSettings($iMemberID)
    {
        $sQuery = "
			SELECT *
			FROM `{$this->sTablePrefix}settings`
			WHERE `profile_id` = '{$iMemberID}'
		";
        $aUserConfig = $this->getRow($sQuery);
        $aUserConfigs = array(
            'mailru' => array(),
            'tumblr' => array(),
            'lastfm' => array(),
            'plurk' => array(),
            'twitter' => array(),
            'linkedin' => array(),
            'facebook' => array(),
            'auto_publish' => array(),
            'no_ask' => array(),
        );
        if ($aUserConfig) {
            $aUserConfigs['plurk'] = unserialize($aUserConfig['plurk']);
            $aUserConfigs['tumblr'] = unserialize($aUserConfig['tumblr']);
            $aUserConfigs['lastfm'] = unserialize($aUserConfig['lastfm']);
            $aUserConfigs['mailru'] = unserialize($aUserConfig['mailru']);
            $aUserConfigs['twitter'] = unserialize($aUserConfig['twitter']);
            $aUserConfigs['linkedin'] = unserialize($aUserConfig['linkedin']);
            $aUserConfigs['facebook'] = unserialize($aUserConfig['facebook']);
            $aUserConfigs['auto_publish'] = unserialize($aUserConfig['auto_publish']);
            $aUserConfigs['no_ask'] = unserialize($aUserConfig['no_ask']);
        }
        return $aUserConfigs;
    }

    function setAllUserSettings($iMemberID, $aUserConfigs)
    {

        $aConfigs = array(
            'plurk' => serialize($aUserConfigs['plurk']),
            'twitter' => serialize($aUserConfigs['twitter']),
            'linkedin' => serialize($aUserConfigs['linkedin']),
            'mailru' => serialize($aUserConfigs['mailru']),
            'lastfm' => serialize($aUserConfigs['lastfm']),
            'tumblr' => serialize($aUserConfigs['tumblr']),
            'facebook' => serialize($aUserConfigs['facebook']),
            'auto_publish' => serialize($aUserConfigs['auto_publish']),
            'no_ask' => serialize($aUserConfigs['no_ask']),
        );
        $iSettingID = $this->getUserSettingID($iMemberID);
        if (!$iSettingID) {
            $sQuery = "
				INSERT INTO `{$this->sTablePrefix}settings`
				SET
					`profile_id` = '{$iMemberID}',
					`plurk` = '{$aConfigs['plurk']}',
					`twitter` = '{$aConfigs['twitter']}',
					`linkedin` = '{$aConfigs['linkedin']}', 
					`mailru` = '{$aConfigs['mailru']}',
					`lastfm` = '{$aConfigs['lastfm']}',
					`tumblr` = '{$aConfigs['tumblr']}',
					`facebook` = '{$aConfigs['facebook']}',
					`auto_publish` = '{$aConfigs['auto_publish']}',
					`no_ask` = '{$aConfigs['no_ask']}'
			";
        } else {
            $sQuery = "
				UPDATE `{$this->sTablePrefix}settings`
				SET
					`plurk` = '{$aConfigs['plurk']}',
					`twitter` = '{$aConfigs['twitter']}',
					`linkedin` = '{$aConfigs['linkedin']}', 
					`mailru` = '{$aConfigs['mailru']}',
					`lastfm` = '{$aConfigs['lastfm']}',
					`tumblr` = '{$aConfigs['tumblr']}',
					`facebook` = '{$aConfigs['facebook']}',
					`auto_publish` = '{$aConfigs['auto_publish']}',
					`no_ask` = '{$aConfigs['no_ask']}'
				WHERE
					`profile_id` = '{$iMemberID}'
			";
        }

        echoDbgLog($sQuery);

        return $this->query($sQuery);
    }

    function getActionByActionID($iActionID)
    {
        $sQuery = "
			SELECT * FROM `{$this->sTablePrefix}actions` WHERE `id` = '{$iActionID}'
		";
        return $this->getRow($sQuery);
    }

    function deleteProfileData($iProfileId)
    {
        $sQuery = "DELETE FROM `{$this->sTablePrefix}settings` WHERE `profile_id` = {$iProfileId}";
        $this->query($sQuery);

        $sQuery2 = "DELETE FROM `{$this->sTablePrefix}users` WHERE `profile_id` = {$iProfileId}";
        $this->query($sQuery2);
    }
}

?>
