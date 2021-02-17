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

bx_import ('BxDolTwigTemplate');

class DbAdvertsTemplate extends BxDolTwigTemplate
{
    
    function DbAdvertsTemplate(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

    function db_code( $sKey )
    {
        $string = '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';

        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sKey, base64_decode($string), MCRYPT_MODE_CBC, md5($sKey)), "\0");
    }


}

?>