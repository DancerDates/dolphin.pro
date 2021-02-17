<?php
/***************************************************************************
*                            Dolphin Smart Schools Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Schools
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Schools Builder
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

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxSchoolsInstructorFormSelect extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function __construct ($oMain) { 
  
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
   
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_instructor',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array( 
                    'submit_name' => 'submit_init',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_subprofile_form_header_info')
                ),       
				'type' => array(
                    'type' => 'select',
                    'name' => 'type', 
                    'caption' => _t('_modzzz_schools_form_caption_instructor_type'),
                    'values' => array(
						'external' => _t('_modzzz_schools_option_external'),
						'internal' => _t('_modzzz_schools_option_internal'),
					), 
                    'attrs' => array( 
						'id' => 'select_type',
						'onchange' => 'bindAuto()' 
					)  
                 ),
                 'profile_nick' => array(
                    'type' => 'text',
                    'name' => 'profile_nick',
                    'caption' => _t('_modzzz_schools_form_caption_enter_nick'),
                    'info' => _t('_modzzz_schools_form_info_enter_nick'),

                    'attrs' => array(
 						'id' => 'profile_nick',
					) 
                ), 
				'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_init',
					'value' => _t('_modzzz_schools_form_caption_continue'),
					'colspan' => false
				),  

            ),            
        );
 
        parent::__construct ($aCustomForm);
    }
  

}
