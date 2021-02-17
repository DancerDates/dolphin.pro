<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.modloaded.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@modloaded.com
***************************************************************************/
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

bx_import('BxDolTwigModule');
bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxTemplSearchResult');


class MlPhotoRModule extends BxDolTwigModule {

    function MlPhotoRModule(&$aModule) {

        parent::__construct($aModule);
    }
		function actionSave($iId, $sExt, $sAngle)
		{
			$iOwner = db_value("SELECT `Owner` FROM `bx_photos_main` WHERE `ID` = '{$iId}'");
			if ($iOwner != getLoggedId() && !isAdmin())
			{
				echo _t('_ml_photo_rotator_cannot_save');	
				return;
			}
			if ($iId && $sAngle && $sExt)
			{
				//
				$sImage = BX_DIRECTORY_PATH_ROOT . "modules/boonex/photos/data/files/{$iId}.{$sExt}";
				$sImageRotated = "{$sImage}";
				$this->_rotateImage($sImage,$sImageRotated,$sAngle);
				
				$sExt = "jpg";
				
				//m
				$sImage_m = BX_DIRECTORY_PATH_ROOT . "modules/boonex/photos/data/files/{$iId}_m.{$sExt}";
				$sImageRotated_m = "{$sImage_m}";
				$this->_rotateImage($sImage_m,$sImageRotated_m,$sAngle);

				//ri
				$sImage_ri = BX_DIRECTORY_PATH_ROOT . "modules/boonex/photos/data/files/{$iId}_ri.{$sExt}";
				$sImageRotated_ri = "{$sImage_ri}";
				$this->_rotateImage($sImage_ri,$sImageRotated_ri,$sAngle);
				
				//rt
				$sImage_rt = BX_DIRECTORY_PATH_ROOT . "modules/boonex/photos/data/files/{$iId}_rt.{$sExt}";
				$sImageRotated_rt = "{$sImage_rt}";
				$this->_rotateImage($sImage_rt,$sImageRotated_rt,$sAngle);

				//t
				$sImage_t = BX_DIRECTORY_PATH_ROOT . "modules/boonex/photos/data/files/{$iId}_t.{$sExt}";
				$sImageRotated_t = "{$sImage_t}";
				$this->_rotateImage($sImage_t,$sImageRotated_t,$sAngle);
				
				echo _t('_ml_photo_rotator_saved');												
			}
		}
		function _rotateImage($sSourceFile,$sDestImageName,$sDegreeOfRotation)
		{
				  //get the detail of the image
		  $aImageInfo=getimagesize($sSourceFile);
		  switch($aImageInfo['mime'])
		  {
		   //create the image according to the content type
		   case "image/jpg":
		   case "image/jpeg":
		   case "image/pjpeg": //for IE
		        $src_img=imagecreatefromjpeg("$sSourceFile");
		                break;
		    case "image/gif":
		        $src_img = imagecreatefromgif("$sSourceFile");
		                break;
		    case "image/png":
		        case "image/x-png": //for IE
		        $src_img = imagecreatefrompng("$sSourceFile");
		                break;
		  }
		  //rotate the image according to the spcified degree
		  $src_img = imagerotate($src_img, $sDegreeOfRotation, 0);
		  //output the image to a file
	  	imagejpeg ($src_img,$sDestImageName);
		}

    function serviceRotator($iId, $sHash) {
  		if (!$sHash) return;
    	$aInfo = db_arr("SELECT `ID`, `Ext` FROM `bx_photos_main` WHERE `Hash` = '{$sHash}'");

    	$sImage = BxDolService::call('photos','get_img_url',array($sHash,'file'),'Search');
    	//var_dump($aImage);
    	//exit;
    	$aVars = array(
    		'clockwise' => BX_DOL_URL_ROOT . 'modules/modloaded/photo_rotator/templates/base/images/icons/clockwise.png',
    		'anticlockwise' => BX_DOL_URL_ROOT . 'modules/modloaded/photo_rotator/templates/base/images/icons/anticlockwise.png',
    		'save' => BX_DOL_URL_ROOT . 'modules/modloaded/photo_rotator/templates/base/images/icons/save.png',
    		'save_title' => _t('_ml_photo_rotator_save_title'),
    		'saving' => _t('_ml_photo_rotator_saving'),
    		'action' => BX_DOL_URL_ROOT . 'm/photo_rotator/save',
    		'get_action' => BX_DOL_URL_ROOT . 'm/photo_rotator/get_rotated_image',
    		'pic' => $sImage,
    		'id' => "{$aInfo[ID]}",
    		'ext' => "{$aInfo[Ext]}",
    		'hash' => $sHash
    		);

    	$this->_oTemplate->addJs ('jQueryRotate.js');
    	$this->_oTemplate->addCss ('rotator.css');
    	return $this->_oTemplate->parseHtmlByName('rotator', $aVars); 
    }

    function actionGetRotatedImage($sHash){
    	return BxDolService::call('photos','get_img_url',array($sHash,'file'),'Search');
    }
    function actionAdministration () {

        if (!$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the admin design

	    $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
	    if(empty($iId)) { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_ml_photo_rotator'));
            return;
        }

        bx_import('BxDolAdminSettings'); // import class

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) { // save settings
	        $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId); // get display form code
        $sResult = $oSettings->getForm();
        	       
        if($mixedResult !== true && !empty($mixedResult)) // attach any resulted messages at the form beginning
            $sResult = $mixedResult . $sResult;

        echo DesignBoxAdmin (_t('_ml_photo_rotator'), $sResult); // dsiplay box
        
        $this->_oTemplate->pageCodeAdmin (_t('_ml_photo_rotator')); // output is completed, admin page will be displaed here
    }   
		


}

?>
