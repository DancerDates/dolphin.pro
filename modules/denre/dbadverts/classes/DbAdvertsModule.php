<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/
function db_adverts_import ($sClassPostfix, $aModuleOverwright = array())
{
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'adverts')
    {
        $oMain = BxDolModule::getInstance('DbAdvertsModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolTwigModule');

class DbAdvertsModule extends BxDolTwigModule
{
    function DbAdvertsModule(&$aModule)
    {
        parent::__construct($aModule);

        if('advert' == $_GET['ajax'])
        {
            $iType = (int)$_GET['ad_type'];
            $sPage = process_db_input($_GET['ad_page']);

            $iScreen = (int)$_GET['screen'];
            $iPosition = (int)$_GET['block_top'];

            $iBlockLeft = (int)$_GET['block_left'];
            $iBlockRight = (int)$_GET['block_right'];

            $iFL = $iScreen - ($iPosition + $iHeight);
            $iBlockWidth = $iBlockRight - $iBlockLeft;

            if(0 > $iFL) //below the fold
                $TF = 2;
            else //above the fold
                $TF = 1;

            echo $this->_oDb->getAdv($iBlockWidth, $TF, $iType, $sPage);
            exit;
        }
    }

    function serviceGetDbAdvertsBlock($iSelected=0, $sPage='all')
    {
        $iSelected = (int) $iSelected;
        $sPage = process_db_input($sPage);

        $this->_oTemplate->addCss('main.css');

        $aTemplateKeys = array(
            'page' => $sPage,
            'type' => $iSelected,
            'random' => rand(0,99999),
            'ajax_url' => BX_DOL_URL_ROOT . 'm/dbadverts',
        );

        return $this->_oTemplate->parseHtmlByName('frame_ad.html', $aTemplateKeys);
    }

    function actionAdministration($sUrl='view', $sParam1='', $sParam2='', $sParam3='')
    {
        if (!$this->isAdmin())
        {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        if(bx_get("action_add"))
            $sParam1 = 'add';

        if(bx_get("action_edit"))
        {
            $sParam1 = 'edit';
            $sParam2 = (int) bx_get("action_edit");
        }

        if(bx_get("action_copy"))
            $sParam1 = 'copy';

        if(bx_get("action_delete"))
            $sParam1 = 'delete';

        if(bx_get("action_activate"))
            $sParam1 = 'activate';

        if(bx_get("action_deactivate"))
            $sParam1 = 'deactivate';

        if(bx_get("action_test"))
            $sParam1 = 'test';

        $this->_oTemplate->pageStart();

        $aMenu = array(
            'adverts' => array(
                'title' => _t('_db_adverts_menu_admin_adverts'),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/adverts/view',
                '_func' => array ('name' => 'actionAdministrationAdverts', 'params' => array($sParam1, $sParam2,$sParam3)),
            ),
            'blocks' => array(
                'title' => _t('_db_adverts_menu_admin_ad_blocks'),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/blocks/view',
                '_func' => array ('name' => 'actionAdministrationBlocks', 'params' => array($sParam1, $sParam2,$sParam3)),
            ),
            'networks' => array(
                'title' => _t('_db_adverts_menu_admin_networks'),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/networks',
                '_func' => array ('name' => 'actionAdministrationNetworks', 'params' => array($sParam1)),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'networks';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_db_adverts_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('entries.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css');
        $this->_oTemplate->addCssAdmin ('forms_adv.css');
        $this->_oTemplate->pageCodeAdmin (_t('_db_adverts_page_title_administration'));

    }

    function actionAdministrationSettings()
    {
        return parent::_actionAdministrationSettings ('Adverts');
    }

    function actionAdministrationNetworks($sParam1)
    {
        if($sParam1 == '')
            $sParam1 = 'general';

        if("adult" == $sParam1)
        {
            $adult = 'Adult Networks';
            $general = '<a href="'.BX_DOL_URL_MODULES . '?r=dbadverts/administration/networks/general">General Networks</a>';
        } else
        {
            $adult = '<a href="'.BX_DOL_URL_MODULES . '?r=dbadverts/administration/networks/adult">Adult Networks</a>';
            $general = 'General Networks';
        }

        $aVars = array(
            'adult_networks' => $adult,
            'general_networks' => $general,
        );

        require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

        $jCall = 'http://denre.com/networks/'.$sParam1;
        $contents = file_get_contents($jCall);

        $oParser = new Services_JSON();
        $oBanners = $oParser->decode($contents, TRUE);

        $aBanners = unserialize($oBanners);

        if($aBanners)
        {
            foreach($aBanners as $aNetwork => $aInfo)
            {
                $aVars['bx_repeat:networks_list'][] = array (
                    'title' => $aInfo['Title'],
                    'text' => $aInfo['Text'],
                    'url' => $aInfo['Url'],
                    'banner' => $aInfo['Banner'],
                );
            }
        }

        return $this->_oTemplate->parseHtmlByName('networks.html', $aVars);
    }

    function actionAdministrationAdverts($sParam1='',$sParam2='', $sParam3='')
    {
        if (!$this->isAdmin())
        {
                $this->_oTemplate->displayAccessDenied ();
                return;
        }

        $sAction = $sParam1;
        $aDataEntry = array($sParam2, $sParam3);

        switch($sParam1)
        {
            case "add":
                break;
            case "edit":
                $aDataEntry = array($this->_oDb->getAdvert($sParam2));
                if(!$aDataEntry)
                    return;
                break;
            case "delete":
            case "copy":
            case "activate":
            case "deactivate":
                $sAction = 'object';
                break;
            default:
                $sAction = 'view';
                break;
        }

        ob_start();

        call_user_func_array (array($this, '_'.$sAction.'AdvertForm'), $aDataEntry);

        $aVars = array (
                'content' => ob_get_clean(),
        );

        return $aBlock.$this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }

    function actionAdministrationBlocks($sParam1='', $sParam2='', $sParam3='')
    {
        if (!$this->isAdmin())
        {
                $this->_oTemplate->displayAccessDenied ();
                return;
        }

        $sAction = $sParam1;
        $aDataEntry = array($sParam2,$sParam3);

        switch($sParam1)
        {
            case "add":
                break;
            case "edit":
                $aDataEntry = array($this->_oDb->getBlock($sParam2));
                if(!$aDataEntry)
                    return;
                break;
            case "delete":
            case "copy":
                $sAction = 'object';
                break;
            default:
                $sAction = 'view';
                break;
        }

        ob_start();

        call_user_func_array (array($this, '_'.$sAction.'BlockForm'), $aDataEntry);

        $aVars = array (
                'content' => ob_get_clean(),
        );

        return $aBlock.$this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }

    function _addAdvertForm()
    {
        bx_import ('FormAdvertAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdvertAdd';
        $oForm = new $sClass ($this);

        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ())
        {
            $page = process_db_input($_POST['Page']);
            $Active = process_db_input($_POST['Active']) == 'on' ? 1 : 0;
            $campaign_start = process_db_input($_POST['campaign_start']);
            $campaign_end = process_db_input($_POST['campaign_end']);
            $fold = process_db_input($_POST['Fold']);
            $aExtraValues = array(
                'Page' => $page,
                'Active' => $Active,
                'campaign_start' => $campaign_start,
                'campaign_end' => $campaign_end,
                'Fold' => $fold,
            );

            if ($oForm->insert ($aExtraValues))
            {
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/adverts" );
                exit;
            } else
                echo MsgBox(_t('_Error Occured'));
        } else
            echo $oForm->getCode();
    }

    function _editAdvertForm ($aDataEntry)
    {
        bx_import ('FormAdvertEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdvertEdit';
        $oForm = new $sClass ($this, $aDataEntry['ID'], $aDataEntry);

        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ())
        {
            $page = process_db_input($_POST['Page']);
            $Active = process_db_input($_POST['Active']) == 'on' ? 1 : 0;
            $campaign_start = process_db_input($_POST['campaign_start']);
            $campaign_end = process_db_input($_POST['campaign_end']);
            $fold = process_db_input($_POST['Fold']);
            $aExtraValues = array(
                'Page' => $page,
                'Active' => $Active,
                'campaign_start' => $campaign_start,
                'campaign_end' => $campaign_end,
                'Fold' => $fold,
            );

            if ($oForm->update ($aDataEntry['ID'], $aExtraValues))
            {
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/adverts");
                exit;
            } else
                echo MsgBox(_t('_Error Occured'));
        } else
            echo $oForm->getCode ();
    }

    function _viewAdvertForm($iPage=1, $iPerPage=10)
    {
        $iPage = $iPage != '' ? (int) $iPage : 1;
        $iPerPage = $iPerPage != '' ? (int) $iPerPage : 10;

        $iTotal = $this->_oDb->countObjects('ads');
        $aAdverts = $this->_oDb->getAdverts($iPage,$iPerPage);

        $sLink =  BX_DOL_URL_MODULES .'?r=dbadverts/administration/advert/view/';

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sLink . '{page}/{per_page}',
            'count' => $iTotal,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => true,
            'page_reloader' => true,
        ));
        $sPaginate = $oPaginate->getPaginate();

        $i = 0;
        foreach ($aAdverts as $aAdvert)
        {
            $aVars['bx_repeat:message_list'][] = array (
                'rid' => $i,
                'id' => $aAdvert['ID'],
                'title' => $aAdvert['Title'],
                'width' => $aAdvert['Width'],
                'fold' => $aAdvert['Fold'] == 1 ? 'Above' : ($aAdvert['Fold'] == 2 ? 'Below' : 'All'),
                'page' => $aAdvert['Page'],
                'active' => $aAdvert['Active'] == 1 ? 'Yes' : 'No',
                'start_date' => $aAdvert['campaign_start'],
                'end_date' => $aAdvert['campaign_end'],
                'edit' => '<button class="bx-btn bx-btn-small" type="submit" value="'.$aAdvert['ID'].'" name="action_edit">Edit</button>',
            );

            $i++;
            if($i == 2)
                $i=0;
        }

        if (!$aVars['bx_repeat:message_list'])
        {
            $aVars['bx_repeat:message_list'][] = array(
                'title' => 'No Adverts Available',
                'edit' => '<button class="bx-btn bx-btn-small" type="submit" value="'.$aAdvert['ID'].'" name="action_edit">Edit</button>',
            );
        }

        bx_import('Search', $this->_aModule);
        $oSearch = new DbAdvertsSearch();

        // array of buttons
        $aBtnsArray = $this->_oConfig->getActionArray('adverts');

        if (!empty($aBtnsArray)) {
            $aBtns = array();
            foreach ($aBtnsArray as $sKey => $aValue)
                $aBtns[$sKey] = _t($aValue['caption']);
            $sManage = $oSearch->showAdminActionsPanel('db_adverts_admin_form', $aBtns);
        } else {
            $sManage = '';
            $oSearch->bAdminMode = false;
        }

        $this->_oTemplate->addJsAdmin ('jquery.tablesorter.js');
        $this->_oTemplate->addJsAdmin ('jquery.jeditable.mini.js');

        echo $this->_oTemplate->parseHtmlByName('adverts.html', $aVars).$sManage.$sPaginate."</form>";
    }

    function _objectAdvertForm($sParam1='')
    {
        if(is_array($_POST['entry']))
        {
            foreach ($_POST['entry'] as $iValue)
            {
                $iValue = (int)$iValue;
                switch (true) {
                    case isset($_POST['action_copy']):
                        $this->_oDb->copyAdvert($iValue);
                        break;
                    case isset($_POST['action_delete']):
                        $this->_oDb->deleteAdvert($iValue);
                        break;
                    case isset($_POST['action_activate']):
                        $this->_oDb->activateAdvert($iValue);
                        break;
                    case isset($_POST['action_deactivate']):
                        $this->_oDb->deactivateAdvert($iValue);
                        break;
                    case isset($_POST['action_test']):
                        break;
                }
            }
        }

        header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/adverts" );
    }

    function _addBlockForm()
    {
        bx_import ('FormBlockAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormBlockAdd';
        $oForm = new $sClass ($this);

        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ())
        {
            $iPageSelect = process_db_input($_POST['PageSelect']);

            $page = process_db_input($_POST['Page']);
            $design_box = process_db_input($_POST['DesignBox']);

            $aExtraValues = array(
                'Page' => $page,
                'PageWidth' => 1140,
                'Desc' => 'Db Adverts',
                'Column' => 0,
                'Order' => 0,
                'Func' => 'PHP',
                'Content' => 'return BxDolService::call(\"dbadverts\", \"get_db_adverts_block\", array('.$iPageSelect.','.$page.'));',
                'DesignBox' => $design_box,
                'ColWidth' => 28.1,
                'Visible' => 'non,memb',
                'MinWidth' => 0,
            );

            if ($oForm->insert ($aExtraValues))
            {
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/blocks" );
                exit;
            } else
                echo MsgBox(_t('_Error Occured'));
        } else
            echo $oForm->getCode();
    }

    function _editBlockForm ($aDataEntry)
    {
        bx_import ('FormBlockEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormBlockEdit';
        $oForm = new $sClass ($this, $aDataEntry['ID'], $aDataEntry);

        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ())
        {
            $iPageSelect = process_db_input($_POST['PageSelect']);
            $page = process_db_input($_POST['Page']);
            $caption = process_db_input($_POST['Caption']);
            $design_box = process_db_input($_POST['DesignBox']);
            $content = 'return BxDolService::call(\"dbadverts\", \"get_db_adverts_block\", array('.$iPageSelect.','.$page.'));';

            $aExtraValues = array(
                'Page' => $page,
                'DesignBox' => $design_box,
                'Content' => $content,
            );

            if ($oForm->update ($aDataEntry['ID'], $aExtraValues))
            {
                $this->clearCache();

                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/blocks");
                exit;
            } else
                echo MsgBox(_t('_Error Occured'));
        } else
            echo $oForm->getCode ();
    }

    function _viewBlockForm($iPage=1, $iPerPage=10)
    {
        $iPage = $iPage != '' ? (int) $iPage : 1;
        $iPerPage = $iPerPage != '' ? (int) $iPerPage : 10;

        $iTotal = $this->_oDb->countObjects('blocks');
        $aBlocks = $this->_oDb->getBlocks($iPage,$iPerPage);

        $sLink =  BX_DOL_URL_MODULES .'?r=dbadverts/administration/blocks/view/';

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sLink . '{page}/{per_page}',
            'count' => $iTotal,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => true,
            'page_reloader' => true,
        ));
        $sPaginate = $oPaginate->getPaginate();

        $i = 0;
        foreach ($aBlocks as $aBlock)
        {
            $aVars['bx_repeat:message_list'][] = array (
                'rid' => $i,
                'id' => $aBlock['ID'],
                'page' => $aBlock['Page'],
                'caption' => $aBlock['Caption'],
                'column' => $aBlock['Column'],
                'order' => $aBlock['Order'],
                'design_box' => $aBlock['DesignBox'],
                'edit' => '<button class="bx-btn bx-btn-small" type="submit" value="'.$aBlock['ID'].'" name="action_edit">Edit</button>',
            );

            $i++;
            if($i == 2)
                $i=0;
        }

        if (!$aVars['bx_repeat:message_list'])
        {
            $aVars['bx_repeat:message_list'][] = array(
                'caption' => 'No Blocks available',
                'edit' => '<button class="bx-btn bx-btn-small" type="submit" value="'.$aAdvert['ID'].'" name="action_edit">Edit</button>',
            );
        }

        bx_import('Search', $this->_aModule);
        $oSearch = new DbAdvertsSearch();

        // array of buttons
        $aBtnsArray = $this->_oConfig->getActionArray();

        if (!empty($aBtnsArray)) {
            $aBtns = array();
            foreach ($aBtnsArray as $sKey => $aValue)
                $aBtns[$sKey] = _t($aValue['caption']);
            $sManage = $oSearch->showAdminActionsPanel('db_adverts_admin_form', $aBtns);
        } else {
            $sManage = '';
            $oSearch->bAdminMode = false;
        }

        $this->_oTemplate->addJsAdmin ('jquery.tablesorter.js');
        $this->_oTemplate->addJsAdmin ('jquery.jeditable.mini.js');

        echo $this->_oTemplate->parseHtmlByName('blocks.html', $aVars).$sManage.$sPaginate."</form>";
    }

    function _objectBlockForm($sParam1='')
    {
        if(is_array($_POST['entry']))
        {
            foreach ($_POST['entry'] as $iValue)
            {
                $iValue = (int)$iValue;
                switch (true)
                {
                    case isset($_POST['action_copy']):
                        $this->_oDb->copyBlock($iValue);
                        break;
                    case isset($_POST['action_delete']):
                        $this->_oDb->deleteBlock($iValue);
                        break;
                }
            }

            $this->clearCache();
        }

        header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/blocks" );
    }

    function clearCache()
    {
        $aFileNames = glob(BX_DIRECTORY_PATH_CACHE . 'db_sys_page_compose*.php');
        array_map('unlink', $aFileNames);
    }

}

?>