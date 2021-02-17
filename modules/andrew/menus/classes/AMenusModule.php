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
bx_import('BxBaseMenu');

class AMenusModule extends BxDolModule {

    // Variables
    var $_iVisitorID;
    var $_iMenuID;
    var $_iEn;

    var $sHomeUrl;
    var $sHomePath;

    // Constructor
    function AMenusModule($aModule) {
        parent::__construct($aModule);

        $this->sHomeUrl = $this->_oConfig->getHomeUrl();
        $this->sHomePath = $this->_oConfig->getHomePath();
        $this->_iVisitorID = $this->getUserId();
        $iLastMenuID = $this->_oDb->getCurrentMenu($this->_iVisitorID);
        $this->_iMenuID = ($iLastMenuID && $iLastMenuID > 0) ? $iLastMenuID : (int)getParam('ams_menu_number');

        if ((int)$_GET['menu_id']) {
            $this->_iMenuID = (int)$_GET['menu_id'];
        }

        $this->_iEn = (int)getParam('ams_en');
    }

    function actionBuilder($sSubActionParam = '') {
        global $logged;
        // Check if administrator is logged in.  If not display login form.
        $logged['admin'] = member_auth(1, true, true);

        require_once( $this->_oConfig->getClassPath() . 'AMenusBuilder.php');
        $oAMenusBuilder = new AMenusBuilder($this);
        //return $oAMenusBuilder->getBuilderMainForm();

        $sSubActionRes = '';
        $sSubAction = process_db_input($sSubActionParam, BX_TAGS_STRIP);
        switch($sSubAction) {
            case 'backup':
                bx_import('BxDolDatabaseBackup');
                $oNewBackup = new BxDolDatabaseBackup();
                $oNewBackup->_getTableStruct('sys_menu_top', 1);

                $sFileName = $this->sHomePath . 'data/sys_menu_top_backup.sql';
                $oFile = fopen($sFileName, 'w');
                fputs($oFile, $oNewBackup->sInputs);
                fclose($oFile);

                $sSuccDumpedIntoFileC = _t('_adm_dbtools_succ_dumped_into_file');
                $sSubActionRes = "<font color='green'><center>{$sSuccDumpedIntoFileC} <b>{$sFileName}</b></center></font>";
                break;
            case 'restore':
                bx_import('BxDolDatabaseBackup');
                $oNewBackup = new BxDolDatabaseBackup();

                db_res("TRUNCATE TABLE `sys_menu_top`");

                $sFileName = $this->sHomePath . 'data/sys_menu_top_backup.sql';
                $oNewBackup->_restoreFromDumpFile($sFileName);
                $sDateRestoredFromDumpC = _t('_adm_dbtools_Data_succefully_restored_from_dump');
                $sSubActionRes .= "<font color='green'><center>{$sDateRestoredFromDumpC}</center></font>\n";
                break;
        }

        $this->_oTemplate->addAdminCss(array('builder_main.css'));
        $this->_oTemplate->addAdminJs(array('interface.js', 'builder_main.js'));

        $this->aPageTmpl['name_index'] = 9;
        $aPageVars = array('page_main_code' => DesignBoxAdmin(_t('_adm_mbuilder_title'), $oAMenusBuilder->getBuilderMainForm($sSubActionRes) . $sJS));

        $this->aPageTmpl['js_name'] = array('menu_compose.js', 'BxDolMenu.js');
        $this->aPageTmpl['css_name'] = array('menu_compose.css', 'forms_adv.css');
        $this->aPageTmpl['header'] = _t('_adm_mbuilder_title');
        $this->aPageTmpl['header_text'] = _t('_ams_ml_navigation_menu');
        $this->_oTemplate->pageCode($this->aPageTmpl, $aPageVars, array(), array(), true);
    }

    /*
    * Overload default menu with my version
    */
    function serviceAmsMain() {
        if ($this->_iMenuID > 0 && $this->_iEn) {
            $sClassName = 'ATemplateMenu' . $this->_iMenuID;
            if (file_exists($this->sHomePath . 'classes/' . $sClassName . '.php')) {
                require_once($sClassName . '.php');
                if (class_exists($sClassName)) {
                    $oTopMenu = new $sClassName($this->sHomeUrl, $this);
                    $GLOBALS['oTopMenu'] = $oTopMenu;
                    return $oTopMenu;
                }
            } else {
                return new BxTemplMenu();
            }
        } else {
            return new BxTemplMenu();
        }
    }

    function serviceGetInstalledMenus() {
        $sFilePath = $this->sHomePath . 'classes/';
        if(!is_dir($sFilePath) || !($rSource = opendir($sFilePath))) return false;         

        $aResult = array('0' => _t('_ams_Original_Dolphin_menu'));
        while(($sFile = readdir($rSource)) !== false) {
            if($sFile == '.' || $sFile =='..' || $sFile[0] == '.') continue;
            if (substr($sFile, 0, 13) == 'ATemplateMenu') {
                $iMenuName = (int)substr($sFile, 13, 2);
                $aResult[$iMenuName] = _t('_ams_CSS_menu') . $iMenuName;
            }
        }
        closedir($rSource);
        return $aResult;
    }

    function serviceGetSwitcherBlock($_iProfileID) {
        if ($this->_iVisitorID == $_iProfileID) {
            if ($_REQUEST['ien']) {
                if ($_REQUEST['ien'] == 'disable') setParam('ams_en', 0);
                if ($_REQUEST['ien'] == 'enable') setParam('ams_en', 1);
            }
            if ($_REQUEST['action'] == 'menu_change') {
                $iMenuID = (int)$_REQUEST['menu_id'];
                $sAction = (isset($_REQUEST['apply'])) ? 'Apply' : 'Reset';

                if ($this->_iVisitorID > 0 && $sAction != '') {
                    $this->_oDb->updateMenuSettings($this->_iVisitorID, $iMenuID, $sAction);

                    $sProfileUrl = getProfileLink($this->_iVisitorID);
                    echo '<script type="text/javascript">parent.oAmsSettings.showErrorMsg("menu_switcher_success", "'.$sProfileUrl.'");</script>';
                    exit;
                    //header("Location: {$sProfileUrl}");
                }
            }

            $this->_oTemplate->addCss(array('main.css'));

            $aMenus = $this->serviceGetInstalledMenus();
            $iLastMenuID = $this->_oDb->getCurrentMenu($this->_iVisitorID);

            $sOptions = '';
            foreach ($aMenus as $iID => $sName) {
                $sSelected = ($iLastMenuID && $iLastMenuID == $iID) ? 'selected="selected"' : '';
                $sOptions .= <<<EOF
<option value="{$iID}" {$sSelected}>{$sName}</option>
EOF;
            }

            $aVariables = array (
                'profile_id' => $_iProfileID,
                'modules_url' => BX_DOL_URL_MODULES,
                'menu_options' => $sOptions,
                'success_msgbox' => MsgBox(_t('_adm_txt_settings_success'))
            );
            $sForm = $this->_oTemplate->parseHtmlByName('switcher_block.html', $aVariables);

            return '<div id="ams_settings" style="display:none;">' . DesignBoxContent(_t('_ams_CSS_menu'), $sForm, 1) . '</div>';
        }
    }

    function serviceAmsVhead() {
        // numbers: 3, 11, 14, 16
        $_bVertical = $GLOBALS['oTopMenu']->_bVertical;
        if ($_bVertical) {
            $iPageWidth = (int)getParam('main_div_width');

            $iMenuWidth = $GLOBALS['oTopMenu']->_iMenuWidth;
            $iTotalWidth = $iPageWidth + $iMenuWidth;

            return <<<EOF
        <div class="main" style="width: {$iTotalWidth}px;margin: 0 auto;" id="main_div">
EOF;
        }
    }
    function serviceAmsVfoot() {
        $_bVertical = $GLOBALS['oTopMenu']->_bVertical;
        if ($_bVertical) {
            return <<<EOF
        </div>
        <div class="clear_both"></div>
    </div>
EOF;
        }
    }
}
