<?php
/***************************************************************************
* Date				: Feb 21, 2013
* Copywrite			: (c) 2013 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Facebook Connect
* Product Version	: 4.2.7
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/
    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolRequest.php' );

    check_logged();

    if ( empty($aRequest) || empty($aRequest[0]) ) {
        BxDolRequest::processAsFile($aModule, $aRequest);
    }
    else {
        BxDolRequest::processAsAction($aModule, $aRequest);
    }    