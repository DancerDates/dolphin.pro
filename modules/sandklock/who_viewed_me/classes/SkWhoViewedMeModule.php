<?php
bx_import('BxDolModule');

class SkWhoViewedMeModule extends BxDolModule
{
	// Constructor
	var $_iVisitorID;

	function __construct($aModule)
	{
		$this->_iVisitorID = (isMember()) ? (int) $_COOKIE['memberID'] : 0;
		parent::__construct($aModule);
	}
	
	function my_str_insert($intostring, $insertstring) {
		$offset = strrpos($intostring, '<div class="clear_both">');
	   	return substr($intostring, 0, $offset) . $insertstring . substr($intostring, $offset);
	}

	function serviceBlockProfilePage($iMemberID = 0)
	{
		if($iMemberID != 0 && $iMemberID != $this->_iVisitorID)
			return '';
			
		$iIndex = 0;
		$sOutputHtml = '';
		$sViewMode = $_GET['mode'];
		$iCountViewer = $this->_oDb->getCountProfileViewers($this->_iVisitorID);
		if($iCountViewer == 0)
			return MsgBox(_t('_sk_who_viewed_me_text_none_viewed_you'));
		$iPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$iPerpage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 6;
		
		bx_import('BxTemplSearchProfile');
		$oSearchProfileTmpl = new BxTemplSearchProfile();
		$sTemplateName = ($sViewMode == 'extended') ? 'search_profiles_ext.html' : 'search_profiles_sim.html';
		$aAllViewers = $this->_oDb->getProfileViewers($this->_iVisitorID, ($iPage-1)*$iPerpage, $iPerpage);
		$aExtendedCss = array( 'ext_css_class' => 'search_filled_block');
		$sDateFormat = getParam('short_date_format_php');
        foreach ($aAllViewers as $aViewer) {
            $aMemberInfo = getProfileInfo($aViewer['ID']);
            if ( $aMemberInfo['Couple']) 
            {
                $aCoupleInfo = getProfileInfo( $aMemberInfo['Couple'] );
                if ( !($iIndex % 2)  ) {
                    $sOutputHtml .= $oSearchProfileTmpl -> PrintSearhResult($aMemberInfo, $aCoupleInfo, null, $sTemplateName);
                } else {
                    // generate filled block ;
                    $sOutputHtml .= $oSearchProfileTmpl -> PrintSearhResult($aMemberInfo, $aCoupleInfo, $aExtendedCss, $sTemplateName);
                }
            } 
            else {
                if ( !($iIndex % 2)  ) {
                    $sOutputHtml .= $oSearchProfileTmpl -> PrintSearhResult($aMemberInfo, '', null, $sTemplateName);
                } else {
                    // generate filled block ;
                    $sOutputHtml .= $oSearchProfileTmpl -> PrintSearhResult($aMemberInfo, null, $aExtendedCss, $sTemplateName);
                }
            }
//             if($sViewMode != 'extended')
//             {
//             	$sViewDate = date($sDateFormat, $aViewer['view_time']);
//             	$sOutputHtml = $this->my_str_insert($sOutputHtml, "
// <div style=\"color: #888;font-size: 9px;margin-top:3px\"><i class=\"sys_icon eye-open\"> </i>{$sViewDate}</div>");
//             }
            $iIndex++;
        }
        $sOutputHtml .= '<div class="clear_both"></div>';
        
        $bProfilePermalink = getParam('enable_modrewrite') == 'on' ? true : false;
		$sLink  = $bProfilePermalink ? BX_DOL_URL_ROOT . getNickName($iMemberID) : BX_DOL_URL_ROOT . 'profile.php?ID=' . $this->_iVisitorID;
		
        $aTopMenu = array(
        	_t('_sk_who_viewed_me_menu_simple') => array(
        		'href' => $sLink . ($bProfilePermalink ? '?mode=simple' : '&mode=simple'),
        		'dynamic' => true,
        		'active' => ( $sViewMode == 'extended' ? false : true)
        	),
        	_t('_sk_who_viewed_me_menu_extended') => array(
        		'href' => $sLink . ($bProfilePermalink ? '?mode=extended': '&mode=extended'),
        		'dynamic' => true,
        		'active' => ( $sViewMode == 'extended' ? true : false)
        	),
        );
        
        $oPaginate = new BxDolPaginate(array(
			'page_url' => $sLink,
			'count' => $iCountViewer,
			'per_page' => $iPerpage,
			'page' => $iPage,
			'per_page_changer' => true,
			'page_reloader' => true,
			'on_change_page' => 'return !loadDynamicBlock({id}, \''.$sLink.'?mode='.$sViewMode.'&page={page}&per_page={per_page}\');',
        	'on_change_per_page' => 'return !loadDynamicBlock({id}, \''.$sLink.'?mode='.$sViewMode.'&page=1&per_page=\' + this.value);',
		));

		$sPaginate = '<div class="clear_both"></div>'.$oPaginate->getPaginate();
		
        return array($sOutputHtml, $aTopMenu, $sPaginate);
	}
}