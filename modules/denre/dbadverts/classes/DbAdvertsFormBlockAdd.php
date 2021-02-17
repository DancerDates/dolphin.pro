<?php

bx_import ('BxDolFormMedia');

class DbAdvertsFormBlockAdd extends BxDolFormMedia
{
    var $_oMain, $_oDb;

    function DbAdvertsFormBlockAdd ($oMain, $iEntryID=0, $aDataEntry=array())
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
        $sCodeC = _t('_Code');
        $sPageC = _t('_db_adverts_page');
        $sPageSelectC = _t('_db_adverts_page_select');
        $sDesignC = _t('_db_adverts_design');

        $sDesignBox = $aDataEntry['DesignBox'] > 0 ? $aDataEntry['DesignBox'] : 1;
        // get pages
        $aPages = $this->_oDb->getPages();

        foreach($aPages as $key => $value)
            $aPagesSelect[$value['Name']] = $value['Title'];

        $aPagesSelector = array(_t("_All"), _t("_db_adverts_page_only"), _t("_db_adverts_page_except"));
        $sPageSelector = substr($aDataEntry['Content'], 69,1);

        $aCustomForm = array(
            'form_attrs' => array(
                'name' => 'form_advert',
                'action' => BX_DOL_URL_ROOT . 'm/dbadverts/administration/blocks/'.$sUrl.'/'.$aDataEntry['ID'],
                'method' => 'post',
            ),
            'params' => array (
                'db' => array(
                    'table' => 'sys_page_compose',
                    'key' => 'ID',
                    'submit_name' => 'add_button',
                ),
            ),
            'inputs' => array(
                'Caption' => array(
                    'type' => 'text',
                    'name' => 'Caption',
                    'caption' => $sTitleC,
                    'required' => true,
                    'value' => $aDataEntry['Caption'],
                    'info' => _t('_db_adverts_caption_note'),
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(2,128),
                        'error' => _t('_chars_to_chars', 2, 128),
                    ),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'BlockPage' => array(
                    'type' => 'select',
                    'name' => 'Page',
                    'caption' => $sPageC,
                    'value' => $aDataEntry['Page'],
                    'values' => $aPagesSelect,
                    'info' => _t('_db_adverts_page_note'),
                    'required' => true,
                ),
                'DesignBox' => array(
                    'type' => 'number',
                    'name' => 'DesignBox',
                    'caption' => $sDesignC,
                    'value' => $sDesignBox,
                    'info' => _t('_db_adverts_design_note'),
                    'required' => true,
                ),
                'PageSelect' => array(
                    'type' => 'select',
                    'name' => 'PageSelect',
                    'caption' => $sPageSelectC,
                    'value' => $sPageSelector,
                    'values' => $aPagesSelector,
                    'info' => _t('_db_adverts_page_select_note'),
                    'required' => true,
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