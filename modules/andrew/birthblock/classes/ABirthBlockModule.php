<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by AndrewP. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
* This product cannot be redistributed for free or a fee without written permission from AndrewP.
* This notice may not be removed from the source code.
*
***************************************************************************/
bx_import('BxDolModule');

class ABirthBlockModule extends BxDolModule {

    /**
    * Variables
    */
    var $aPageTmpl;
    var $sModuleUrl;
    var $_iVisitorID;

    /**
    * Constructor
    */
    function ABirthBlockModule($aModule) {
        parent::__construct($aModule);

        $this->aPageTmpl = array(
            'name_index' => 9, 
            'header' => $GLOBALS['site']['title'],
            'header_text' => '',
        );

        $this->sModuleUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
        $this->_iVisitorID = $this->getUserId();
    }

    function actionAdministration() {
        if( !isAdmin() ) {
            header('location: ' . BX_DOL_URL_ROOT);
        }

        $aLanguageKeys = array(
            'settings' => _t('_Settings'),
        );

        // try to define globals category number;
        $iId = $this->_oDb->getSettingsCategory();
        if (!$iId) {
            $sContent = MsgBox(_t('_Empty'));
        } else {
            bx_import('BxDolAdminSettings');

            $mixedResult = '';
            if(isset($_POST['save']) && isset($_POST['cat'])) {
                $oSettings = new BxDolAdminSettings($iId);
                $mixedResult = $oSettings->saveChanges($_POST);
            }

            $oSettings = new BxDolAdminSettings($iId);
            $sResult = $oSettings->getForm();

            if($mixedResult !== true && !empty($mixedResult))
                $sResult = $mixedResult . $sResult;

            $sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $sResult));
            $sContent = $this-> _oTemplate->adminBlock($sContent, $aLanguageKeys['settings']);
        }

        $this->aPageTmpl['header'] = _t('_abb_main');
        $this->aPageTmpl['header_text'] = _t('_abb_main');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sContent), array(), array(), true);
    }

    function serviceIndexBlock() {
        $sBlockName = 'BirthBlock';
        $iLimit = 16;

        //main fields
        $sqlMainFields = "";
        $aDefFields = array('ID', 'NickName', 'Couple', 'Sex');
        foreach ($aDefFields as $iKey => $sValue)
             $sqlMainFields .= "`Profiles`. `{$sValue}`, ";

        $sqlMainFields .= "`DateLastNav`";

        // possible conditions
        $sqlCondition = "WHERE `Profiles`.`Status` = 'Active' and (`Profiles`.`Couple` = 0 or `Profiles`.`Couple` > `Profiles`.`ID`)";
        $sqlConditionDOB = " AND MONTH(`Profiles`.`DateOfBirth`) = MONTH(CURDATE()) AND DAY(`Profiles`.`DateOfBirth`) = DAY(CURDATE())";

        // top menu and sorting
        $aModes = array('all');
        if ($this->_iVisitorID) $aModes[] = 'friends';
        $aDBTopMenu = array();

        $sMode = (in_array($_GET[$sBlockName . 'Mode'], $aModes)) ? $_GET[$sBlockName . 'Mode'] : $sMode = 'all';
        $sqlOrder = "";
        foreach( $aModes as $sMyMode ) {
            switch ($sMyMode) {
                case 'all':
                    if ($sMode == $sMyMode)
                        $sqlOrder = " ORDER BY `Profiles`.`Couple` ASC, `Profiles`.`DateReg` DESC";
                    $sModeTitle = _t('_All');
                break;
                case 'friends':
                    if ($sMode == $sMyMode && $this->_iVisitorID > 0) {
                        $sqlLJoin = 'LEFT JOIN `sys_friend_list` AS f1 ON (f1.`ID` = `Profiles`.`ID` AND f1.`Profile` ='.$this->_iVisitorID.' AND `f1`.`Check` = 1)
                        LEFT JOIN `sys_friend_list` AS f2 ON (f2.`Profile` = `Profiles`.`ID` AND f2.`ID` ='.$this->_iVisitorID.' AND `f2`.`Check` = 1)';
                        $sqlCondition .= " AND (f1.`ID` IS NOT NULL OR f2.`ID` IS NOT NULL)";
                    }
                    $sModeTitle = _t('_Friends');
                break;
            }
            $aDBTopMenu[$sModeTitle] = array('href' => BX_DOL_URL_ROOT . "index.php?{$sBlockName}Mode={$sMyMode}", 'dynamic' => true, 'active' => ( $sMyMode == $sMode ));
        }
        $iCount = (int)db_value("SELECT COUNT(`Profiles`.`ID`) FROM `Profiles` {$sqlLJoin} {$sqlCondition} {$sqlConditionDOB}");

        $aData = array();
        $sPaginate = '';
        if ($iCount) {
            $iNewWidth = BX_AVA_W + 6;
            $iPages = ceil($iCount/ $iLimit);
            $iPage = (int)$_GET['page'];
            if ($iPage < 1)
                $iPage = 1;
            if ($iPage > $iPages)
                $iPage = $iPages;

            $sqlFrom = ($iPage - 1) * $iLimit;
            $sqlLimit = "LIMIT {$sqlFrom}, {$iLimit}";

            $sqlQuery = "SELECT " . $sqlMainFields . " FROM `Profiles` {$sqlLJoin} {$sqlCondition} {$sqlConditionDOB} {$sqlOrder} {$sqlLimit}";

            $rData = db_res($sqlQuery);
            $iCurrCount = mysqli_num_rows ($rData);

            while ($aData = mysqli_fetch_assoc($rData)) {
                $sCode .= get_member_thumbnail($aData['ID'], 'none', true);
            }
            $sCode = '<h3>' . _t('_abb_today') . '</h3>' . $sCode . '<hr />';

            if ($iPages > 1) {
                $oPaginate = new BxDolPaginate(array(
                    'page_url' => BX_DOL_URL_ROOT . 'index.php',
                    'count' => $iCount,
                    'per_page' => $iLimit,
                    'page' => $iPage,
                    'per_page_changer' => true,
                    'page_reloader' => true,
                    'on_change_page' => 'return !loadDynamicBlock({id}, \'index.php?'.$sBlockName.'Mode='.$sMode.'&page={page}&per_page={per_page}\');',
                    'on_change_per_page' => ''
                ));
                $sPaginate = $oPaginate->getSimplePaginate(BX_DOL_URL_ROOT . 'browse.php');
            }
        }

        $sNextFewDays = '';
        $iOtherAmount = (int)getParam("abb_amount");
        for ($i = 1; $i <= $iOtherAmount; $i++) {
            $sNextFewDays .= $this->getNextXDayData($i, $sqlMainFields, $sqlLJoin . ' ' . $sqlCondition);
        }

        $sNextFewDays = ($sNextFewDays == '') ? '' : '<h4>' . _t('_abb_in_few_days').':</h4><div class="clear_both"></div>'.$sNextFewDays;

        if ($sCode == '' && $sNextFewDays == '') return;
        return array($sCode.$sNextFewDays, $aDBTopMenu, $sPaginate);
    }

    function getNextXDayData($iDay, $sqlMainFields, $sSQL) {
        $sqlConditionDOBX = " AND MONTH(DATE_SUB(`Profiles`.`DateOfBirth`, INTERVAL {$iDay} DAY)) = MONTH(CURDATE()) AND DAY(DATE_SUB(`Profiles`.`DateOfBirth`, INTERVAL {$iDay} DAY)) = DAY(CURDATE())";

        $sDXC = _t('_abb_in_d', $iDay);
        $sSubCode = '';
        $iCount = (int)db_value("SELECT COUNT(`Profiles`.`ID`) FROM `Profiles` {$sSQL} {$sqlConditionDOBX}");
        $aData = array();
        if ($iCount) {
            $sqlQuery = "SELECT " . $sqlMainFields . " FROM `Profiles` {$sSQL} {$sqlConditionDOBX} {$sqlOrder} {$sqlLimit}";
            $rData = db_res($sqlQuery);
            while ($aData = mysqli_fetch_assoc($rData)) {
                $sSubCode .= '<div>' . get_member_thumbnail($aData['ID'], 'none', true) . '</div>';
            }
            return $sSubCode;
        }
    }
}
