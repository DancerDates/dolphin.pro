<?php
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolDb.php' );

class SkWhoViewedMeDb extends BxDolModuleDb {
	var $sTableProfileViewTrack;
	var $sTableProfile;
	
	/*
	 * Constructor.
	 */
	function __construct(&$oConfig) {
		parent::__construct();
		$this->sTableProfileViewTrack = 'sys_profile_views_track';
		$this->sTableProfile = 'Profiles';
	}
	
	function getCountProfileViewers($iMemberID)
	{
		$sSelectSQL = "
			SELECT COUNT(DISTINCT(`p`.`ID`))
			FROM `{$this->sTableProfile}` as `p`, `{$this->sTableProfileViewTrack}` as `v`
			WHERE `v`.`id` = '{$iMemberID}'
			AND `v`.`viewer` <> '{$iMemberID}' 
			AND	`v`.`viewer` <> '0'
			AND `v`.`viewer` = `p`.`ID`
		";
		return $this->getOne($sSelectSQL);
	}
	
	function getProfileViewers($iMemberID, $iStart, $iLimit)
	{
		$sSelectSQL = "
			SELECT `p`.`ID`, MAX(`v`.`ts`) as `view_time`
			FROM `{$this->sTableProfile}` as `p`, `{$this->sTableProfileViewTrack}` as `v`
			WHERE `v`.`id` = '{$iMemberID}' 
			AND `v`.`viewer` <> '{$iMemberID}'
			AND	`v`.`viewer` <> '0'
			AND `v`.`viewer` = `p`.`ID`
			GROUP BY `p`.`ID`
			ORDER BY MAX(`v`.`ts`) DESC
			LIMIT {$iStart}, {$iLimit}
		";
		return $this->getAll($sSelectSQL);
	}
}