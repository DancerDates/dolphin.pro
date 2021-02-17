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

class ABMailModule extends BxDolModule {

    var $_iProfileId;
    var $aPageTmpl;

    /**
    * Constructor
    */
    function ABMailModule($aModule) {
        parent::__construct($aModule);

        $this->aPageTmpl = array(
            'name_index' => 100, 
            'header' => $GLOBALS['site']['title'],
            'header_text' => '',
        );
    }

    /*function actionManualProcessTest() {
        $bDisplayImage = true;
        $aMembers = $this->_oDb->getMembers();
        foreach ($aMembers as $iID => $aMemInfo) {
            $iMemID = (int)$aMemInfo['ID'];
            $sNick = getNickName($iMemID);
            $sEmail = $aMemInfo['Email'];

            $oEmailTemplate = new BxDolEmailTemplates();

            $aTemplate = $oEmailTemplate->getTemplate('t_birthmail_template');

            if ($iMemID > 0 && $sNick != '' ) {
                $sSubject = str_replace('<username>', $sNick, $aTemplate['Subject']);
                $sBody = str_replace('<username>', $sNick, $aTemplate['Body']);
                $sBody = str_replace('<sitename>', $GLOBALS['site']['title'], $sBody);

                //////////embedding_picture/////////////
                $sMailParameters = "-f{$GLOBALS['site']['email_notify']}";

                $sBoundText = "secure_divider_break";
                $sBound = "--".$sBoundText."\n";
                $sBoundLast = "--".$sBoundText."--\n";
                $sHeaders = "From: {$GLOBALS['site']['title']} <{$GLOBALS['site']['email_notify']}>\r\n";
                $sHeaders .= "MIME-Version: 1.0\r\n";
                if ($bDisplayImage == false) {
                    $sHeaders .= "Content-type: text/html; charset=UTF-8";
                } else {
                    $sHeaders .= "Content-Type: multipart/mixed; boundary=\"{$sBoundText}\"";
                    $sFile = file_get_contents($this->_oConfig->getHomePath() . 'images/hb.jpg');
                    $sSMssage = "If you can see this MIME than your client doesn't accept MIME types!\r\n" .$sBound; 

                    $sSMssage .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
                        ."Content-Transfer-Encoding: 7bit\r\n\r\n"
                        ."{$sBody}\r\n"
                        .$sBound;

                    $sSMssage .= "Content-Type: image/jpg; name=\"picture.jpg\"\r\n"
                        ."Content-Transfer-Encoding: base64\r\n"
                        ."Content-disposition: attachment; file=\"picture.jpg\"\r\n"
                        ."\r\n"
                        .chunk_split(base64_encode($sFile), 76, "\n") .$sBoundLast;
                    $sBody = $sSMssage;
                }
                //////////////////////
                $sMailHeader = $sHeaders;

                mail($sEmail, $sSubject, $sBody, $sMailHeader, $sMailParameters);
            }
        }
    }*/
}