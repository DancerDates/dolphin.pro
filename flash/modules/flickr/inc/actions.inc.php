<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by Rayz Expert. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Rayz Expert.
* This notice may not be removed from the source code.
*
***************************************************************************/

$sId = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
$sId1 = isset($_REQUEST['id1']) ? $_REQUEST['id1'] : "";
$sUser = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
$sMember = isset($_REQUEST['member']) ? $_REQUEST['member'] : "";
$sPassword = isset($_REQUEST['password']) ? $_REQUEST['password'] : "";
$sSkin = isset($_REQUEST['skin']) ? $_REQUEST['skin'] : "default";
$sLanguage = isset($_REQUEST['language']) ? $_REQUEST['language'] : "english";

$sCategoryId = isset($_REQUEST['category']) ? $_REQUEST['category'] : "0";
$sDescription = isset($_REQUEST['description']) ? addslashes($_REQUEST['description']) : "";
$sExtension = isset($_REQUEST['ext']) ? $_REQUEST['ext'] : "";

$sPhoto = isset($_REQUEST['photo']) ? $_REQUEST['photo'] : "";
$sThumbImage = isset($_REQUEST['thumb']) ? $_REQUEST['thumb'] : "";
$sPlayImage = isset($_REQUEST['play']) ? $_REQUEST['play'] : "";
$sSaveImage = isset($_REQUEST['save']) ? $_REQUEST['save'] : "";
$sAuthor = isset($_REQUEST['author']) ? addslashes($_REQUEST['author']) : "Unknown";
$sTitle = isset($_REQUEST['title']) ? addslashes($_REQUEST['title']) : "";
$sTags = isset($_REQUEST['tags']) ? addslashes($_REQUEST['tags']) : "";
$sRating = isset($_REQUEST['rating']) ? $_REQUEST['rating'] : "0.0";
$iUploaded = isset($_REQUEST['uploaded']) ? $_REQUEST['uploaded'] : 0;
$iRate = isset($_REQUEST['rate']) ? $_REQUEST['rate'] : 5;

$sParam = isset($_REQUEST['param']) ? $_REQUEST['param'] : "";
$sValue = isset($_REQUEST['value']) ? addslashes($_REQUEST['value']) : "";

$iPage = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
$iItemsPerPage = isset($_REQUEST['perPage']) && is_numeric($_REQUEST['perPage']) ? $_REQUEST['perPage'] : 25;
$sMode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "top";

switch ($sAction)
{
    /**
    * gets skins
    */
    case 'getSkins':
        $sContents = printFiles($sModule, "skins");
        break;

    /**
    * Sets default skin.
    */
    case 'setSkin':
        setCurrentFile($sModule, $sSkin, "skins");
        break;

    /**
    * gets languages
    */
    case 'getLanguages':
        $sContents = printFiles($sModule, "langs", false, true);
        break;

    /**
    * Sets default language.
    */
    case 'setLanguage':
        setCurrentFile($sModule, $sLanguage, "langs");
        break;

    /**
    * Get chat's config.
    */
    case 'config':
        $sFileName = $sModulesPath . $sModule . "/xml/config.xml";
        $rHandle = fopen($sFileName, "rt");
        $sContents = fread($rHandle, filesize($sFileName));
        $sContents = str_replace("#categoryImage#", $sDefaultCategory, $sContents);
        $sContents = str_replace("#filesUrl#", $sFilesUrl, $sContents);
        $sShowHints = empty($_COOKIE["RayzShowHints"]) ? TRUE_VAL : $_COOKIE["RayzShowHints"];
        $sContents = str_replace("#showHints#", $sShowHints, $sContents);
        fclose($rHandle);
        break;
        
    case 'setShowHints':
        $sValue = isset($_REQUEST['value']) ? $_REQUEST['value'] : "";
        if(empty($sValue)) break;
        setCookie("RayzShowHints", $sValue, time() + 31536000);
        break;
        
    /**
    * Authorize user
    */
    case 'userAuthorize':
        $sContents = parseXml($aXmlTemplates['result'], loginUser($sUser, $sPassword));
        break;

    /**
    * Authorize admin
    */
    case 'adminAuthorize':
        $sContents = parseXml($aXmlTemplates['result'], loginAdmin($sUser, $sPassword));
        $sContents .= parseXml($aXmlTemplates['result'], $aInfo['version']);
        break;

///// CATEGORIES METHODS BEGIN/////
    case 'getCategories':
        $iCount = @getValue("SELECT COUNT(`ID`) AS `Count` FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `Category`=1 AND `Parent`=0");
        if($bShort) $iItemsPerPage = $iCount;
        $sSql = "SELECT `cats`.*, COUNT(`els`.`ID`) AS `Count` FROM `" . MODULE_DB_PREFIX . "Elements` AS `cats` LEFT JOIN `" . MODULE_DB_PREFIX . "Elements` AS `els` ON `cats`.`ID`=`els`.`Parent` WHERE `cats`.`Category`=1 AND `cats`.`Parent`=0 GROUP BY `cats`.`ID` ORDER BY `cats`.`Order` ASC, `cats`.`ID` DESC";
        $rResult = @getResult($sSql . getPagination($iPage, $iItemsPerPage));
            
        $sCats = "";
        for($i=0; $i<mysql_num_rows($rResult); $i++)
        {
            $aCat = mysql_fetch_assoc($rResult);
            $sCats .= parseXml($aXmlTemplates["element"], $aCat['ID'], getCatImage($aCat['ID']), stripslashes($aCat['Title']), stripslashes($aCat['Desc']), $aCat['Param'], stripslashes($aCat['Value']));
        }
        $sContents = parseXml($aXmlTemplates["result"], "", SUCCESS_VAL, $iPage, $iItemsPerPage, $iCount);
        $sContents .= makeGroup($sCats, "elements");
        break;

    case 'newCategory':
        $sCategoryId = addCategory($sTitle, $sDescription, $sParam, $sValue, $sCategoryId);
        $sContents = parseXml($aXmlTemplates["result"], $sCategoryId, SUCCESS_VAL);
        break;
        
    case 'editCategory':
        $sContents = parseXml($aXmlTemplates["result"], "Error changing category", FAILED_VAL);
        $rResult = getResult("UPDATE `" . MODULE_DB_PREFIX . "Elements` SET `Title`='" . $sTitle . "', `Desc`='" . $sDescription . "', `Param`='" . $sParam . "', `Value`='" . $sValue . "' WHERE `ID`='" . $sId . "'");
        if(!$rResult) break;
            
        $sContents = parseXml($aXmlTemplates["result"], "", SUCCESS_VAL);
        break;
    
    case 'uploadCategoryImage':
        $sOldFile = $sFilesPath . getCatImage($sId);
        $sFileName = $sFilesPath . $sId . "_cat." . $sExtension;
        if(is_uploaded_file($_FILES['Filedata']['tmp_name']))
        {
            @unlink($sOldFile);
            move_uploaded_file($_FILES['Filedata']['tmp_name'], $sFileName);
            @chmod($sFileName, 0644);
        }
        break;

    case 'checkCategoryImage':
        $sFileName = $sFilesPath . $sId . "_cat." . $sExtension;
        if(!file_exists($sFileName) || filesize($sFileName) == 0)
            $sContents = parseXml($aXmlTemplates['result'], "Error uploading file.", FAILED_VAL);
        else
            $sContents = parseXml($aXmlTemplates['result'], $sId . "_cat." . $sExtension, SUCCESS_VAL);
        break;

    case 'removeCategory':
        if(loginAdmin($sUser, $sPassword) != TRUE_VAL)
        {
            $sContents = parseXml($aXmlTemplates['result'], "Access error!", FAILED_VAL);
            break;
        }
        $iParentId = getValue("SELECT `Parent` FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `ID`='" . $sId . "'");
        getResult("DELETE FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `ID`='" . $sId . "'");
        @unlink($sFilesPath . getCatImage($sId));
        if(mysql_affected_rows($oDb->rLink) > 0)
        {
            getResult("UPDATE `" . MODULE_DB_PREFIX . "Elements` SET `Order`=(`Order`-1) WHERE `Category`=1 AND `Parent`=" . $iParentId);
            $sContents = parseXml($aXmlTemplates["result"], $sId, SUCCESS_VAL);
        }
        else $sContents = parseXml($aXmlTemplates["result"], "Error removing category", FAILED_VAL);
        break;

    /**
    * change categories order
    */
    case 'changeCategoriesOrder':
        $rResult = getResult("SELECT `ID`, `Order` FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `ID`='" . $sId . "' OR `ID`='" . $sId1 . "'");
        $aCat1 = mysql_fetch_assoc($rResult);
        $aCat2 = mysql_fetch_assoc($rResult);
        getResult("UPDATE `" . MODULE_DB_PREFIX . "Elements` SET `Order`='" . $aCat2['Order'] . "' WHERE `ID`='" . $aCat1['ID'] . "'");
        getResult("UPDATE `" . MODULE_DB_PREFIX . "Elements` SET `Order`='" . $aCat1['Order'] . "' WHERE `ID`='" . $aCat2['ID'] . "'");
        break;
        
    case 'getCategoryContents':
        $sMode = "category";
        //break shouldn't be here
///// CATEGORIES METHODS END /////

    /**
    * get photos ()all, top, latest, member)
    */
    case 'getElements':
        $bPrintUser = true;
        $bPaginate = true;
        $sPublicFactor = "WHERE `Public`='" . TRUE_VAL . "'";
        $sCount = "SELECT COUNT(`ID`) AS `Count` ";
        $sSelect = "SELECT * ";
        switch($sMode)
        {
            case 'category':
                $sSql = "";
                $sCount = "SELECT COUNT(`ID`) AS `Count` FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `Parent`=" . $sCategoryId;
                $sSelect = "SELECT `cats`.*, COUNT(`els`.`ID`) AS `Count` FROM `" . MODULE_DB_PREFIX . "Elements` AS `cats` LEFT JOIN `" . MODULE_DB_PREFIX . "Elements` AS `els` ON `cats`.`ID`=`els`.`Parent` WHERE `cats`.`Category`=1 AND `cats`.`Parent`=" . $sCategoryId . " GROUP BY `cats`.`ID` ORDER BY `cats`.`Order` ASC, `cats`.`ID` DESC";
                break;
            case 'member':
                $sSql = "FROM `" . MODULE_DB_PREFIX . "Elements` " . $sPublicFactor . " AND `Category`=0 AND `User`='" . $sMember . "' ORDER BY `ID` DESC";
                break;
            case 'favorites':
                $sCount = "SELECT COUNT(`El`.`ID`) AS `Count` ";
                $sSelect = "SELECT `El`.* ";
                $sSql = "FROM `" . MODULE_DB_PREFIX . "Elements` AS `El` INNER JOIN `" . MODULE_DB_PREFIX . "Favorites` AS `Fv` WHERE `El`.`Category`=0 AND `El`.`ID`=`Fv`.`Element` AND `Fv`.`User`='" . $sUser ."' ORDER BY `Fv`.`ID` DESC";
                $sUser = "";
                break;
            case 'viewed':
                $sSql = "FROM `" . MODULE_DB_PREFIX . "Elements` " . $sPublicFactor . " AND `Category`=0 ORDER BY `Views` DESC, `Title` ASC";
                break;
            case 'top':
            default:
                $sOrder = getSettingValue($sModule, "topByRating") == TRUE_VAL ? "`Rating`" : "(`Rating`*`Voted`)";
                $sSql = "FROM `" . MODULE_DB_PREFIX . "Elements` " . $sPublicFactor . " AND `Category`=0 ORDER BY " . $sOrder . " DESC, `Title` ASC";
                break;
        }
        $iCount = @getValue($sCount . $sSql);
        if(!$bPaginate) $iItemsPerPage = $iCount;
        $rResult = @getResult($sSelect . $sSql . ($bPaginate ? getPagination($iPage, $iItemsPerPage) : ""));
        if(!$rResult)
        {
            $sContents = parseXml($aXmlTemplates["result"], "msgErrorGetPhotos", FAILED_VAL);
            break;
        }
        
        $sContents = parseXml($aXmlTemplates["result"], "", SUCCESS_VAL, $iPage, $iItemsPerPage, $iCount);
        $sElements = "";
        $iCurrentTime = time();
        for($i=0; $i<mysql_num_rows($rResult); $i++)
        {
            $aElement = mysql_fetch_assoc($rResult);
            if($aElement['Category'])
            {
                $sElements .= parseXml($aXmlTemplates['element'], $aElement['ID'], getCatImage($aElement['ID']), stripslashes($aElement['Title']), stripslashes($aElement['Desc']), $aElement['Param'], $aElement['Value'], $aElement['Count']);
            }
            else
            {            
                $iDate = $iCurrentTime - ($aElement['Date'] - $iRunTime);
                $sFavorite = (!empty($sUser) && isFavorite($sUser, $aElement['ID'])) ? TRUE_VAL : FALSE_VAL;
                $aVotes = empty($aElement['Votes']) ? array() : unserialize($aElement['Votes']);
                $iVote = isset($aVotes[$sUser]) ? $aVotes[$sUser] : 0;            
                $aUser = getUserInfo($aElement['User']);
                $sElements .= parseXml($aXmlTemplates['element'], $aElement['ID'], $aUser['nick'], $aElement['Author'], $aElement['Thumb'], $aElement['Play'], $aElement['Save'], $aElement['Author'], $iDate, $aElement['Rating'], $aElement['Voted'], $iVote, $sFavorite, $aElement['Views'], stripslashes($aElement['Title']), stripslashes($aElement['Tags']));
            }
        }
        $sContents .= makeGroup($sElements, "elements");
        break;

    /**
    * vote
    */
    case 'vote':
        $sVotes = getValue("SELECT `Votes` FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `ID` = '" . $sId . "' LIMIT 1");
        $aVotes = empty($sVotes) ? array() : unserialize($sVotes);
        $aVotes[$sUser] = $iRate;
        $iVoted = count($aVotes);
        
        $iSum = 0;
        foreach($aVotes as $iVote)$iSum += $iVote;
        $iRating = round($iSum / $iVoted, 2);
        getResult("UPDATE `" . MODULE_DB_PREFIX . "Elements` SET `Rating`='" . $iRating . "', `Voted`='" . $iVoted . "', `Votes`='" . serialize($aVotes) . "' WHERE `ID` = '" . $sId . "'");        
        $sContents = parseXml($aXmlTemplates['vote'], $iRating, $iVoted);
        break;

    case 'removePhoto':
        $sOwner = getValue("SELECT `User` FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `ID`='" . $sId . "' LIMIT 1");
        if(!(loginAdmin($sUser, $sPassword) == TRUE_VAL || (!empty($sOwner) && $sOwner == $sUser && loginUser($sUser, $sPassword) == TRUE_VAL)))
        {
            $sContents = parseXml($aXmlTemplates['result'], "Access error!", FAILED_VAL);
            break;
        }
        getResult("DELETE FROM `" . MODULE_DB_PREFIX . "Favorites` WHERE `Element`='" . $sId . "'");
        getResult("DELETE FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `ID`='" . $sId . "' LIMIT 1");
        if(mysql_affected_rows($oDb->rLink) > 0)$sContents = parseXml($aXmlTemplates["result"], "", SUCCESS_VAL);
        else                                    $sContents = parseXml($aXmlTemplates["result"], "Error removing photo", FAILED_VAL);
        break;

    case 'addPhoto':
        $sPublic = isset($_REQUEST['public']) ? $_REQUEST['public'] : TRUE_VAL;
        $aPhoto = getArray("SELECT `ID`, `Public` FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `PhotoID`='" . $sPhoto . "' LIMIT 1");
        $sId = $aPhoto['ID'];
        if(empty($sId))
        {
            getResult("INSERT INTO `" . MODULE_DB_PREFIX . "Elements`(`PhotoID`, `User`, `Thumb`, `Play`, `Save`, `Author`, `Title`, `Tags`, `Date`, `Public`) VALUES('" . $sPhoto . "', '" . $sUser . "', '" . $sThumbImage . "', '" . $sPlayImage . "', '" . $sSaveImage . "', '" . $sAuthor . "', '" . $sTitle . "', '" . $sTags . "', '" . $iUploaded . "', '" . $sPublic . "')");
            $sId = getLastInsertId();
        }
        elseif($sPublic == TRUE_VAL && $aPhoto['Public'] == FALSE_VAL)
            getResult("UPDATE `" . MODULE_DB_PREFIX . "Elements` SET `Public`='" . $sPublic . "', `User`='" . $sUser . "' WHERE `PhotoID`='" . $sPhoto . "' AND `Public`='" . FALSE_VAL . "' LIMIT 1");
        if($sPublic == TRUE_VAL)
        {
            $sContents = parseXml($aXmlTemplates['result'], $sId, SUCCESS_VAL);
            break;
        }
        //break shouldn't be here

    ////////////FAVORITES FUNCTIONS BEGIN/////////////////
    /**
    * add favorite
    */
    case 'addFavorite':
        $sContents = parseXml($aXmlTemplates['result'], $sId, SUCCESS_VAL);
        if(!isFavorite($sUser, $sId))
            getResult("INSERT INTO `" . MODULE_DB_PREFIX . "Favorites`(`User`, `Element`) VALUES('" . $sUser . "', '" . $sId . "')");
        break;

    /**
    * add favorite
    */
    case 'removeFavorite':
        getResult("DELETE FROM `" . MODULE_DB_PREFIX . "Favorites` WHERE `User`='" . $sUser . "' AND `Element`='" . $sId . "'");
        getResult("DELETE FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `User`='" . $sUser . "' AND `Photo`='" . $sId . "' AND `Public`='" . FALSE_VAL . "' LIMIT 1");
        if(mysql_affected_rows($oDb->rLink) > 0)$sContents = parseXml($aXmlTemplates["result"], TRUE_VAL);
        else                                    $sContents = parseXml($aXmlTemplates["result"], FALSE_VAL);
        break;
    ////////////FAVORITES FUNCTIONS END/////////////////
    
    case 'export':
        $bSharing = isset($_REQUEST['sharing']) ? $_REQUEST['sharing'] == TRUE_VAL : false;
        $bProfile = isset($_REQUEST['profile']) ? $_REQUEST['profile'] == TRUE_VAL : false;
        if(empty($sId)) $aPhoto = array('Save' => $sSaveImage, 'Title' => $sTitle, 'Tags' => $sTags);
        else $aPhoto = getArray("SELECT * FROM `" . MODULE_DB_PREFIX . "Elements` WHERE `ID`='" . $sId . "' LIMIT 1");
        $sContents = parseXml($aXmlTemplates['result'], "msgExportError", FAILED_VAL);
        $aResult = getTempImage($aPhoto['Save']);
        $sTempImage = $aResult['value'];
        if($bSharing && $aResult['status'] == SUCCESS_VAL) $aResult = exportSharing($sUser, $aPhoto, $sTempImage);
        if($bProfile && $aResult['status'] == SUCCESS_VAL) $aResult = exportProfile($sUser, $aPhoto, $sTempImage);
        @unlink($sTempImage);
        $sContents = parseXml($aXmlTemplates['result'], $aResult['value'], $aResult['status']);
        break;

    /**
    * add view
    */
    case 'addView':
        getResult("UPDATE `" . MODULE_DB_PREFIX . "Elements` SET `Views`=(`Views`+1) WHERE `ID`='" . $sId . "' LIMIT 1");
        break;

    case 'help':
        $sApp = isset($_REQUEST['app']) ? $_REQUEST['app'] : "admin";
        $sContents = makeGroup("", "topics");
        $sFileName = $sModulesPath . $sModule . "/help/" . $sApp . ".xml";
        if(file_exists($sFileName))
        {
            $rHandle = @fopen($sFileName, "rt");
            $sContents = @fread($rHandle, filesize($sFileName)) ;
            fclose($rHandle);
        }
        break;
}
