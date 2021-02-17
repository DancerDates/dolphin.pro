<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx School
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

bx_import('BxDolProfileFields');

class BxSchoolsFormSearch extends BxTemplFormView {

    function __construct () {

		//[begin] - ultimate schools mod from modzzz  
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
        $aCountries = array_merge (array('' => _t('_modzzz_schools_all_countries')), $aCountries);
  
        $oMain = BxDolModule::getInstance('BxSchoolsModule');

		$sStateUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/?ajax=state&country=' ;
		//[end] - ultimate schools mod from modzzz 


        bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();
        $aCategories = $oCategories->getCategoriesList('modzzz_schools', (int)$iProfileId, true);

		//[begin] - ultimate schools mod from modzzz  
		$aCategories[''] = _t('_modzzz_schools_all_categories');  
		//[end] - ultimate schools mod from modzzz 

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_schools',
                'action'   => '',
                'method'   => 'get',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(
                'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_modzzz_schools_form_caption_keyword'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_schools_form_err_keyword'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
                'Category' => array(
                    'type' => 'select_box',
                    'name' => 'Category',
                    'caption' => _t('_modzzz_schools_form_caption_category'),
                    'values' => $aCategories,
					 /*       
					'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_schools_form_err_category'),
                    ),  
					*/	
					'required' => false,
					/*
					'checker' => array (
					'func' => 'avail',
					'error' => _t ('_modzzz_schools_form_err_category'),
					),  
				    */ 

                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ),

				//[begin] - ultimate schools mod from modzzz   
				'Country' => array(
					'type' => 'select',
					'name' => 'Country',
					'caption' => _t('_modzzz_schools_form_caption_country'),
					'values' => $aCountries,
					'required' => false, 
					'attrs' => array(
					'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					), 
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([a-zA-Z]{0,2})/'),
					),                    
				),

				'State' => array(
					'type' => 'select',
					'name' => 'State', 
					'caption' => _t('_modzzz_schools_form_caption_state'),
							'attrs' => array(
					'id' => 'substate',
							), 
						  'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([a-zA-Z]+)/'),
					), 
				),
						 
				'City' => array(
					'type' => 'text',
					'name' => 'City',
					'caption' => _t('_modzzz_schools_form_caption_city'),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),                
				), 
			   //[end] - ultimate schools mod from modzzz  

                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => false,
                ),
            ),            
        );

        parent::__construct ($aCustomForm);
    }
}
