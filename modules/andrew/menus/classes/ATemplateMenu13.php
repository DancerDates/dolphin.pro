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

class ATemplateMenu13 extends BxBaseMenu {
    var $oParent;
    var $_bVertical;

    function ATemplateMenu13($sHomeUrl, $oParent) {
        parent::BxBaseMenu();
        $this->oParent = $oParent;

        $this->oParent->_oTemplate->addCss('menu13.css');

        $this->iElementsCntInLine = 99;
        $this->_bVertical = false;
    }

    function genTopHeader() {
        $this -> sCode .= '<div class="dropList"><ul>';
    }
    
    function genTopFooter() {
        $this->sCode .= '<div class="clear_both"></div></ul></div>';
    }
    
    function genTopItem($sText, $sLink, $sTarget, $sOnclick, $bActive, $iItemID, $isBold = false, $sPicture = '') {
        $sSubMenu = '';
        if( !$bActive ) {
            $sOnclick = $sOnclick ? ( ' onclick="' . $sOnclick . '"' ) : '';
            $sTarget  = $sTarget  ? ( ' target="'  . $sTarget  . '"' ) : '';
        }

        $sSubMenu = $this->getAllSubMenus($iItemID, $bActive);
        $sLink = (strpos($sLink, 'http://') === false && !strlen($sOnclick)) ? $this->sSiteUrl . $sLink : $sLink;

        $sBoldStyle = ($isBold) ? 'style="font-weight:bold;"' : '';

        $sImgTabStyle = $sPictureRep = '';
        if($sText == '' && $isBold && $sPicture != '') {
            $sPicturePath = getTemplateIcon($sPicture);
            $sPictureRep = <<<EOF
<img src="{$sPicturePath}" style="width:16px;height:16px;" />
EOF;
        }

        if ($sSubMenu == '') {
            $this -> sCode .= <<<EOF
<li><a href="{$sLink}">{$sPictureRep}{$sText}</a></li>
EOF;
        } else {
            $this -> sCode .= <<<EOF
<li class="oneCol"><a href="{$sLink}" class="oneCol fly">{$sPictureRep}{$sText}</a>
    <div>
        <ul>
            {$sSubMenu}
        </ul>
    </div>
</li>
EOF;
        }
    }

    /*overrided*/
    function getAllSubMenus($iItemID, $bActive = false) {
        $aMenuInfo = $this->aMenuInfo;

        $ret = '';
        
        $aTTopMenu = $this->aTopMenu;

        foreach( $aTTopMenu as $iTItemID => $aTItem ) {
            if( strpos( $aTItem['Visible'], $aMenuInfo['visible'] ) === false )
                continue;

            if ($iItemID == $aTItem['Parent']) {
                //generate
                list( $aTItem['Link'] ) = explode( '|', $aTItem['Link'] );

                $aTItem['Link'] = str_replace( "{memberID}",    isset($aMenuInfo['memberID']) ? $aMenuInfo['memberID'] : '',    $aTItem['Link'] );
                $aTItem['Link'] = str_replace( "{memberNick}",  isset($aMenuInfo['memberNick']) ? $aMenuInfo['memberNick'] : '',  $aTItem['Link'] );
                $aTItem['Link'] = str_replace( "{memberLink}",  isset($aMenuInfo['memberLink']) ? $aMenuInfo['memberLink'] : '',  $aTItem['Link'] );

                $aTItem['Link'] = str_replace( "{profileID}",   isset($aMenuInfo['profileID']) ? $aMenuInfo['profileID'] : '',   $aTItem['Link'] );
                $aTItem['Onclick'] = str_replace( "{profileID}", isset($aMenuInfo['profileID']) ? $aMenuInfo['profileID'] : '',   $aTItem['Onclick'] );

                $aTItem['Link'] = str_replace( "{profileNick}", isset($aMenuInfo['profileNick']) ? $aMenuInfo['profileNick'] : '', $aTItem['Link'] );
                $aTItem['Onclick'] = str_replace( "{profileNick}", isset($aMenuInfo['profileNick']) ? $aMenuInfo['profileNick'] : '', $aTItem['Onclick'] );

                $aTItem['Link'] = str_replace( "{profileLink}", isset($aMenuInfo['profileLink']) ? $aMenuInfo['profileLink'] : '', $aTItem['Link'] );

                $aTItem['Onclick'] = str_replace( "{memberID}", isset($aMenuInfo['memberID']) ? $aMenuInfo['memberID'] : '',    $aTItem['Onclick'] );
                $aTItem['Onclick'] = str_replace( "{memberNick}",  isset($aMenuInfo['memberNick']) ? $aMenuInfo['memberNick'] : '',  $aTItem['Onclick'] );
                $aTItem['Onclick'] = str_replace( "{memberPass}",  getPassword( isset($aMenuInfo['memberID']) ? $aMenuInfo['memberID'] : ''),  $aTItem['Onclick'] );

                $sElement = $this->getCustomMenuItem( _t( $aTItem['Caption'] ), $aTItem['Link'], $aTItem['Target'], $aTItem['Onclick'], ( $iTItemID == $aMenuInfo['currentCustom'] ), false, $iTItemID );

                $ret .= $sElement;
            }
        }

        return $ret;
    }

    function getCustomMenuItem( $sText, $sLink, $sTarget, $sOnclick, $bActive, $bSub = false, $iTItemID = 0) {
        $sIActiveStyles = ($bActive) ? ' style="font-weight:bold;" ' : '';
        $sITarget = (strlen($sTarget)) ? ' target="' . $sTarget . '" ' : '';
        $sIOnclick = (strlen($sOnclick)) ? ' onclick="' . $sOnclick . '" ' : '';
        $sILink = (strpos($sLink, 'http://') === false && !strlen($sOnclick)) ? $this->sSiteUrl . $sLink : $sLink;

        if ($iTItemID) {
            $sSubMenu = $this->getAllSubMenus($iTItemID, $bActive);
        }

        if ($sSubMenu == '') { // top2
            return '<li><a href="' . $sILink . '" ' . $sITarget . $sIOnclick . $sIActiveStyles . '>' . $sText . '</a></li>';
        } else {
            return <<<EOF
<li class="fly"><a class="fly" href="{$sILink}">{$sText}</a>
    <ul>
        {$sSubMenu}
    </ul>
</li>
EOF;
        }
        return '<li><a href="' . $sILink . '" ' . $sITarget . $sIOnclick . $sIActiveStyles . '>' . $sText . '</a></li>';
    }

    function getCode() {
        if(isset($GLOBALS['bx_profiler']))
            $GLOBALS['bx_profiler']->beginMenu('Main Menu');

        $this->getMenuInfo();

        //--- Main Menu ---//
        $this -> genTopHeader();
        $this->genTopItems();
        $this -> genTopFooter();
        $sMenuMain = $this->sCode;

        //--- Submenu Menu ---//
        $this->sCode = '';
        if(!defined('BX_INDEX_PAGE') && !defined('BX_JOIN_PAGE'))
            $this->genSubMenus();

        $sResult = $this->oParent->_oTemplate->parseHtmlByName('navigation_menu.html', array(
            'main_menu' => $sMenuMain,
            'sub_menu' => $this->sCode
        ));

        if(isset($GLOBALS['bx_profiler']))
            $GLOBALS['bx_profiler']->endMenu('Main Menu');

        return $sResult;
    }
}