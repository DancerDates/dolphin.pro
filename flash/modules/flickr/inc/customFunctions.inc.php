<?php
if(file_exists(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php'))
    require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
if(file_exists(BX_DIRECTORY_PATH_INC . 'tags.inc.php'))    require_once(BX_DIRECTORY_PATH_INC . 'tags.inc.php');
if(file_exists(BX_DIRECTORY_PATH_INC . 'utils.inc.php'))    require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');

if(getSettingValue($sModule, "export") == TRUE_VAL)
{
    if(file_exists($sRootPath . "uploadPhoto.php"))    require_once($sRootPath . "uploadPhoto.php");
    if(file_exists($sRootPath . "inc/params.inc.php"))    require_once($sRootPath . "inc/params.inc.php");
    if(file_exists($sRootPath . "inc/profiles.inc.php"))    require_once($sRootPath . "inc/profiles.inc.php");
}

function genUri($sName, $sTable, $sField)
{
return "";
    /*if(function_exists("uriGenerate")) return uriGenerate($sName, $sTable, $sField, 255);
    else return "";*/
}

function exportSharing($sUser, $aPhoto, $sTempImage)
{
    global $dir;
    $aError = array('value' => "msgExportError", 'status' => FAILED_VAL);
        
    $aFileInfo = getimagesize($sTempImage);
    if(!$aFileInfo) return $aError;
        
    switch($aFileInfo['mime'])
    {
        case 'image/jpeg': $sExt = 'jpg'; break;
        case 'image/gif':  $sExt = 'gif'; break;
        case 'image/png':  $sExt = 'png'; break;
        default:           return $aError;
    }
    $sActive = getParam("enable_shPhotoActivation") == 'on' ? 'true' : 'false' ;
    $sTags = implode(" ", explode(", ", $aPhoto['Tags']));
    
    $sUri = genUri($aPhoto['Title'], 'sharePhotoFiles', 'medUri');
    if(empty($sUri))getResult("INSERT INTO `sharePhotoFiles` (`medProfId`,`medTitle`,`medExt`,`medDesc`,`medTags`,`medDate`,`Approved`) VALUES('" . $sUser . "','" . $aPhoto['Title'] . "', '" . $sExt . "', '', '" . $sTags . "', '" . time() . "', '" . $sActive . "')");
    else            getResult("INSERT INTO `sharePhotoFiles` (`medProfId`,`medTitle`,`medUri`,`medExt`,`medDesc`,`medTags`,`medDate`,`Approved`) VALUES('" . $sUser . "','" . $aPhoto['Title'] . "', '" . $sUri . "', '" . $sExt . "', '', '" . $sTags . "', '" . time() . "', '" . $sActive . "')");
    
    $sId = getLastInsertId();
    reparseObjTags('photo', $iNew);

    $sNewFileName = $dir['sharingImages'] . $sId . '.' . $sExt;
    $sNewMainName = $dir['sharingImages'] . $sId . '_m.' . $sExt;
    $sNewThumbName = $dir['sharingImages'] . $sId . '_t.' . $sExt;
    if(!@copy($sTempImage, $sNewFileName)) return $aError;
        
    chmod($sNewFileName, 0644);
    $iWidth  = (int)getParam("max_photo_width");
    $iHeight = (int)getParam("max_photo_height");
    $iThumbW = (int)getParam("max_thumb_width");
    $iThumbH = (int)getParam("max_thumb_height");
    if( imageResize($sNewFileName, $sNewMainName, $iWidth, $iHeight) != IMAGE_ERROR_SUCCESS ||
        imageResize($sNewMainName, $sNewThumbName, $iThumbW, $iThumbH) != IMAGE_ERROR_SUCCESS )
        return $aError;
        
    return array('value' => "", 'status' => SUCCESS_VAL);
}

function exportProfile($sUser, $aPhoto, $sTempImage)
{
    $aError = array('value' => "msgExportError", 'status' => FAILED_VAL);
    
    $oMedia = new UploadPhoto($sUser);
    if($oMedia->iMediaCount >= $oMedia->aMediaConfig['max'][$oMedia->sMediaType]) 
        return array('value' => "msgErrorFilesCount", 'status' => FAILED_VAL);;
        
    $sMediaDir = $oMedia->getProfileMediaDir();
    if(!$sMediaDir) return $aError;
        
    $sFileName = time();
    $scan = getimagesize($sTempImage);
    if( ($scan['mime'] == 'image/jpeg' && $sExt = '.jpg') || ($scan['mime'] == 'image/gif' && $sExt = '.gif') || ($scan['mime'] == 'image/png' && $sExt = '.png') )
    {
        $sFileExt = $sFileName . $sExt;
        $sFilePath = $sMediaDir . $sFileExt;
        if(!@copy($sTempImage, $sFilePath)) return $aError;
    }
    else return $aError;
        
    if( imageResize($sFilePath, $sMediaDir . 'icon_' . $sFileExt, $oMedia->aMediaConfig['size']['iconWidth'], $oMedia->aMediaConfig['size']['iconHeight'], true) != IMAGE_ERROR_SUCCESS ||
        imageResize($sFilePath, $sMediaDir . 'thumb_' . $sFileExt, $oMedia->aMediaConfig['size']['thumbWidth'], $oMedia->aMediaConfig['size']['thumbHeight'], true) != IMAGE_ERROR_SUCCESS ||
        imageResize($sFilePath, $sMediaDir . 'photo_' . $sFileExt, $oMedia->aMediaConfig['size']['photoWidth'], $oMedia->aMediaConfig['size']['photoHeight'], true) )
        return $aError;
        
    $_POST['title'] = $aPhoto['Title'];
    $oMedia->insertMediaToDb($sFileExt);
    if(0 == $oMedia->iMediaCount || $oMedia->aMedia['0']['PrimPhoto'] == 0 )
        $oMedia->oMediaQuery->setPrimaryPhoto($oMedia->iProfileID, getLastInsertId());
    @unlink($sFilePath);
        
    return array('value' => "", 'status' => SUCCESS_VAL);
}
