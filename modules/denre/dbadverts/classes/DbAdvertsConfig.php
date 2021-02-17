<?
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

bx_import('BxDolConfig');

class DbAdvertsConfig extends BxDolConfig {

    function DbAdvertsConfig($aModule)
    {
        parent::__construct($aModule);
        $this->sPrefix = 'db_adverts';
    }

    function getActionArray ($sType='')
    {
        $sPref = '_' . $this->sPrefix . '_admin_';
        $btnExtraArray = array();

        $btnArray = array(
            'action_add' => array(
                'caption' => $sPref . 'add',
                'method' => '_addMessage'
            ),
            'action_copy' => array(
                'caption' => $sPref . 'copy',
                'method' => '_copyMessage'
            ),
            'action_delete' => array(
                'caption' => $sPref . 'delete',
                'method' => '_deleteMessage'
            ),
        );

        if('adverts' == $sType)
        {
            $btnExtraArray = array(
                'action_activate' => array(
                    'caption' => $sPref . 'activate',
                    'method' => '_activateMessage'
                ),
                'action_deactivate' => array(
                    'caption' => $sPref . 'deactivate',
                    'method' => '_deactivateMessage'
                ),
            );
        }

        return array_merge($btnArray, $btnExtraArray);
    }

    function getKey()
    {
        return '3c6469762069643d2264';
    }

}

?>