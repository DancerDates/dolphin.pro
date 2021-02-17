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

class AqbEventRecDb extends BxDolModuleDb {	
	/*
	 * Constructor.
	 */
	
	function __construct(&$oConfig) {
		parent::__construct($oConfig);
		$this -> _oConfig = &$oConfig;
	}
	  	
	function getSettingsCategory () {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'aqb_event_rec' LIMIT 1");
    }	
	
	function getEventRecInfo($iEventId){
		return $this -> getRow("SELECT * FROM `{$this -> _sPrefix}settings` WHERE `entry_id` = '{$iEventId}' LIMIT 1");	
	}
	
	function updateEventsDate($iEventID, &$aData){
		$oChecker = new BxDolFormCheckerHelper();
		
		$sEventStart = $oChecker -> passDateTime($aData['EventStart']);
		$sEventEnd = $oChecker -> passDateTime($aData['EventEnd']);
		
		return $this -> query("UPDATE `bx_events_main` SET `EventStart` = '{$sEventStart}', `EventEnd` = '{$sEventEnd}' WHERE `ID` = '{$iEventID}'");
	}
	
	function updateEventSettings($iEventID, &$aData){
		if (!$iEventID) return false;
			
		$tStart = $aData['start'];
		$tEnd = $aData['end'];
		$iDuraion = stripos($aData['duration'], ':') === false ? 0 : $aData['duration'];
		$iRepeat = (int)$aData['repeat'];
		$sRepeatWeekDays = $aData['repeat_week_days'] ? implode(',', $aData['repeat_week_days']) : '';
		$iWeeksNumber = (int)$aData['every_week_number'];
		$dStart = $aData['date_start'];
		$dEnd = $aData['date_end'];
		$iRangeDate	= (int)$aData['range_date'];
		$iOccurrence = (int)$aData['occurrence'];
		$iUseStandard = (int)$aData['aqb_time_settings']['0'];
		$iDateOnly = isset($aData['only_date']);
		
		if ($iUseStandard){			
			$sEventStart = strtotime("{$dStart} {$tStart}");
			$sEventEnd = strtotime("{$dStart} {$tEnd}");			
			$this -> query("UPDATE `bx_events_main` SET `EventStart` = '{$sEventStart}', `EventEnd` = '{$sEventEnd}' WHERE `ID` = '{$iEventID}'");			
			
		}else $this -> updateEventsDate($iEventID, $aData);
		
		$aInfo = $this -> getEventRecInfo($iEventID);		
		if  (empty($aInfo)) return $this -> query("REPLACE INTO `{$this -> _sPrefix}settings` SET 
													`entry_id` = '{$iEventID}', `start` = '{$tStart}', `end` = '{$tEnd}', `duration` = '{$iDuraion}', `repeat` = '{$iRepeat}', 
													`repeat_week_days` = '{$sRepeatWeekDays}', `every_week_number` = '{$iWeeksNumber}', `date_start` = '{$dStart}', `date_end` = '{$dEnd}', `range_date` = '{$iRangeDate}', 
													`occurrence` = '{$iOccurrence}', `use_standard` = '{$iUseStandard}'");
		
		return $this -> query("UPDATE `{$this -> _sPrefix}settings` SET `start` = '{$tStart}', `end` = '{$tEnd}', `duration` = '{$iDuraion}', `repeat` = '{$iRepeat}', 
												`repeat_week_days` = '{$sRepeatWeekDays}', `every_week_number` = '{$iWeeksNumber}', `date_start` = '{$dStart}', `date_end` = '{$dEnd}', `range_date` = '{$iRangeDate}', 
												`occurrence` = '{$iOccurrence}', `use_standard` = '{$iUseStandard}' WHERE `entry_id` = '{$iEventID}'");						   
		
		
		
	}
			
	function getEventInfo($iID, $sFieldName = ''){
		$aEventInfo = $this -> getRow("SELECT * FROM `bx_events_main` WHERE `ID` = '{$iID}' LIMIT 1");
		
		if ($sFieldName) return $aEventInfo[$sFieldName];
		
		return $aEventInfo;
	}
	
	function deleteEventInfo($iEventID){
		$this -> query("DELETE FROM `{$this->_sPrefix}settings` WHERE `entry_id` = '{$iEventID}'");
	}
	
	private function getNewDateByWeekDays(&$aInfo, $iDate = 0){
		if (!$iDate) $iDate = time();
		
		$aDays = asort(explode(',', $aInfo['repeat_week_days']));
		$iWeekDay = (int)date("N", $iDate); 
		$iNewWeekDay = 0;
	
		foreach($aDays as $iKey => $iVal) if ($iVal > $iWeekDay){ 
			$iNewWeekDay = $iVal;
			break;
		}		
		
		if ($iNewWeekDay > $iWeekDay) return (int)date("j", $iDate + ($iNewWeekDay - $iWeekDay) * 24 * 60 * 60);
		
		return (int)date("j", $iDate);
	}
	
	function getNextDate(&$aInfo){
		$aDays = explode(',', $aInfo['repeat_week_days']);
		sort($aDays);

		$iCurrentDate = time();
		$iEventDate = strtotime($aInfo['date_start']);
		$iStartDate = $iCurrentDate >= $iEventDate ? $iCurrentDate : $iEventDate; 
		$iDateDay = (int)date("N", $iEventDate);
		
		if ((($aDays[0] && in_array($iDateDay, $aDays)) || !$aDays[0]) && $iEventDate > $iCurrentDate) return $iEventDate;
		
		$iOneDayInSeconds = 24 * 60 * 60; // one day in seconds
		
		$iDiff = ($iStartDate - $iEventDate) / $iOneDayInSeconds;
		
		$iRepeatDays = 0;
		if($aInfo['every_week_number']) $iRepeatDays = (int)$aInfo['every_week_number'];
				
		if ($aDays[0]){	
			if ($iRepeatDays){				
					for($j = 1; $j <= 365; $j++) {
						$iNewDate = $iStartDate + $j * $iOneDayInSeconds;						
						if ((($iDiff + $j) % $iRepeatDays) == 0 && in_array((int)date("N", $iNewDate), $aDays)) return $iNewDate;
					}					
			} else{ 
					$iDateDay = (int)date("N", $iStartDate);					
					for($i = $iDateDay; $i <= 7; $i++){ 
						if (in_array($i, $aDays)){ 
							$iWeekDayNumber = $i; 
							break;
						}
					}
										
					if (!$iWeekDayNumber) return $iStartDate + ($aDays[0] + 7 - $iDateDay) * $iOneDayInSeconds;
					else return $iStartDate  + ($i - $iDateDay) * $iOneDayInSeconds;					
			}
		}
			
		if ($iRepeatDays)			
			for($j = 1; $j <= $iRepeatDays; $j++){
				if (($iDiff + $j) % $iRepeatDays == 0) return $iNewDate = $iStartDate + $j * $iOneDayInSeconds;					
			}	
		
		return $iCurrentDate;
	}
	
	private function isRightWeek($iFirstDate, $iLastDate, $iInterval){
		if(!$iInterval) return true;
		
		$iOneDayInSeconds = 24 * 60 * 60;
		$iOneDayInWeekSeconds = 7 * 24 * 60 * 60;
		
		$iFirstDayDate = $iFirstDate - ((int)date("N", $iFirstDate) - 1) * $iOneDayInSeconds;
		$iLastDayDate = $iLastDate - ((int)date("N", $iLastDate) - 1) * $iOneDayInSeconds;		
	
		return (($iFirstDayDate - $iLastDayDate) / $iOneDayInWeekSeconds) % $iInterval == 0;		
	}
	
	function getNextDateFromWeek(&$aInfo){
		$aDays = explode(',', $aInfo['repeat_week_days']);
		sort($aDays);

		if (!$aDays[0]) return false;
		
		$iCurrentDate = time();
		$iEventDate = strtotime($aInfo['date_start']);
		$iStartDate = $iCurrentDate >= $iEventDate ? $iCurrentDate : $iEventDate; 
		
		$iDateDay = (int)date("N", $iEventDate);
					
		$iOneDayInSeconds = 24 * 60 * 60; // one day in seconds
		$iOneDayInWeekSeconds = 7* 24 * 60 * 60;

		if (in_array($iDateDay, $aDays) && $iEventDate > $iCurrentDate) return $iEventDate;
		elseif ($this -> isRightWeek($iEventDate, $iStartDate, $aInfo['every_week_number'])){			
			$iDateDay = (int)date("N", $iStartDate);	
			for($i = $iDateDay; $i <= 7; $i++)
				if (in_array($i, $aDays)) return $iStartDate + ($i - $iDateDay) * $iOneDayInSeconds;			
		}			
		
		$iRepeatWeeks = 0;
		if($aInfo['every_week_number']) $iRepeatWeeks = (int)$aInfo['every_week_number'];
		
		if ($iRepeatWeeks){
			for($j = 1; $j <= $iRepeatWeeks; $j++) {
					$iNewDate = $iCurrentDate + $j * $iOneDayInWeekSeconds;
					
					if ($this -> isRightWeek($iEventDate, $iNewDate, $iRepeatWeeks)){
						$iNewEventDate = $iNewDate - ((int)date("N", $iNewDate) - (int)$aDays[0]) * $iOneDayInSeconds;
						if ($iNewEventDate >= $iCurrentDate) return  $iNewEventDate;
					}	
			}
		}	
		else{
			$iDateDay = (int)date("N", $iCurrentDate);					
			return $iCurrentDate + ($aDays[0] + 7 - $iDateDay) * $iOneDayInSeconds;				
		}	
		
		
		return $iEventDate;
	}
	
	private function isRightMonth($iFirstDate, $iLastDate, $iInterval){
		if(!$iInterval) return true;

		$iFirstM = (int)date("n", $iFirstDate);
		$iLastM = (int)date("n", $iLastDate);
		$iMonthsBetween = (date("Y", $iLastDate) - date("Y", $iFirstDate)) * 12;	
	
		$iMonthsBetween + $iLastM - $iFirstM ;
		return ($iMonthsBetween + $iLastM - $iFirstM) % $iInterval == 0;		
	}
	
	function getNextDateFromTheMonth(&$aInfo){		
		$iCurrentDate = time();		
		$iEventDate = strtotime($aInfo['date_start']);
		$iStartDate = $iCurrentDate >= $iEventDate ? $iCurrentDate : $iEventDate; 		
		$iDateDay = (int)date("N", $iEventDate);
		$iDay = (int)date("d", $iEventDate);
					
		$iOneDayInSeconds = 24 * 60 * 60; // one day in seconds

		$aDays = explode(',', $aInfo['repeat_week_days']);
		if ($aDays[0]) sort($aDays);

		if ((!$aDays[0] || in_array($iDateDay, $aDays)) && $iEventDate >= $iCurrentDate) return $iEventDate;
		elseif ($aDays[0] && $this -> isRightMonth($iEventDate, $iStartDate, $aInfo['every_week_number'])){ //&& !in_array($iDateDay, $aDays)
			$iRestDays = (int)date("t", $iStartDate) - (int)date("d", $iStartDate);			
			for($i = 0; $i <= $iRestDays; $i++){ 
				$iNewDate = $iStartDate + $i * $iOneDayInSeconds;				
				if (in_array(date("N", $iNewDate), $aDays))	return $iNewDate;
			}
		}
			
		$iRepeatMonths = 0;
		if($aInfo['every_week_number']) $iRepeatMonths = (int)$aInfo['every_week_number'];
	
		$iMonth = (int)date("n", $iCurrentDate);
		$iYear = (int)date("Y", $iCurrentDate);			

		if ($iRepeatMonths){
			for($j = 1; $j <= $iRepeatMonths; $j++) {
					$iMonth++;					
					if ($iMonth > 12){ 
						$iYear++;
						$iMonth = 1;
					}						
					$iNewDate = mktime(0, 0, 0, $iMonth, 1, $iYear); 					
					
					if ($this -> isRightMonth($iEventDate, $iNewDate, $iRepeatMonths)){						
						if ($aDays[0]){ 
							for($i = 1; $i <= 7; $i++){ 
								$iNewDate = mktime(0, 0, 0, $iMonth, $i, $iYear);				
								if (in_array(date("N", $iNewDate), $aDays))	return $iNewDate;
							}
						}else return mktime(0, 0, 0, $iMonth, $iDay, $iYear);
					}	
			}
		}	
		else{			
			$iDateDay = (int)date("N", $iCurrentDate);
			if ($aDays[0] && !in_array($iDateDay, $aDays)){
				for($i = 1; $i <= 7; $i++){ 
					$iNewDate = $iCurrentDate + $i * $iOneDayInSeconds;				
					if (in_array(date("N", $iNewDate), $aDays))	return $iNewDate;
				}
			}elseif(!$aDays[0]){
				$iMonth++;	
				
				if ($iMonth > 12){ 
					$iYear++;
					$iMonth = 1;
				}			

				return mktime(0, 0, 0, $iMonth, $iDay, $iYear);
			}
		}	
		
		return mktime(0, 0, 0, $iMonth, $iDay, $iYear);
	}
	
	function getNearesEventDate($iEventID){
		$aInfo = $this -> getEventRecInfo($iEventID);
		$aEventInfo = $this -> getEventInfo($iEventID);
		
		if (empty($aInfo) || !(int)$aInfo['use_standard']) return array();
		
		switch($aInfo['repeat']){
			case '1': 
					 return $this -> getNextDate($aInfo);
			case '2': 					 
					 return $this -> getNextDateFromWeek($aInfo);					 	
			case '3': 			           
					 return $this -> getNextDateFromTheMonth($aInfo);
		}
		
		return false;
	}

	function updateEventsDates(){
		$aEvents = $this -> getAll("SELECT * FROM `{$this -> _sPrefix}settings` LEFT JOIN `bx_events_main` ON `ID` = `entry_id` WHERE `EventEnd` <= UNIX_TIMESTAMP() AND NOT (`range_date` = 3 AND `date_end` < NOW()) AND NOT (`range_date` = 2 AND `occurrence` <= `performed_occurrence`)  AND `date_start` != 0");
		
		foreach($aEvents as $iKey => $aValue){
			$aInfo = $this -> getEventRecInfo($aValue['entry_id']);
			$iDate = $this -> getNearesEventDate($aValue['entry_id']);
			
			if(!is_int($iDate)) continue;
			
			$sDate = date('Y-m-d', $iDate);			
	
			$sEventStart = strtotime("{$sDate} {$aInfo['start']}");
			$sEventEnd = strtotime("{$sDate} {$aInfo['end']}");	
			
			$this -> query("UPDATE `bx_events_main` SET `EventStart` = '{$sEventStart}', `EventEnd` = '{$sEventEnd}' WHERE `ID` = '{$aValue['entry_id']}'");			
			
			if ((int)$aInfo['range_date'] == 2){
				$this -> query("UPDATE `{$this -> _sPrefix}settings` SET `performed_occurrence` = `performed_occurrence` + 1 WHERE `entry_id` = '{$aValue['entry_id']}'");
			}
			
			if ($this -> _oConfig -> updateEventsClean()){
				$this->query ("DELETE FROM `bx_events_participants` WHERE `id_entry` = {$aValue['entry_id']}");
				$this->query ("UPDATE `bx_events_main` SET `FansCount` = '0' WHERE `ID` = {$aValue['entry_id']}");
			}
	
		}
	}	
}
?>