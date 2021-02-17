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

bx_import('BxDolTwigModuleDb');

class DbAdvertsDb extends BxDolTwigModuleDb
{
    function DbAdvertsDb(&$oConfig) {
        parent::__construct();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }

    function getAdv($width, $TF, $iType, $sPage)
    {
        $sPage = process_db_input($sPage);

        if(0 == $iType)
        {
            // all types
            $sWhere = '(`Page` = "all" OR `Page` = "") AND ';
        } else if(1 == $iType)
        {
            // only for page
            $sWhere = '`Page` = "'.$sPage.'" AND ';

        } else if(2 == $iType)
        {
           // all except for page
           $sWhere = '`Page != "'.$sPage.'" AND ';
        }

        $sAdvert = '<center><h2>Advertise here</h2></center>';
        $sDate = date("Y-m-d");

        $iWidth = (int)$width;
        $iTF = (int)$TF;
        $iSize = $this->getOne("SELECT `width` FROM `db_adverts_advert` WHERE $sWhere `Active` = 1 AND `width` <= $iWidth AND (`fold` = $iTF OR `fold` = 0) AND `campaign_start` <= '".$sDate."' AND `campaign_end` >= '".$sDate."' ORDER BY `width` DESC LIMIT 1");

        $aAdverts = $this->getAll("SELECT `code` FROM `db_adverts_advert` WHERE $sWhere `width` = '".$iSize."' AND `Active` = 1 AND (`fold` = $iTF OR `fold` = 0) AND `campaign_start` <= '".$sDate."' AND `campaign_end` >= '".$sDate."'");
        if(!empty($aAdverts))
        {
            $iAdvert = array_rand($aAdverts,1);
            $sAdvert = $aAdverts[$iAdvert]['code'];
        }
        return $sAdvert;
    } 

    function getAdvert($iID)
    {
        $iId = (int)$iID;
        return $this->getRow("SELECT * FROM `db_adverts_advert` WHERE `ID` = $iId LIMIT 1");
    }

    function getBlock($iID)
    {
        $iId = (int)$iID;
        return $this->getRow("SELECT * FROM `sys_page_compose` WHERE `ID` = $iId LIMIT 1");
    }

    function getAdverts($iPage=1, $iPerPage=10)
    {
        $iStart = ($iPage - 1) * $iPerPage;
        return $this->getAll("SELECT * FROM `db_adverts_advert` LIMIT $iStart, $iPerPage");
    }

    function countObjects($sTable)
    {
        if("ads" == $sTable)
        {
            $sSelectedTable = 'db_adverts_advert';
            $where = '';
        } else if ("blocks" == $sTable)
        {
            $sSelectedTable = 'sys_page_compose';
            $where = 'WHERE `Desc` = "Db Adverts"';
        } else
            return;

        return $this->getOne("SELECT count(*) FROM $sSelectedTable $where");
    }

    function getBlocks($iPage, $iPerPage)
    {
        $iStart = ($iPage - 1) * $iPerPage;

        return $this->getAll("SELECT * FROM `sys_page_compose` WHERE `Desc` = 'Db Adverts' LIMIT $iStart, $iPerPage");
    }

    function getPages() {
            return $this->getAll("SELECT * FROM `sys_page_compose_pages`");
    }

    function copyBlock($iId)
    {
        $sQuery = "INSERT INTO `sys_page_compose` (`Page`,`PageWidth`,`Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache`) SELECT `Page`,`PageWidth`,`Desc`, CONCAT (`Caption`,' (COPY)'), 0, 0, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache` FROM `sys_page_compose` WHERE `ID` = $iId";
        if ( db_res($sQuery ) ) return true;
            return false;
    }

    function deleteBlock($iId)
    {
        $sQuery = "DELETE FROM `sys_page_compose` WHERE `id` = $iId";
        if ( db_res($sQuery ) ) return true;
            return false;
    }

    function copyAdvert($iId)
    {
        $sQuery = "INSERT INTO `db_adverts_advert` (`Title`,`Width`,`Fold`,`Page`,`Code`,`Active`, `Created`, `campaign_start`,`campaign_end`) SELECT CONCAT (`Title`,' (COPY)'),`Width`,`Fold`,`Page`,`Code`,0, now(),`campaign_start`,`campaign_end` FROM `db_adverts_advert` WHERE `id` = $iId";

        if ( db_res($sQuery ) ) return true;
            return false;
    }

    function deleteAdvert($iId)
    {
        $sQuery = "DELETE FROM `db_adverts_advert` WHERE `id` = $iId";
        if ( db_res($sQuery ) ) return true;
            return false;
    }

    function activateAdvert($iId)
    {
        $sQuery = "UPDATE `db_adverts_advert` SET `Active` = 1 WHERE `id` = $iId";
        if ( db_res($sQuery ) )
            return true;
        return false;
    }

    function deactivateAdvert($iId)
    {
        $sQuery = "UPDATE `db_adverts_advert` SET `Active` = 0 WHERE `id` = $iId";
        if ( db_res($sQuery ) )
            return true;
        return false;
    }
}

?>