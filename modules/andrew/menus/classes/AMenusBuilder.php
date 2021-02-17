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

define ('BX_SECURITY_EXCEPTIONS', true);
$aBxSecurityExceptions = array(
    'POST.Link',
    'REQUEST.Link',
);

bx_import('BxDolMenu');
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

class AMenusBuilder extends BxDolMenu {

    var $oMenusMain;
    var $sABFolderIcon;
    var $sABUpIcon;
    var $sABDownIcon;

    /*
    * Constructor.
    */
    function AMenusBuilder($oMenusMain) {
        parent::BxDolMenu();
        $this->oMenusMain = $oMenusMain;

        $this->sABFolderIcon = $this->oMenusMain->_oTemplate->getIconUrl('folder.png');
        $this->sABUpIcon = $this->oMenusMain->_oTemplate->getIconUrl('toggle_up.png');
        $this->sABDownIcon = $this->oMenusMain->_oTemplate->getIconUrl('toggle_down.png');
    }

    function compile() { // For 7.0.4 and above (till 7.1.1)

        $sEval =  "return array(\n";
        $aFields = array( 'Type','Caption','Link','Visible','Target','Onclick','Check','Parent','Picture','Icon','BQuickLink', 'Statistics', 'Name' );

        $sQuery = "
            SELECT
                `ID`,
                `" . implode('`,
                `', $aFields ) . "`
            FROM `sys_menu_top`
            WHERE
                `Active` = 1 AND
                ( `Type` = 'system' OR `Type` = 'top' )
            ORDER BY `Type`,`Order`
        ";

        $rMenu = db_res( $sQuery );
        while( $aMenuItem = mysqli_fetch_assoc( $rMenu ) ) {
            $sEval .= "  " . str_pad( $aMenuItem['ID'], 2 ) . " => array(\n";

            foreach( $aFields as $sKey => $sField ) {
                $sCont = $aMenuItem[$sField];

                if( $sField == 'Link' )
                    $sCont = $this->getCurrLink($sCont);

                $sCont = str_replace( '\\', '\\\\', $sCont );
                $sCont = str_replace( '"', '\\"',   $sCont );
                $sCont = str_replace( '$', '\\$',   $sCont );

                $sCont = str_replace( "\n", '',     $sCont );
                $sCont = str_replace( "\r", '',     $sCont );
                $sCont = str_replace( "\t", '',     $sCont );

                $sEval .= "    " . str_pad( "'$sField'", 11 ) . " => \"$sCont\",\n";
            }
            
            $sEval .= "  ),\n";
            
            // write it's children
            $sEval .= $this->compileSubUnitsRecurs($aMenuItem['ID'], $aFields);
        }
        
        $sEval .= ");\n";
        $aResult = eval($sEval);

        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        return $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey('sys_menu_top'), $aResult);
    }

    function compileSubUnitsRecurs($iParentID, $aFields) {
        // write it's children
        $sQuery = "
            SELECT
                `ID`,
                `" . implode('`,
                `', $aFields ) . "`
            FROM `sys_menu_top`
            WHERE
                `Active` = 1 AND
                `Type` = 'custom' AND
                `Parent` = {$iParentID}
            ORDER BY `Order`
        ";
        
        $rCMenu = db_res( $sQuery );
        while( $aMenuItem = mysqli_fetch_assoc( $rCMenu ) ) {
            $sEval .= "  " . str_pad( $aMenuItem['ID'], 2 ) . " => array(\n";
            
            foreach( $aFields as $sKey => $sField ) {
                $sCont = $aMenuItem[$sField];
                
                if( $sField == 'Link' )
                    $sCont = $this->getCurrLink($sCont);
                
                $sCont = str_replace( '\\', '\\\\', $sCont );
                $sCont = str_replace( '"', '\\"',   $sCont );
                $sCont = str_replace( '$', '\\$',   $sCont );
                
                $sCont = str_replace( "\n", '',     $sCont );
                $sCont = str_replace( "\r", '',     $sCont );
                $sCont = str_replace( "\t", '',     $sCont );
                
                $sEval .= "    " . str_pad( "'$sField'", 11 ) . " => \"$sCont\",\n";
            }
            
            $sEval .= "  ),\n";

            $sEval .= $this->compileSubUnitsRecurs($aMenuItem['ID'], $aFields);
        }
        return $sEval;
    }

    function getBuilderMainForm($sSubActionRes = '') {
        $GLOBALS['oAdmTemplate']->addJsTranslation(array(
            '_adm_mbuilder_Sorry_could_not_insert_object',
            '_adm_mbuilder_This_items_are_non_editable'
        ));

        // process actions
        $this->ABProcessActions();

        // active elements
        $sTopElements = '';
        $sTopQuery = "SELECT `ID`, `Name` FROM `sys_menu_top` WHERE `Active`=1 AND `Type`='top' ORDER BY `Order`";
        $rTopItems = db_res( $sTopQuery );
        while ($aTopItem = mysqli_fetch_assoc($rTopItems)) {
            $sTopElements .= $this->getSubUnitsRecurs($aTopItem, 't');
        }

        $sSysQuery = "SELECT `ID`, `Name` FROM `sys_menu_top` WHERE `Active`=1 AND `Type`='system' ORDER BY `Order`";
        $rSysItems = db_res( $sSysQuery );
        while ($aSysItem = mysqli_fetch_assoc($rSysItems)) {
            //echoDbg($aSysItem);
            $sTopElements .= $this->getSubUnitsRecurs($aSysItem, 's');
        }

        // sys elements
        /*
        $sSysQuery = "SELECT `ID`, `Name` FROM `sys_menu_top` WHERE `Active`=1 AND `Type`='system' ORDER BY `Order`";
        $rSysItems = db_res( $sSysQuery );
        while( $aSystemItem = mysqli_fetch_assoc( $rSysItems ) ) {
            $sComposerInit .= "
                
                aSystemItems[{$aSystemItem['ID']}] = '" . addslashes( $aSystemItem['Name'] ) . "';
                aCustomItems[{$aSystemItem['ID']}] = {};";
            $sQuery = "SELECT `ID`, `Name` FROM `sys_menu_top` WHERE `Active`=1 AND `Type`='custom' AND `Parent`={$aSystemItem['ID']} ORDER BY `Order`";
            
            $rCustomItems = db_res( $sQuery );
            while( $aCustomItem = mysqli_fetch_assoc( $rCustomItems ) ) {
                $sComposerInit .= "
                aCustomItems[{$aSystemItem['ID']}][{$aCustomItem['ID']}] = '" . addslashes( $aCustomItem['Name'] ) . "';";
            }
        }*/

        // all items
        $sAllItems = '';
        $sAllQuery = "SELECT `ID`, `Name` FROM `sys_menu_top` WHERE `Type`!='system'";
        $rAllItems = db_res( $sAllQuery );
        while ($aAllItem = mysqli_fetch_assoc($rAllItems)) {
            $iAItemID = (int)$aAllItem['ID'];
            $sAItemName = addslashes($aAllItem['Name']);

            $sAllItems .= <<<EOF
    <li class="treeItem" id="{$iAItemID}" type="inactive">
        <img src="{$this->sABUpIcon}" class="upIcon" /><img src="{$this->sABDownIcon}" class="downIcon" /><span class="textHolder"><img src="{$this->sABFolderIcon}" class="folderImage" /> {$sAItemName}</span>
    </li>
EOF;
        }

        $aVariables = array (
            'sParserUrl' => BX_DOL_URL_ROOT . $this->oMenusMain->_oConfig->getBaseUri() . 'builder/',
            'sSubImgPath' => $this->oMenusMain->_oConfig->_sHomeUrl . 'templates/base/images/icons/',
            'sTopElements' => $sTopElements,
            'sAllItems' => $sAllItems,
            'actions_result' => $sSubActionRes,
        );
        return $this->oMenusMain->_oTemplate->parseHtmlByName('builder_main.html', $aVariables);
    }

    function getSubUnitsRecurs($aCustomItem, $sType = '') {
        $iSubID = (int)$aCustomItem['ID'];
        $sSubName = addslashes($aCustomItem['Name']);

        // level X of subs
        $sSub2Elements = '';
        $sQuery2 = "SELECT `ID`, `Name` FROM `sys_menu_top` WHERE `Active`=1 AND `Type`='custom' AND `Parent`={$iSubID} ORDER BY `Order`";
        $rCustom2Items = db_res($sQuery2);
        while($aCustom2Item = mysqli_fetch_assoc($rCustom2Items)) {
            /*$iSub2ID = (int)$aCustom2Item['ID'];
            $sSub2Name = addslashes($aCustom2Item['Name']);

            $sSub2Elements .= <<<EOF
    <li class="treeItem" id="{$iSub2ID}"><span class="textHolder"><img src="{$this->sABFolderIcon}" class="folderImage" /> {$sSub2Name}</span></li>
    EOF;*/
            $sSub2Elements .= $this->getSubUnitsRecurs($aCustom2Item);
        }

        $sTop2FlyClass =  '';
        if ($sSub2Elements != '') {
            $sTop2FlyClass = ' fly';
            $sSub2Elements = <<<EOF
<ul>
    {$sSub2Elements}
</ul>
EOF;

            $sSubElements .= <<<EOF
<li class="treeItem" id="{$iSubID}" type="{$sType}">
    <img src="{$this->sABUpIcon}" class="upIcon" /><img src="{$this->sABDownIcon}" class="downIcon" /><span class="textHolder{$sTop2FlyClass}"><img src="{$this->sABFolderIcon}" class="folderImage" /> {$sSubName}<!--[if gte IE 7]><!--></span><!--<![endif]-->
    <!--[if lte IE 6]><table><tr><td><![endif]-->
    {$sSub2Elements}
    <!--[if lte IE 6]></td></tr></table></span><![endif]-->
</li>
EOF;
        } else {
            $sSubElements .= <<<EOF
<li class="treeItem" id="{$iSubID}" type="{$sType}"><img src="{$this->sABUpIcon}" class="upIcon" /><img src="{$this->sABDownIcon}" class="downIcon" /><span class="textHolder"><img src="{$this->sABFolderIcon}" class="folderImage" /> {$sSubName} </span></li>
EOF;
        }
        return $sSubElements;
    }

    function ABProcessActions() {
        if( $_REQUEST['action'] ) {
            switch( $_REQUEST['action'] ) {
                case 'edit_form':
                    $id = (int)$_REQUEST['id'];
                    
                    $aItem = db_assoc_arr( "SELECT * FROM `sys_menu_top` WHERE `ID` = '{$id}'", 0 );
                    if( $aItem )
                        echo $this->showEditForm( $aItem );
                    else
                        $this->echoMenuEditMsg( _t('_Error occured'), 'red' );
                exit;
                case 'create_item':
                    $newID = $this->createNewElement( $_GET['type'], (int)$_GET['source'] );
                    echo $newID;
                exit;
                case 'deactivate_item':
                    $res = db_res( "UPDATE `sys_menu_top` SET `Active`=0 WHERE `ID`=" . (int)$_GET['id'] );
                    echo db_affected_rows();
                    $this->compile();
                exit;
                case 'save_item':
                    $id = (int)$_POST['id'];
                    if(!$id)
                        $aResult = array('code' => 1, 'message' => _t('_Error occured'));
                    else {
                        $aItemFields = array('Name', 'Caption', 'Link', 'Picture', 'Icon');
                        $aItem = array();
                        foreach( $aItemFields as $field )
                            $aItem[$field] = $_POST[$field];
            
                        $aVis = array();
                        if( (int)$_POST['Visible_non'] )
                            $aVis[] = 'non';
                        if( (int)$_POST['Visible_memb'] )
                            $aVis[] = 'memb';
            
                        $aItem['Visible'] = implode( ',', $aVis );
                        $aItem['BQuickLink'] = (int)$_POST['BInQuickLink'] ? '1' : '0';
                        $aItem['Target'] = $_POST['Target'] == '_blank' ? '_blank' : '';

                        $aResult = $this->saveItem( $id, $aItem );
                    }

                    $aResult['message'] = MsgBox($aResult['message']);

                    $oJson = new Services_JSON();   
                    echo $oJson->encode($aResult);
                exit;
                case 'delete_item':
                    $id = (int)$_GET['id'];
                    if( !$id ) {
                        echo _t('_adm_mbuilder_Item_ID_not_specified');
                        exit;
                    }

                    $aItem = db_arr( "SELECT `Deletable` FROM `sys_menu_top` WHERE `ID` = '{$id}'" );
                    if( !$aItem ) {
                        echo _t('_adm_mbuilder_Item_not_found');
                        exit;
                    }

                    if( !(int)$aItem['Deletable'] ) {
                        echo _t('_adm_mbuilder_Item_is_non_deletable');
                        exit;
                    }

                    db_res( "DELETE FROM `sys_menu_top` WHERE `ID` = $id" );
                    if( db_affected_rows() )
                        echo 'OK';
                    else
                        echo _t('_adm_mbuilder_Could_not_delete_the_item');
                    $this->compile();
                exit;
                case 'save_orders':
                    $sTop = $_POST['top'];
                    //$aCustom = $_GET['custom'];
                    //$this->saveOrders( $sTop, $aCustom );
                    $this->saveOrders($sTop);
                    echo 'OK';
                exit;
            }
        }
    }

    // Extra functions
    function showEditForm($aItem) {
        $aForm = array(
            'form_attrs' => array(
                'id' => 'formItemEdit',
                'name' => 'formItemEdit',
                'action' => $_SERVER['PHP_SELF'],
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'inputs' => array (                        
                'Name' => array(
                    'type' => 'text',
                    'name' => 'Name',
                    'caption' => _t('_adm_mbuilder_System_Name'),
                    'value' => $aItem['Name'],
                    'attrs' => array()
                ),
                'Caption' => array(
                    'type' => 'text',
                    'name' => 'Caption',
                    'caption' => _t('_adm_mbuilder_Language_Key'),
                    'value' => $aItem['Caption'],
                    'attrs' => array()
                ),
                'LangCaption' => array(
                    'type' => 'text',
                    'name' => 'LangCaption',
                    'caption' => _t('_adm_mbuilder_Default_Name'),
                    'value' => _t( $aItem['Caption'] ),
                    'attrs' => array()
                ),
                'Link' => array(
                    'type' => 'text',
                    'name' => 'Link',
                    'caption' => _t('_URL'),
                    'value' => htmlspecialchars_adv( $aItem['Link'] ),
                    'attrs' => array()
                ),            
                'Picture' => array(
                    'type' => 'text',
                    'name' => 'Picture',
                    'caption' => _t('_Picture'),
                    'value' => htmlspecialchars_adv( $aItem['Picture'] ),
                    'attrs' => array()
                ),
                'Icon' => array(
                    'type' => 'text',
                    'name' => 'Icon',
                    'caption' => _t('_adm_mbuilder_icon'),
                    'value' => htmlspecialchars_adv( $aItem['Icon'] ),
                    'attrs' => array()
                ),
                'BInQuickLink' => array(
                    'type' => 'checkbox',
                    'name' => 'BInQuickLink',
                    'caption' => _t('_adm_mbuilder_Quick_Link'),
                    'value' => 'on',
                    'checked' => $aItem['BQuickLink'] != 0,
                    'attrs' => array()
                ),
                'Target' => array(
                    'type' => 'radio_set',
                    'name' => 'Target',
                    'caption' => _t('_adm_mbuilder_Target_Window'),
                    'value' => $aItem['Target'] == '_blank' ? '_blank' : '_self',
                    'values' => array(
                        '_self' => _t('_adm_mbuilder_Same'),
                        '_blank' => _t('_adm_mbuilder_New')
                    ),
                    'attrs' => array()
                ),
                'Visible' => array(
                    'type' => 'checkbox_set',
                    'name' => 'Visible',
                    'caption' => _t('_adm_mbuilder_Visible_for'),
                    'value' => array(),
                    'values' => array(
                        'non' => _t('_Guest'),
                        'memb' => _t('_Member')
                    ),
                    'attrs' => array()
                ),
                'submit' => array(
                    'type' => 'input_set',
                    array(
                        'type' => 'button',
                        'name' => 'save',
                        'value' => _t('_Save Changes'),
                        'attrs' => array(
                            'onclick' => 'javascript:saveItem(' . $aItem['ID'] . ');'
                        )
                    ),                
                    array(
                        'type' => 'button',
                        'name' => 'delete',
                        'value' => _t('_Delete'),
                        'attrs' => array(
                            'onclick' => 'javascript:overDeleteItem(' . $aItem['ID'] . ');'
                        )
                    )
                ),                
            )
        );
        
        foreach($aForm['inputs'] as $sKey => $aInput)
            if(in_array($aInput['type'], array('text', 'checkbox')) && !$aItem['Editable'])
                $aForm['inputs'][$sKey]['attrs']['disabled'] = "disabled";

        if(strpos($aItem['Visible'], 'non') !== false)
            $aForm['inputs']['Visible']['value'][] = 'non';
        if(strpos($aItem['Visible'], 'memb') !== false)
            $aForm['inputs']['Visible']['value'][] = 'memb';

        $oForm = new BxTemplFormView($aForm);
        return PopupBox('tmc_edit_popup', _t('_adm_mbuilder_edit_item'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode() . LoadingBox('formItemEditLoading'))));
    }

    function createNewElement( $type, $source ) {
        if( $source ) {
            $sourceActive = db_value( "SELECT `Active` FROM `sys_menu_top` WHERE `ID`='{$source}'" );
            if( !$sourceActive ) {
                //convert to active
                db_res( "UPDATE `sys_menu_top` SET `Active`=1, `Type`='{$type}' WHERE `ID`='{$source}'" );
                $newID = $source;
            } else {
                //create from source
                db_res( "INSERT INTO `sys_menu_top`
                            ( `Name`, `Caption`, `Link`, `Visible`, `Target`, `Onclick`, `Check`, `Type` )
                        SELECT
                              `Name`, `Caption`, `Link`, `Visible`, `Target`, `Onclick`, `Check`, '{$type}'
                        FROM `sys_menu_top`
                        WHERE `ID`='{$source}'" );
                $newID = db_last_id();
            }
        } else {
            //create new
            db_res( "INSERT INTO `sys_menu_top` ( `Name`, `Type` ) VALUES ( 'NEW ITEM', '{$type}' )" );
            $newID = db_last_id();
        }
        
        $this->compile();
        return $newID;
    }

    function echoMenuEditMsg( $text, $color = 'black' ) {
        echo <<<EOF
<div onclick="hideEditForm();" style="color:{$color};text-align:center;">{$text}</div>
<script type="text/javascript">setTimeout( 'hideEditForm();', 1000 )</script>
EOF;
    }

    function saveItem( $id, $aItem ) {
        $sSavedC = _t('_Saved');
        $sItemNotFoundC = _t('_adm_mbuilder_Item_not_found');
        $sItemNonEditableC = _t('_adm_mbuilder_Item_is_non_editable');
        
        $aOldItem = db_arr( "SELECT * FROM `sys_menu_top` WHERE `ID`='{$id}'" );
        
        if(!$aOldItem)
            return array('code' => 2, 'message' => $sItemNotFoundC);

        if((int)$aOldItem['Editable'] != 1)
            return array('code' => 3, 'message' => $sItemNonEditableC);
        
        $sQuerySet = '';
        foreach( $aItem as $field => $value )
            $sQuerySet .= ", `{$field}`='" . process_db_input( $value ) ."'";
        
        $sQuerySet = substr( $sQuerySet, 1 );
        
        $sQuery = "UPDATE `sys_menu_top` SET {$sQuerySet} WHERE `ID` = '{$id}'";

        db_res( $sQuery );
        $this->compile();

        return array('code' => 0, 'message' => $sSavedC, 'timer' => 3);
    }

    function saveOrders($sTop) {
        $sUnits = trim($sTop, '|');
        $aUnits = explode('|', $sUnits);

        $aTopUnits = array();
        $aSubUnits = array();
        foreach ($aUnits as $sSunit) {
            $aSUnits = explode(':', $sSunit);

            if ($aSUnits[0]=='') {
                $aTopUnits[$aSUnits[2]]= $aSUnits[1];
            } else {
                $aSubUnits[(int)$aSUnits[0]][$aSUnits[2]]= $aSUnits[1];
            }
        }
        ksort($aTopUnits);
        reset($aTopUnits);

        db_res( "UPDATE `sys_menu_top` SET `Order` = 0, `Parent` = 0" );

        foreach($aTopUnits as $iOrd => $sID) {
            $iID = (int)$sID;
            if(! $iID) continue;
            $sType = 'top';
            $sType = (strlen($sID)>1 && substr($sID, -1) == 's') ? 'system' : 'top';
            db_res( "UPDATE `sys_menu_top` SET `Order` = '{$iOrd}', `Type` = '{$sType}' WHERE `ID` = '{$iID}'" );
        }

        foreach($aSubUnits as $iParent => $aCustomIDs) {
            $iParent = (int)$iParent;
            ksort($aCustomIDs);
            reset($aCustomIDs);
            foreach($aCustomIDs as $iOrd => $iID) {
                $iID = (int)$iID;
                if(! $iID) continue;
                db_res( "UPDATE `sys_menu_top` SET `Order` = '{$iOrd}', `Type` = 'custom', `Parent`='{$iParent}' WHERE `ID` = '{$iID}'" );
            }
        }
        $this->compile();
    }
}
