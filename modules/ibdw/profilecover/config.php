<?php
/**********************************************************************************
*                            IBDW Profile Cover for Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mar 18 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW Profile Cover is not free and you cannot redistribute and/or modify it.
* 
* IBDW Profile Cover is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

$retriveidprofilecover="SELECT ID FROM sys_options_cats WHERE name='Profile Cover'";
$risultatoid = mysqli_query($retriveidprofilecover);
$idmodulo=mysqli_fetch_assoc($risultatoid);
$querydiconfigurazione="SELECT Name, VALUE FROM sys_options WHERE kateg=".$idmodulo['ID'];
$risultato = mysqli_query($querydiconfigurazione);
while ($feccia=mysqli_fetch_array($risultato))
{
 $name = $feccia['Name'];
 $riga[$name]=$feccia['VALUE'];
}
$boonexdefaultcovername="SELECT `VALUE` FROM `sys_options` WHERE `Name`='bx_photos_profile_cover_album_name'";
$risultis=mysqli_query($boonexdefaultcovername);
while ($fecc=mysqli_fetch_array($risultis))
{
 $boonexcoveralbum = $fecc['VALUE'];
}

$KeyCode=$riga['KeyCode'];
$coveralbumname=str_replace(" ","-",preg_replace('/\s{2,}/',' ',$riga['AlbumCoverName']));
$friends=$riga['Friends'];
$photos=$riga['Photos'];
$sounds=$riga['Sounds'];
$videos=$riga['Videos'];
$groups=$riga['Groups'];
$groupsmod=$riga['groupsmod'];
$events=$riga['Events'];
$eventsmod=$riga['eventsmod'];
$ads=$riga['Ads'];
$adsmod=$riga['adsmod'];
$polls=$riga['Polls'];
$pollsmod=$riga['polls_mod'];
$sites=$riga['Sites'];
$blogs=$riga['Blogs'];
$blogsmod=$riga['blogsmod'];
$pages=$riga['Pages'];
$pagesmod=$riga['pagesmod'];
$Displayheadline=$riga['Displayheadline'];
$maxfilesize = $riga['maxfilesize'];
$profileimagetypeis = $riga['profileimaget'];
$xyfactor=$riga['xyfactor'];
$activeEditProfileThumbsLink=$riga['EnableEditProfilePictureLink'];
//la seguente va ancora inserita nella configurazione in amministrazione
$usedefaultCover=$riga['usedefaultCover'];
?>

