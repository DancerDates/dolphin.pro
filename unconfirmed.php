<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*
* This file is part of Dolphin - Smart Community Builder
*
* This file is part of a Dolphin Mod made by Mika_P, designed to block
* unconfirmed members from doing almost anything while they are logged in
* to your site.
* This particular file creates the page shown for unconfirmed members
* while they attempt to browse your site, as long as they don't verify
* their email address.
* 
* For more information: http://www.boonex.com/market/posts/mika_p
***************************************************************************/

ob_start();
require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
ob_end_clean();

$_page['name_index'] 	= 1;

// New lang keys here for page title and header. Make sure to add them.
$_page['header'] = _t( "_Waiting_Email_Conf" );
$_page['header_text'] = _t( "_Waiting_Email_Conf_Header");

$_ni = $_page['name_index'];

//This is an existing lang key, used as the main content of the page. Change it if you like.
$action_result = _t( "_ATT_UNCONFIRMED_E"); 

$sPageCode = <<<BLAH
            <div class="bx-def-margin-sec-bottom bx-def-font-large">
                $action_result
            </div>
BLAH;

$_page_cont[$_ni]['page_main_code'] = DesignBoxContent($_page['header_text'], $sPageCode, 11);

PageCode();

?>