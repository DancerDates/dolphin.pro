<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
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
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolTwigPageView');

class BxSchoolsNewsPageView extends BxDolTwigPageView {	

    function __construct(&$oNewsMain, &$aNews) {
        parent::__construct('modzzz_schools_news_view', $oNewsMain, $aNews);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix; 
	}
   
    function getBlockCode_Info() {
        return array($this->_oTemplate->blockSubProfileInfo ('news', $this->aDataEntry));
    }

	function getBlockCode_Desc() {
		$aData = $this->aDataEntry;

        $aVars = array (
            'description' => $this->aDataEntry['desc'], 
        );

        return array($this->_oTemplate->parseHtmlByName('block_description', $aVars)); 
    }
 
	function getBlockCode_Photos() {
 
		if($this->aDataEntry['icon'] && (!$this->aDataEntry['thumb']))
			return $this->getPhoto(); 
		else
			return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);  
    }    
  
	function getPhoto() {
 
        $sImage = $this->_oMain->_oDb->getIcon($this->aDataEntry['icon']);
   
        $aVars = array (
            'image_url' => $sImage,
        );
        return $this->_oTemplate->parseHtmlByName('block_photo', $aVars);
    }
 
    function getBlockCode_Rate() {
        modzzz_schools_import('NewsVoting');
        $o = new BxSchoolsNewsVoting ('modzzz_schools_news', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableNews, $this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_schools_import('NewsCmts');
        $o = new BxSchoolsNewsCmts ('modzzz_schools_news', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aNewsEntry = $this->_oDb->getNewsEntryById($this->aDataEntry['id']);
			$iEntryId = $aNewsEntry['school_id'];
	
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                 'TitleEdit' => $this->_oMain->isAllowedEdit($aDataEntry) ? _t('_modzzz_schools_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($aDataEntry) ? _t('_modzzz_schools_action_title_delete') : '',
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotosSubProfile($this->_oDb->_sTableNews,$this->aDataEntry) ? _t('_modzzz_schools_action_upload_photos') : '', 


            );
 
            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'] && !$this->aInfo['TitleUploadPhotos'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_schools_news');
        } 

        return '';
    }    
  
    function getCode() { 
        return parent::getCode();
    }    
}
