<?php
bx_import('BxDolModuleDb');

class SkSocialInviterDb extends BxDolModuleDb {

	var $_sTablePrefix;
	var $_sTableNetworks;
	var $_sTableUsers;

	function SkSocialInviterDb(&$oConfig) {
		parent::__construct($oConfig);        
		$this->_sTablePrefix = $oConfig->getDbPrefix();
		$this->_sTableNetworks = $this->_sTablePrefix.'networks';
		$this->_sTableUsers = $this->_sTablePrefix.'users';
    }


	function getSettings(){
		return (int) $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock Social Inviter Setting' LIMIT 1");
	}

	function getApiSettings()
    {
        return (int) $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Sandklock SocialAll API Settings' LIMIT 1");
    }

	function getAllNetworks(){
		$sql = " SELECT * FROM `{$this->_sTableNetworks}` ORDER BY `order` ASC";
		return $this->getAll($sql);
	}

	function getEnabledNetworks(){
		$sql = " SELECT * FROM `{$this->_sTableNetworks}` WHERE `status` = 'enabled' ORDER BY `order` ASC";
		return $this->getAll($sql);
	}

	function getProfileByEmail($sEmail){
		$sEmail = process_db_input($sEmail);
		$sql = " SELECT * FROM `Profiles` where `Email` = '{$sEmail}' ";
		$result = $this->getRow($sql);

		return !empty($result) ? $result : false;
	}

	function getProfileById($iId){
		$iId = process_db_input($iId);
		$sql = " SELECT * FROM `Profiles` where `ID` = '{$iId}' ";
		$result = $this->getRow($sql);

		return !empty($result) ? $result : false;
	}

	function createNetworkFriend($jFriendList,$sNetwork,$iId){
		$aFriendList = json_decode($jFriendList,true);
		$iLength = count($aFriendList);
		$iCount = 0;
		$sValue = '';
		$date = date('Y-m-d', time());
		foreach($aFriendList as $friend_id){
			$iCount++;
			if($iCount < $iLength)
				$sValue .= "('{$sNetwork}','{$friend_id}','{$iId}','{$date}'),";
			else
				$sValue .= "('{$sNetwork}','{$friend_id}','{$iId}','{$date}')";
		}
		$sql = " INSERT INTO `{$this->_sTableUsers}`(`network`,`friend_identity`,`profile_id`,`date`) VALUES{$sValue}";

		return $this->query($sql);
	}

	function getTotalFriends($iId){
		$sql = " SELECT COUNT(DISTINCT `friend_identity`) FROM `{$this->_sTableUsers}` WHERE `profile_id` = '{$iId}'";
		return (int) $this->getOne($sql);
	}

	function getTotalInvitations($iId){

		$sDateFrom = date('Y-m-d',mktime(0,0,0,date('m'),date('d')));
		$sDateTo = date('Y-m-d',mktime(0,0,0,date('m'),date('d')+1));

		$sql = " SELECT COUNT(*) FROM `{$this->_sTableUsers}` WHERE `profile_id` = '{$iId}' AND `date`>='{$sDateFrom}' AND `date`<'{$sDateTo}'";
		return (int) $this->getOne($sql);

	}

	function updateInvitationLink(){

		$sql = "UPDATE `sys_options` SET `VALUE` = '".BX_DOL_URL_ROOT."' WHERE `Name` = 'sk_social_inviter_invitation_link'";

		return $this->query($sql);
	}

	function updateNetworkOrder($aNetwork){
		$order = 1;
		$sWhenSql = '';
		foreach($aNetwork as $network){
			$network = process_db_input($network);
			$sWhenSql .= " WHEN '{$network}' THEN {$order} ";
			$order++;
		}
		$sql = "UPDATE `{$this->_sTableNetworks}` SET `order` = CASE `name` {$sWhenSql} END";

		return $this->query($sql);
	}
	function updateNetworkStatus($sNetwork,$sStatus){
		$sNetwork = process_db_input($sNetwork);
		$sStatus = process_db_input($sStatus);
		$sql = " UPDATE `{$this->_sTableNetworks}` SET `status` = '{$sStatus}' WHERE `name` = '{$sNetwork}' ";
		return $this->query($sql);
	}
	function getAllSystemUser()
	{
		$sSelectSQL = "
			SELECT `ID`, `NickName`
			FROM `Profiles`
		";
		return $this->getAll($sSelectSQL);
	}
	function getInvitationByUser($iProfileId,$sNetwork){
		$sql = " SELECT * FROM `{$this->_sTableUsers}` ";
		$sWhereId = '';
		$sWhereNet = '';
		if($iProfileId)
			$sWhereId = "`profile_id` = '{$iProfileId}'";
		if($sNetwork)
			$sWhereNet = "`network` = '{$sNetwork}'";
		if(!empty($sWhereId) || !empty($sWhereNet)){
			$sql .= 'WHERE ';
			if(!empty($sWhereId) && !empty($sWhereNet))
				$sql .= $sWhereId.' AND '.$sWhereNet;
			elseif(!empty($sWhereId))
				$sql .= $sWhereId;
			else
				$sql .= $sWhereNet;
		}
		return $this->getAll($sql);
	}
	function getInvitationByDate($dateFrom,$dateTo){
		$sql = " SELECT COUNT(*) as invitations,profile_id,network FROM `{$this->_sTableUsers}` WHERE `date` >= '{$dateFrom}' AND `date`<= '{$dateTo}' GROUP BY `profile_id`,`network`";
		//return $sql;
		return $this->getAll($sql);

	}

	function getFriendRelationship($iVisitorId,$iFriendId,$bFriend){

		$sql = " SELECT * FROM `sys_friend_list`
				WHERE ((`ID`='{$iVisitorId}' AND `Profile`='{$iFriendId}')
				OR (`ID`='{$iFriendId}' AND `Profile`='{$iVisitorId}'))
				AND `Check`='{$bFriend}' ";
		$result = $this->getRow($sql);

		return !empty($result) ? $result : false;
	}

	function getNetworkUserByIdentity($sIdentity){
		$sIdentity = process_db_input($sIdentity);
		$sql = " SELECT * FROM `sk_social_login_users` WHERE `identity` = '{$sIdentity}' ";
		$result = $this->getRow($sql);

		return !empty($result) ? $result : false;
	}

	function getAllProfileByEmail($sEmail){

		$sql = " SELECT * FROM `Profiles` WHERE `Email` IN ('{$sEmail}')";
		$result = $this->getAll($sql);

		//return $sql;
		return !empty($result) ? $result : false;
	}

	function getAllProfileByIdentity($sIdentity){

		$sql = " SELECT `Profiles`.`ID`,`Profiles`.`NickName`,`Profiles`.`Email`
				FROM `sk_social_login_users`
				RIGHT JOIN `Profiles`
				ON `Profiles`.`ID` = `sk_social_login_users`.`profile_id`
				WHERE `sk_social_login_users`.`identity` IN ('{$sIdentity}') ";
		$result = $this->getAll($sql);

		//return $sql;
		return !empty($result) ? $result : false;
	}
}
?>
