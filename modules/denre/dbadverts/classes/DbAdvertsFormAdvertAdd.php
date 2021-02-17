<?php

bx_import ('BxDolFormMedia');

class DbAdvertsFormAdvertAdd extends BxDolFormMedia
{
    var $_oMain, $_oDb;

    function DbAdvertsFormAdvertAdd ($oMain, $iEntryID=0, $aDataEntry=array())
    {
        if($iEntryID > 0)
            $sUrl = 'edit';
        else
            $sUrl = 'add';

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;

        $sAsNew = _t('_adm_bann_Insert_as_new');
        $sErrorC = _t('_Error Occured');
        $sApplyChangesC = _t('_Submit');
        $sTitleC = _t('_Title');
        $sWidthC = _t('_db_adverts_width');
        $sActiveC = _t('_db_adverts_active');
        $sPageC = _t('_db_adverts_page');
        $sCodeC = _t('_db_adverts_code');
        $sFoldC = _t('_db_adverts_fold');
        $sStartDateC = _t('_db_adverts_start_date');
        $sEndDateC = _t('_db_adverts_end_date');

        // get start & end dates
        $start_date_default = date("Y-m-d");
        $end_date_default = "2030-12-31";

        $sActive = $aDataEntry['Active'] == 1 ? 'on' : 'off';

        $start_date = $aDataEntry['campaign_start'] != '' ? $aDataEntry['campaign_start'] : $start_date_default;
        $end_date = $aDataEntry['campaign_end'] != '' ? $aDataEntry['campaign_end'] : $end_date_default;

        // get pages
        $aPages = $this->_oDb->getPages();

        $aPagesSelect = array('all' => _t("_All"));
        foreach($aPages as $key => $value)
            $aPagesSelect[$value['Name']] = $value['Title'];

        // fold
        $aFoldSelect = array('All', 'Above', 'Below');
        $sFoldSelect = $aDataEntry['fold'];

        $aCustomForm = array(
            'form_attrs' => array(
                'name' => 'form_advert',
                'action' => BX_DOL_URL_ROOT . 'm/dbadverts/administration/adverts/'.$sUrl.'/'.$aDataEntry['ID'],
                'method' => 'post',
            ),
            'params' => array (
                'db' => array(
                    'table' => 'db_adverts_advert',
                    'key' => 'ID',
                    'submit_name' => 'add_button',
                ),
            ),
            'inputs' => array(
                'BannerTitle' => array(
                    'type' => 'text',
                    'name' => 'Title',
                    'caption' => $sTitleC,
                    'required' => true,
                    'value' => $aDataEntry['Title'],
                    'info' => _t('_db_adverts_title_note'),
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(2,128),
                        'error' => _t('_chars_to_chars', 2, 128),
                    ),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'BannerWidth' => array(
                    'type' => 'text',
                    'name' => 'Width',
                    'caption' => $sWidthC,
                    'required' => true,
                    'value' => $aDataEntry['Width'],
                    'info' => _t('_db_adverts_width_note'),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'BannerCode' => array(
                    'type' => 'textarea',
                    'name' => 'Code',
                    'caption' => $sCodeC,
                    'required' => true,
                    'value' => $aDataEntry['Code'],
                    'info' => _t('_db_adverts_code_note'),
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(10,32000),
                        'error' => _t('_chars_to_chars', 10, 32000),
                    ),
                    'db' => array (
                        'pass' => 'All',
                    ),
                ),
                'BannerPage' => array(
                    'type' => 'select',
                    'name' => 'Page',
                    'caption' => $sPageC,
                    'value' => $aDataEntry['Page'],
                    'info' => _t('_db_adverts_page_note'),
                    'values' => $aPagesSelect,
                ),
                'BannerFold' => array(
                    'type' => 'select',
                    'name' => 'Fold',
                    'caption' => $sFoldC,
                    'values' => $aFoldSelect,
                    'value' => $aDataEntry['Fold'],
                    'info' => _t('_db_adverts_fold_note'),
                ),
                'BannerActive' => array(
                    'type' => 'checkbox',
                    'name' => 'Active',
                    'caption' => $sActiveC,
                    'checked' => $sActive,
                ),
                'StartDate' => array(
                    'type' => 'date',
                    'name' => 'campaign_start',
                    'caption' => $sStartDateC,
                    'value' => $start_date,
                    'required' => true,
                    'info' => _t('_db_adverts_start_note'),
                    'checker' => array (
                        'func' => 'Date',
                        'error' => $sErrorC,
                    ),
                ),
                'EndDate' => array(
                    'type' => 'date',
                    'name' => 'campaign_end',
                    'caption' => $sEndDateC,
                    'value' => $end_date,
                    'required' => true,
                    'info' => _t('_db_adverts_end_note'),
                    'checker' => array (
                        'func' => 'Date',
                        'error' => $sErrorC,
                    ),
                ),
                'Action' => array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => $action,
                ),
                'add_button' => array(
                    'type' => 'submit',
                    'name' => 'add_button',
                    'value' => $sApplyChangesC,
                ),
            ),
        );

        parent::BxDolFormMedia ($aCustomForm);
    }
}

?>