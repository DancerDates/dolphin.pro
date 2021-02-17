<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Location
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

bx_import('BxDolTwigPageMain');
bx_import('BxTemplCategories');

class BxSchoolsCoursesPageBrowse extends BxDolTwigPageMain {

    function __construct(&$oMain, $sUri) {

		$this->oDb = $oMain->_oDb;
        $this->oConfig = $oMain->_oConfig;
        $this->oTemplate = $oMain->_oTemplate;
		$this->oMain = $oMain;
		$this->sUri = $sUri;
        
		$this->sUrlStart = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .  'courses/browse/' . $this->sUri;
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

        $this->sSearchResultClassName = 'BxSchoolsSearchResult';
        $this->sFilterName = 'filter';
		parent::__construct('modzzz_schools_courses_browse', $oMain);
	}
  
    function getBlockCode_Browse () {
  
        return $this->ajaxBrowseSubProfile(
            'courses',
            'courses',
            $this->oDb->getParam('modzzz_schools_perpage_browse_subitems'), 
            array(), $this->sUri, true, false 
        );
 
    }
  
    function ajaxBrowseSubProfile($sType, $sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true) {

        bx_import ('SearchResult', $this->oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);
 
        if (!($s = $o->displaySubProfileResultBlock($sType))) {
             return array(MsgBox(_t('_Empty')), $aMenu);
		} 

        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->oConfig->getBaseUri() . $o->sBrowseUrl, -1, -1, false);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }    





}
