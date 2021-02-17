<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by BoonEx Ltd. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from BoonEx Ltd.
* This notice may not be removed from the source code.
*
***************************************************************************/

function getCatImage($sCategoryId)
{
    global $sFilesPath;
    
    if(file_exists($sFilesPath . $sCategoryId . "_cat.jpg")) return $sCategoryId . "_cat.jpg";
    if(file_exists($sFilesPath . $sCategoryId . "_cat.png")) return $sCategoryId . "_cat.png";
    if(file_exists($sFilesPath . $sCategoryId . "_cat.gif")) return $sCategoryId . "_cat.gif";
    return "";
}

function getPagination($iPage = 0, $iItemsPerPage = 25)
{
    return " LIMIT " . $iPage * $iItemsPerPage . ", " . $iItemsPerPage;
}

function addCategory($sTitle, $sDesc = "", $sParam = "tag", $sValue = "", $iParentId = 0)
{
    global $sFilesPath;
    
    $sCategoryId = getValue("SELECT `ID` FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `Category`=1 AND `Title`='" . $sTitle . "' AND `Parent`= " . $iParentId . " LIMIT 1");
    if(empty($sCategoryId))
    {
        getResult("INSERT INTO `" . MODULE_DB_PREFIX . "Elements`(`Title`, `Desc`, `Param`, `Value`, `Category`, `Parent`) VALUES('" . $sTitle . "', '" . $sDesc . "', '" . $sParam . "', '" . $sValue . "', 1, " . $iParentId . ")");
        $sCategoryId = getLastInsertId();
        getResult("UPDATE `" . MODULE_DB_PREFIX . "Elements` SET `Order`=(`Order`+1) WHERE `Category`=1 AND `Parent`=" . $iParentId);
        $sCatImage = getCatImage($sTitle);
        if(!empty($sCatImage))
        {
            $sNewImage = str_replace($sTitle, $sCategoryId, $sCatImage);
            @rename($sFilesPath . $sCatImage, $sFilesPath . $sNewImage);
        }        
    }
    return $sCategoryId;
}

function isFavorite($sUser, $sProject)
{
    $sFavoriteId = getValue("SELECT `ID` FROM `" . MODULE_DB_PREFIX . "Favorites` WHERE `User`='" . $sUser . "' AND `Element`='" . $sProject . "' LIMIT 1");
    return !empty($sFavoriteId);
}

function getTempImage($sImageUrl)
{
    if(!ini_get('allow_url_fopen'))
        return array('value' => "Can't read URLs by fopen. 'allow_url_fopen' PHP setting should be enabled. Contact hosting provider to solve the problem.", 'status' => FAILED_VAL);

    $sTempFile = tempnam ("/tmp", "tmp");
    $f = @fopen($sTempFile, "w");
    $sFileContents = @file_get_contents($sImageUrl);
    if(empty($sFileContents))
        return array('value' => "Can't connect to flickr.com. The reason is server firewall or flickr.com is down.", 'status' => FAILED_VAL);
    
    @fwrite($f, $sFileContents);
    @fclose($f);
    if($sTempFile == FALSE || !file_exists($sTempFile))
         return array('value' => "Can't create temporary file.", 'status' => FAILED_VAL);
    else if(filesize($sTempFile) == 0)
         return array('value' => "Can't read file from flickr.com", 'status' => FAILED_VAL);
    else return array('value' => $sTempFile, 'status' => SUCCESS_VAL);
}
