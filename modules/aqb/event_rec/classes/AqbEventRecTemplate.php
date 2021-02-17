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

bx_import('BxTemplFormView');
bx_import('BxDolTwigTemplate');

class AqbEventRecTemplate extends BxDolTwigTemplate {
	
	/**
	 * Constructor
	 */
	
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
		$this -> _oConfig -> init($oDb);
	}		
    
	function parseHtmlByName ($sName, $aVars) {        
        return parent::parseHtmlByName ($sName.'.html', $aVars);
    }	
	
	function genWrapperInput($aInput, $sContent) {
       $oForm = new BxTemplFormView(array());
       
       $sAttr = isset($aInput['attrs_wrapper']) && is_array($aInput['attrs_wrapper']) ? $oForm -> convertArray2Attrs($aInput['attrs_wrapper']) : '';
       switch ($aInput['type']) {
            case 'textarea':
                $sCode = <<<BLAH
                        <div class="input_wrapper input_wrapper_{$aInput['type']}" $sAttr>
                            <div class="input_border">
                                $sContent
                            </div>
                            <div class="input_close_{$aInput['type']} left top"></div>
                            <div class="input_close_{$aInput['type']} left bottom"></div>
                            <div class="input_close_{$aInput['type']} right top"></div>
                            <div class="input_close_{$aInput['type']} right bottom"></div>
                        </div>
BLAH;
            break;
            
            default:
                $sCode = <<<BLAH
                        <div class="input_wrapper input_wrapper_{$aInput['type']}" $sAttr>
                            $sContent
                            <div class="input_close input_close_{$aInput['type']}"></div>
                        </div>
BLAH;
        }
        
        return $sCode;
    }
	
	function getSettingsPanel() {
        $iId = $this -> _oDb -> getSettingsCategory();

        if(empty($iId))
           return MsgBox(_t('_aqb_smenu_nothing_found'));

        bx_import('BxDolAdminSettings');

        $mixedResult = '';

        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings -> saveChanges($_POST);
			$oSettings -> _onSavePermalinks();
        }
        
        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings -> getForm();
                   
			
        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;
        return $sResult;
    }
	
	function getErrosList(){
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_start_time_error'));
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_end_time_error'));
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_rec_pat_empty_error'));	
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_rec_pat_every_weeks_error'));	
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_rec_pat_week_days_error'));
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_range_date_start_error'));
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_range_empty_error'));	
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_range_occurrence_num_error'));
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_range_date_end_error'));
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_end_greater_start_error'));
		$aErrors[]= bx_js_string(_t('_aqb_eventrec_time_area_range_date_end_less_start_error'));		              
		return "'" . implode("','" , $aErrors) . "'";
	}
	
	function getRecurringPanel($iEventId, $aDates){
		$aData = array();		
		
		if ($iEventId && !isset($_POST['submit_form'])){
			$aData = $this -> _oDb -> getEventRecInfo($iEventId); 
			$aDates['EventStart']['value'] = $this -> _oDb -> getEventInfo($iEventId, 'EventStart');
			$aDates['EventEnd']['value'] = $this -> _oDb -> getEventInfo($iEventId, 'EventEnd');
			
			$aDates['EventStart']['value'] = BxDolFormCheckerHelper::displayDateTime($aDates['EventStart']['value']);
			$aDates['EventEnd']['value'] = BxDolFormCheckerHelper::displayDateTime($aDates['EventEnd']['value']);
			
		}else{ 			
			$aData = $_POST;			
			$aData['use_standard'] = $_POST['aqb_time_settings'][0];
			
			if ($_POST['EventStart']) $aDates['EventStart']['value'] = $_POST['EventStart'];
			if ($_POST['EventEnd'])	$aDates['EventEnd']['value'] = $_POST['EventEnd'];
		}

		$oForm = new BxTemplFormView(array());
		$oForm -> _isTbodyOpened = true;
		$oForm -> addCssJs(true);
		
		$sHTML = $this -> parseHtmlByName('recurring_area', array('bx_if:recurring_enabled' => array(
																								'condition' => $this -> _oConfig -> isRecurringOptionEnabled(),
																								'content' => array(
																									'checked_1' => !(int)$aData['use_standard'] ? 'checked="checked"' : '',
																									'checked_2' => (int)$aData['use_standard'] ? 'checked="checked"' : ''
																								)
																							   ),
																  'aqb_errors' => 'var aAqbErrors = [' . $this -> getErrosList() . '];',
																  'date_fields'	=> $oForm -> genRowStandard($aDates['EventStart']) . $oForm -> genRowStandard($aDates['EventEnd']),
																  'style1' => !(int)$aData['use_standard'] ? 'style="display:none;"' : '',
																  'style2' => (int)$aData['use_standard'] ? 'style="display:none;"' : '',
																  'set_time_title' => bx_js_string(_t('_aqb_eventrec_set_time_duration_title')),
																  'weeks' => bx_js_string(_t('_aqb_eventrec_pattern_recur_weeks_on')),
																  'days' => bx_js_string(_t('_aqb_eventrec_pattern_recur_days_on')),
																  'months' => bx_js_string(_t('_aqb_eventrec_pattern_recur_months_on')),
															      'rec_panel' => $this -> parseHtmlByName('rec_panel', 
																														array(
																															'appoint_title' => _t('_aqb_eventrec_appointment_title'),
																															'time_area' =>  $this -> getAppointmentTable($aData),
																															'rec_title' => _t('_aqb_eventrec_rec_title'),
																															'recurrance_pattern_area' => $this -> getRecurrenceTable($aData),
																															'range_title' => _t('_aqb_eventrec_rec_range_title'),
																															'range_of_rec' => $this -> getRangeTable($aData),						
																															))));										
		
		$aFields['recurring_header'] = array(
													'type' => 'block_header',
													'caption' => _t('_aqb_eventrec_recurring_area'),
													'collapsable' => true,
													'collapsed' => false,
												);
		
		$aFields['recurring_area'] = array(
													'type' => 'custom',
													'name' => 'schedule',
													'caption' => _t('_aqb_eventrec_time_area_caption'),
													'content' => $sHTML,	
													'colspan' => false,
												);
		$aFields['recurring_end'] = array(
													'type' => 'block_end',													
												);
		
		return $aFields;
	}
	
	private function getAppointmentTable($aData = array()){
	    $oForm = new BxTemplFormView(array());  	
		
		$aStart = array(
					        'type' => 'text',
					        'name' => 'start',
							'caption' => _t('_aqb_eventrec_start_title'),
							'attrs' => array('id' => 'aqb-start', 'style' => 'width:220px', 'readonly' => 'readonly'),
							'attrs_wrapper' => array('style' => 'width:220px;'),
							'value' => $aData['start'],
							'colspan' => false,
						);		
		$sStart = $oForm -> genRowStandard($aStart);
		
		$aEnd = array(
					        'type' => 'text',
					        'name' => 'end',
							'caption' => _t('_aqb_eventrec_end_title'),
							'attrs' => array('id' => 'aqb-end', 'style' => 'width:220px', 'readonly' => 'readonly'),
							'attrs_wrapper' => array('style' => 'width:220px;'),
							'value' => $aData['end'],
							'colspan' => false,
						);
						
		$sEnd = $oForm -> genRowStandard($aEnd);		
		
		$aDuration = array(
					        'type' => 'text',
					        'name' => 'duration',
							'caption' => _t('_aqb_eventrec_duration_title'),
							'attrs' => array('id' => 'aqb-duration', 'style' => 'width:220px', 'readonly' => 'readonly'),
							'attrs_wrapper' => array('style' => 'width:220px;'),
							'value' => $aData['duration'] ? $aData['duration'] : _t('_aqb_eventrec_duration_unrestricted')
						);
						
		$sDuration = $oForm -> genRowStandard($aDuration);		

		$sTable =<<<EOF
<table class="aqb-event-rec-table form_advanced_table" cellpadding="0" cellspacing="0">
	{$sStart}
	{$sEnd}
	{$sDuration}
</table>
EOF;

		return $sTable;
	}
	
	
	private function getRecurrenceTable($aData = array()){
	    $oForm = new BxTemplFormView(array());
		$oForm -> _isTbodyOpened = true;
	   	
		$aRepeat = array(
					        'type' => 'radio_set',
					        'name' => 'repeat',
							'attrs' => array('id' => 'aqb-repeat'),							
							'values' => array(1 => _t('_aqb_eventrec_pattern_daily'), 2 => _t('_aqb_eventrec_pattern_weekly'), 3 => _t('_aqb_eventrec_pattern_monthly')/*, 4 => _t('_aqb_eventrec_pattern_yearly')*/),
							'value' => $aData['repeat'],
							'dv' => '<br /><br/>',
							'colspan' => true,
						);		
		$sRepeat = $oForm -> genInputRadioSet($aRepeat);
		
		$aWeekDays = array(
					        'type' => 'checkbox_set',
					        'name' => 'repeat_week_days',
							'attrs' => array('id' => 'aqb-week-days'),							
							'values' => array(
												7 => _t('_aqb_eventrec_pattern_day_sunday'),
												1 => _t('_aqb_eventrec_pattern_day_monday'),
												2 => _t('_aqb_eventrec_pattern_day_tuesday'),
												3 => _t('_aqb_eventrec_pattern_day_wednesday'), 
												4 => _t('_aqb_eventrec_pattern_day_thursday'), 
												5 => _t('_aqb_eventrec_pattern_day_friday'),
												6 => _t('_aqb_eventrec_pattern_day_saturday'),
											  ),
							
							'value' => !isset($_POST['repeat_week_days']) ? explode(',', $aData['repeat_week_days']) : $aData['repeat_week_days'],
							'dv' => '&nbsp;&nbsp;',
							'colspan' => true,
						);		
		$sWeekDays = $oForm -> genWrapperInput($aWeekDays, $oForm -> genInputCheckboxSet($aWeekDays));
		
		$aEvery = array(
					        'type' => 'text',
					        'name' => 'every_week_number',							
							'attrs' => array('id' => 'aqb-every-week-number', 'style' => 'width:40px'),
							'attrs_wrapper' => array('style' => 'width:40px;'),
							'value' => $aData['every_week_number'],
							'colspan' => false,
						);
						
		$sEvery = $oForm -> genWrapperInput($aEvery, $oForm -> genInput($aEvery));		

		$sTheSameDate = _t('_aqb_eventrec_pattern_the_same_date');
		$sEveryWeek = _t('_aqb_eventrec_pattern_recur_every');
		
		$sWeeksMonthsTitle =  '';
		switch($aData['repeat']){
			case 1: $sWeeksMonthsTitle = _t('_aqb_eventrec_pattern_recur_days_on'); break;
			case 2: $sWeeksMonthsTitle = _t('_aqb_eventrec_pattern_recur_weeks_on'); break;
			case 3: $sWeeksMonthsTitle = _t('_aqb_eventrec_pattern_recur_months_on'); 
		}
						
		$sTable =<<<EOF
<table class="aqb-event-recurrance-pattern-table form_advanced_table" cellpadding="5" cellspacing="0">
	<tr><td>{$sRepeat}</td>
			<td>
				<div class="aqb-event-recurrance-pattern-days-number">{$sEveryWeek} <div class="aqb-event-recurrance-pattern-weeks">{$sEvery}</div> <span id="aqb-weeks-month">{$sWeeksMonthsTitle}</span>:</div>
				<br/>{$sWeekDays}				
			</td>
	</tr>
</table>
EOF;

		return $sTable;
	}
	
	
	private function getRangeTable($aData = array()){
	    $oForm = new BxTemplFormView(array());
		$oForm -> _isTbodyOpened = true;
		
		$aDateStart = array(
					        'type' => 'datetime',
					        'name' => 'date_start',							
							'attrs' => array('id' => 'aqb-date-start', 'style' => 'width:130px'),
							'attrs_wrapper' => array('style' => 'width:130px;'),							
							'value' => $aData['date_start']
						);		
		$sDateStart = $oForm -> genWrapperInput($aDateStart, $oForm -> genInput($aDateStart));	
		
		$aDateRange = array(
					        'type' => 'radio_set',
					        'name' => 'range_date',
							'attrs' => array('id' => 'aqb-range-date'),							
							'values' => array(1 => _t('_aqb_eventrec_range_no_date_end'), 2 => _t('_aqb_eventrec_range_end_after'), 3 => _t('_aqb_eventrec_range_end_by')),
							'value' => 1,
							'dv' => '<br /><br />',
							'colspan' => true,
							'value' => $aData['range_date']
						);		
		$sDateRange = $oForm -> genWrapperInput($aDateRange, $oForm -> genInputRadioSet($aDateRange));
				
		$aDateEnd = array(
					        'type' => 'datetime',
					        'name' => 'date_end',							
							'attrs' => array('id' => 'aqb-date-end', 'style' => 'width:130px'),
							'attrs_wrapper' => array('style' => 'width:130px;'),
							'colspan' => false,
							'value' => $aData['date_end']
						);
						
		$sDateEnd = $oForm -> genWrapperInput($aDateEnd, $oForm -> genInput($aDateEnd));		

		$aOccurrence = array(
					        'type' => 'text',
					        'name' => 'occurrence',							
							'attrs' => array('id' => 'aqb-occurrence', 'style' => 'width:40px'),
							'attrs_wrapper' => array('style' => 'width:40px;'),
							'value' => $aData['occurrence'],
							'colspan' => false,
						);
						
		$sOccurrence = $oForm -> genWrapperInput($aOccurrence, $oForm -> genInput($aOccurrence));		

		$sStart = _t('_aqb_eventrec_start_date_title');
		$sOccurrenceTitle = _t('_aqb_eventrec_range_occurrence');
		
		$sTable =<<<EOF
<table class="aqb-event-recurrance-range-table form_advanced_table" cellpadding="0" cellspacing="5">
	<tr><td rowspan=3>{$sStart}: <div class="aqb-block-inline">{$sDateStart}</div></td><td rowspan=3>{$sDateRange}</td><td class="aqb-event-recurrance-range-empty-td"></td></tr>
	<tr><td class="aqb-event-recurrance-range-td"><div class="aqb-block-inline">{$sOccurrence}</div> {$sOccurrenceTitle}</td></tr>
	<tr><td class="aqb-event-recurrance-range-td">{$sDateEnd}</td></tr>
</table>
EOF;

		return $sTable;
	}
					
	function getRepeatValue(&$aInfo){
		$aRepeat = array(1 => _t('_aqb_eventrec_pattern_daily'), 2 => _t('_aqb_eventrec_pattern_weekly'), 3 => _t('_aqb_eventrec_pattern_monthly')/*, 4 => _t('_aqb_eventrec_pattern_yearly')*/);
		$aDays = array(
							7 => _t('_aqb_eventrec_pattern_day_sunday'),
							1 => _t('_aqb_eventrec_pattern_day_monday'),
							2 => _t('_aqb_eventrec_pattern_day_tuesday'),
							3 => _t('_aqb_eventrec_pattern_day_wednesday'), 
							4 => _t('_aqb_eventrec_pattern_day_thursday'), 
							5 => _t('_aqb_eventrec_pattern_day_friday'),
							6 => _t('_aqb_eventrec_pattern_day_saturday'),
					    );
								
		$sResult = $aRepeat[$aInfo['repeat']];		
		
		if ($aInfo['every_week_number'])
		switch($aInfo['repeat']){
			case 1: $sResult .= ', ' . _t('_aqb_eventrec_rec_pattern_every_day', $aInfo['every_week_number']); break;
			case 2: $sResult .= ', ' . _t('_aqb_eventrec_rec_pattern_every_week', $aInfo['every_week_number']); break;
			case 3: $sResult .= ', ' . _t('_aqb_eventrec_rec_pattern_every_month', $aInfo['every_week_number']);break;
		}
		
		
		$aWeeks = explode(',' , trim($aInfo['repeat_week_days']));		
		if ($aWeeks[0]){
			foreach($aWeeks as $iKey => $iValue) $sWeeks .= "{$aDays[$iValue]}, ";
			$sResult .= ' ' . _t('_aqb_eventrec_rec_pattern_every_week_on', trim($sWeeks, ', '));
		}	 
		elseif ($aInfo['repeat'] == 3)				
			$sResult .= ' ' . _t('_aqb_eventrec_rec_pattern_every_week_on', _t('_aqb_eventrec_rec_pattern_on_the_same_date'));		
			
		return $sResult;
	}
	
	function getRecurInfoBlock($iEventID){
		$aInfo = $this -> _oDb -> getEventRecInfo($iEventID);	
			
		if ($aInfo['use_standard']){	
		//appointment time
		$aItems[] = array(
							'title' => _t('_aqb_eventrec_app_time_title'),
							'value' => '',
							'bx_if:sub_items' => array(
															'condition' => true,
															'content' => array(
																				'bx_repeat:sub_recur' => array(
																					array('title' => _t('_aqb_eventrec_app_time_start'), 'value' => $aInfo['start']),
																					array('title' => _t('_aqb_eventrec_app_time_end'), 'value' => $aInfo['end']),
																					array('title' => _t('_aqb_eventrec_app_time_duration'), 'value' => !$aInfo['duration'] || $aInfo['duration'] == '00:00' ? str_replace('-', '', _t('_aqb_eventrec_duration_unrestricted')) : $aInfo['duration']),
																				),	
																			 ),
													    ),
								
						 );
		//recurrence pattern
		$aItems[] = array(
							'title' => _t('_aqb_eventrec_rec_pattern_title'),
							'value' => '',
							'bx_if:sub_items' => array(
															'condition' => true,
															'content' => array(
																				'bx_repeat:sub_recur' => array(
																					array('title' => _t('_aqb_eventrec_rec_pattern_repeat'), 'value' => $this -> getRepeatValue($aInfo)),
																				),	
																			 ),
													    ),
								
						 );
		
		//recurrence pattern
		$aItems[] = array(
							'title' => _t('_aqb_eventrec_range_rec_title'),
							'value' => '',
							'bx_if:sub_items' => array(
															'condition' => true,
															'content' => array(
																					'bx_repeat:sub_recur' => array(
																						array('title' => _t('_aqb_eventrec_range_pattern_start'), 'value' => $this -> _oConfig -> formatDate(strtotime($aInfo['date_start']))),
																						array('title' => _t('_aqb_eventrec_range_pattern_end_date'), 'value' => $aInfo['range_date'] == 1 ? _t('_aqb_eventrec_range_pattern_no_end') : ($aInfo['range_date'] == 2 ? _t('_aqb_eventrec_range_pattern_end_after', $aInfo['occurrence']) : $this -> _oConfig -> formatDate(strtotime($aInfo['date_end']))))
																					),	
																			   ),
													   ),							
						 );
		} else{
				$sStartDate = $this -> _oDb -> getEventInfo($iEventID, 'EventStart');
				$sEndDate = $this -> _oDb -> getEventInfo($iEventID, 'EventEnd');
			
				$sStartDate = $this -> _oConfig -> formatDate($sStartDate, BX_DOL_LOCALE_DATE);
				$sEndDate = $this -> _oConfig -> formatDate($sEndDate, BX_DOL_LOCALE_DATE);
			
				$aItems[] = array(
							'title' => _t('_aqb_eventrec_app_time_title'),
							'value' => '',
							'bx_if:sub_items' => array(
															'condition' => true,
															'content' => array(
																				'bx_repeat:sub_recur' => array(
																					array('title' => _t('_aqb_eventrec_app_time_start_date'), 'value' => $sStartDate),
																					array('title' => _t('_aqb_eventrec_app_time_end_date'), 'value' => $sEndDate)
																				),	
																			 ),
													    ),
								
						 );
		}
		
		return $this -> parseHtmlByName('rec_info', array('bx_repeat:recur' => $aItems));
	}	
	
	function adminBlock ($sContent, $sTitle, $aMenu = array()){
        return parent::adminBlock($sContent, $sTitle, $aMenu, '', 11);
    }
}
?>