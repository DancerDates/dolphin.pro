<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolRequest.php' );
if(isset($aRequest[0]) && substr($aRequest[0], 0, 4) == 'act_') {
    $aRequest[0] = str_replace('act_', '', $aRequest[0]);
    echo BxDolRequest::processAsAction($aModule, $aRequest);
}
else
    BxDolRequest::processAsAction($aModule, $aRequest);