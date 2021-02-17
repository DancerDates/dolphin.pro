<?php
db_adverts_import ('FormAdvertAdd');

class DbAdvertsFormAdvertEdit extends DbAdvertsFormAdvertAdd {

    function DbAdvertsFormAdvertEdit ($oMain, $iEntryId=0, $aDataEntry) {
        parent::DbAdvertsFormAdvertAdd ($oMain, $iEntryId, $aDataEntry);

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