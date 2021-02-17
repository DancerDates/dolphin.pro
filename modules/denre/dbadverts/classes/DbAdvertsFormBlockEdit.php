<?php
db_adverts_import ('FormBlockAdd');

class DbAdvertsFormBlockEdit extends DbAdvertsFormBlockAdd {

    function DbAdvertsFormBlockEdit ($oMain, $iEntryId=0, $aDataEntry) {
        parent::DbAdvertsFormBlockAdd ($oMain, $iEntryId, $aDataEntry);

        $aFormInputsId = array (
            'id' => array (
                'type' => 'hidden',
                'name' => 'banner_id',
                'value' => $iEntryId,
            ),
        );

        $this->aInputs = array_merge($this->aInputs, $aFormInputsId);
    }

}

?>