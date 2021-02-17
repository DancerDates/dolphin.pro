<?php 
if(isset($_POST['ajax2']))
{
require_once( '../../../inc/header.inc.php');
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_MODULES.'ibdw/profilecover/config.php');
function getCoverAlbumUri($idprofile)
{
 $usernameis=getUsername($idprofile);
 $defaultcoveralbumname="SELECT `VALUE` from sys_options WHERE Name='bx_photos_profile_cover_album_name'";
 $runthisq=mysqli_query($defaultcoveralbumname);
 $getifexists=mysqli_num_rows($runthisq);
 if ($getifexists>0)
 {
  $acnameis=mysqli_fetch_assoc($runthisq);
  $can=$acnameis['VALUE'];
  $coveralbumname=str_replace("{nickname}",$usernameis,$can);
  $defaulturiis="SELECT Uri FROM sys_albums WHERE Caption='".addslashes($coveralbumname)."'";
  $runthisq=mysqli_query($defaulturiis);
  $getifexists=mysqli_num_rows($runthisq);
  if ($runthisq>0)
  {
   $cauriget=mysqli_fetch_assoc($runthisq);
   $coveralbumUri=$cauriget['Uri'];
  }
  $coveralbumurl=BX_DOL_URL_ROOT.'m/photos/albums/my/add_objects/'.$coveralbumUri.'/owner/'.$usernameis;
 }
 return $coveralbumUri;
}
}

//photodeluxe check installation
$photodeluxeinstalled = 0; 
$verphotodeluxe="SELECT uri FROM sys_modules WHERE uri = 'photo_deluxe'";
$runverphotodeluxe=mysqli_query($verphotodeluxe);
$ifonephotodeluxe=mysqli_num_rows($runverphotodeluxe);
if($ifonephotodeluxe!=0) $photodeluxeinstalled=1;

$IDmio=$_COOKIE['memberID'];
$idprofile=getID($_REQUEST['ID']); 

//if($_REQUEST['ID']=="") $idprofile=$_COOKIE['profileID'];
$profilevector = getProfileInfo($idprofile);




//return the url of the profile photo manager (dolphin default)
function getEditProfilePhotoUri($idprofile)
{
 $usernameis=getUsername($idprofile);
 $defaultvalue="SELECT `VALUE` from sys_options WHERE Name='bx_photos_profile_album_name'";
 $runthisq=mysqli_query($defaultvalue);
 $getifexists=mysqli_num_rows($runthisq);
 if ($getifexists>0)
 {
  $anameis=mysqli_fetch_assoc($runthisq);
  $an=$anameis['VALUE'];
  $albumname=str_replace("{nickname}",$usernameis,$an);
  $defaulturiis="SELECT Uri FROM sys_albums WHERE Caption='".addslashes($albumname)."'";
  
  $runthisq=mysqli_query($defaulturiis);
  $getifexists=mysqli_num_rows($runthisq);
  if ($runthisq>0)
  {
   $auriget=mysqli_fetch_assoc($runthisq);
   $albumUri=$auriget['Uri'];
  }
  $albumurl=BX_DOL_URL_ROOT.'m/photos/albums/my/manage_profile_photos/'.$albumUri.'/owner/'.$usernameis;
 }
 return $albumurl;
}

function issubscribed($io, $lui)
{
   $sQuerysubsc="SELECT COUNT(*) FROM sys_sbs_entries WHERE subscriber_id=".$io." AND object_id=".$lui." AND (subscription_id=3 OR subscription_id=4 OR subscription_id=5)";  
   return db_value($sQuerysubsc) ? true : false; 
}

//IF NOT EXIST, THE PROFILE COVER ALBUM WILL BE CREATED
if ($IDmio==$idprofile)
{ 
 if($usedefaultCover=="on")
  {
   $nomealbum=getCoverAlbumUri($idprofile);
   $nomealbumtrue=addslashes($nomealbum);
   $estrazione="SELECT ID,ObjCount,Uri FROM sys_albums WHERE Owner='$IDmio' AND Uri='$nomealbum'";
  }
  else 
  {
   $nomealbum="profilecover";
   $nomealbumtrue=addslashes($coveralbumname);
   $estrazione="SELECT ID,ObjCount,Uri FROM sys_albums WHERE Owner='$IDmio' AND Uri='$nomealbum'";
  }
 
 
 $runquery=mysqli_query($estrazione);
 $verificanumero=mysqli_num_rows($runquery);
 
 if($verificanumero==0)
 { 
  $instquery="INSERT INTO sys_albums (Caption,Uri,Location,Type,Owner,Status,ObjCount,LastObjId,AllowAlbumView,Date) VALUES ('".$nomealbumtrue."','".$nomealbum."','Undefined','bx_photos','".$IDmio."','Active','0','0','3',UNIX_TIMESTAMP( ))";
  $runquery_exe = mysqli_query($instquery);
 }
 else {
 $estrazione_albumid = mysqli_fetch_assoc($runquery);
 $id_album_predef = $estrazione_albumid['ID'];
 }
}
$qpf="SELECT Hash, PositionY, PositionX, ibdw_profile_cover.width FROM ibdw_profile_cover WHERE Owner=".$idprofile. " ORDER BY ID DESC Limit 0,1"; 
$resultpf = mysqli_query($qpf);
$contarespf = mysqli_num_rows($resultpf);    

$mainpf = mysqli_fetch_assoc($resultpf);
if ($contarespf==0)
{
 $defaultimage='modules/ibdw/profilecover/templates/base/images/default.jpg';
 $defaultposition="0px 0px";
 $defY=0;
}
else
{
 $defaultimagetwidth=$mainpf['width'];
 $checkifexistimage="SELECT ID FROM bx_photos_main WHERE Hash='".$mainpf['Hash']."'";
 $resultchk = mysqli_query($checkifexistimage);
 $contachk = mysqli_num_rows($resultchk);
 if($contachk>0) 
 {
  $defY=$mainpf['PositionY'];
  $defaultimage='m/photos/get_image/file/'.$mainpf['Hash'].'.jpg';
  $defaultposition=$mainpf['PositionX']."px ".$defY."px";
 }
 else
 {
  $defaultimage='modules/ibdw/profilecover/templates/base/images/default.jpg';
  $defaultposition="0px 0px";
  $defY=0;
  $contarespf=0;
 }
}
?>

<img src="<?php echo BX_DOL_URL_ROOT.$defaultimage;?>" id="dummy" style="display:none;" alt="" />
<script>
$(document).ready(function()
{
 var profileimagesize='<?php echo $profileimagetypeis;?>';
 var initw = $("#profilecover_main").width();
 if (initw==0) {initw=$(".page_column_single").width()-2;$("#profilecover_main").width(initw);}
   
 
 var wratio=(initw/<?php if ($defaultimagetwidth==0 || $defaultimagetwidth =='')$defaultimagetwidth=0.25; echo (int)$defaultimagetwidth;?>); 
 var inith = initw*<?php if ($xyfactor==0 || $xyfactor =='')$xyfactor=0.25; echo $xyfactor;?>;
 
 $("#profilecover_main").height(inith);
 var scale = <?php echo $xyfactor;?>;
 initposy=<?php echo $defY;?>*wratio;
 $("#profilecover_main").css("background-position","0 "+initposy+"px");
 
 $('#dummy').ready(function() 
 {
  $("#profilecover_main").css("background-image","url(<?php echo BX_DOL_URL_ROOT.$defaultimage;?>)");
  $("#pfblockconteiner").addClass("coversloaded");
  $("#loading_div_Start").delay(1500).hide(0); 
 });  
 
 if(profileimagesize=='Picture')
 {
  if(inith<200) 
  {
   $("#ibox").removeClass("avatarboxbig");
   $("#ibox").addClass("avatarboxsmall");
   $("#PiBox").removeClass("ProfileboxBIG");
   $("#PiBox").addClass("ProfileboxSMALL");
   $("div.profmenuitem a").removeClass("linkitem");
   $("div.profmenuitem a").addClass("linkitemSMALL");
   $("#idtitle").removeClass("titlecover");
   $("#idtitle").addClass("titlecoverSMALL");
   $("#headtext").removeClass("enfasi");
   $("#headtext").addClass("enfasiSMALL");
   $("#avtarea").removeClass("avatararea");
   $("#avtarea").addClass("avatarareaVERYSMALL");   
  }
  else
  {
   $("#ibox").removeClass("avatarboxsmall");
   $("#ibox").addClass("avatarboxbig");
   $("#PiBox").removeClass("ProfileboxSMALL");
   $("#PiBox").addClass("ProfileboxBIG");
   $("div.profmenuitem a").removeClass("linkitemSMALL");
   $("div.profmenuitem a").addClass("linkitem");
   $("#idtitle").removeClass("titlecoverSMALL");
   $("#idtitle").addClass("titlecover");
   $("#headtext").removeClass("enfasiSMALL");
   $("#headtext").addClass("enfasi");
   $("#avtarea").removeClass("avatarareaVERYSMALL");
   $("#avtarea").addClass("avatararea");
  }
  if($("#PiBox").height()>(inith-50)) $("#profmenu").css("display","none");
  else $("#profmenu").css("display","block");
 }
 else
 {
  if(inith<200) 
  {
   $("div.profmenuitem a").removeClass("linkitem");
   $("div.profmenuitem a").addClass("linkitemSMALL");
   $("#idtitle").removeClass("titlecover");
   $("#idtitle").addClass("titlecoverSMALL");
   $("#headtext").removeClass("enfasi");
   $("#headtext").addClass("enfasiSMALL");
   $("#avtarea").removeClass("avatararea");
   $("#avtarea").addClass("avatarareaVERYSMALL");
  }
  else
  {
   $("div.profmenuitem a").removeClass("linkitemSMALL");
   $("div.profmenuitem a").addClass("linkitem");
   $("#idtitle").removeClass("titlecoverSMALL");
   $("#idtitle").addClass("titlecover");
   $("#headtext").removeClass("enfasiSMALL");
   $("#headtext").addClass("enfasi");
   $("#avtarea").removeClass("avatarareaVERYSMALL");
   $("#avtarea").addClass("avatararea");     
  }
  $("#profmenu").css("display","block");
  
    initposy=document.getElementById('posY').value*wratio;
    var neww=$("#profilecover_main").width();
    var newh=neww*scale;
    
  if($("#PiBox").height()>(newh-50)) $("#profmenu").css("display","none");
  else $("#profmenu").css("display","block");
 }
 
 $(window).resize(function()
 {
  initposy=document.getElementById('posY').value*wratio;
  var neww=$("#profilecover_main").width();
  var newh=neww*scale;
  
  $("#profilecover_main").css("height",newh);
  
  var newyposition= newh*initposy/inith;
  $("#profilecover_main").css("background-position","0 "+newyposition+"px");
  
  if(profileimagesize=='Picture')
  {   
   if(newh<200) 
   {
    $("#ibox").removeClass("avatarboxbig");
    $("#ibox").addClass("avatarboxsmall");
    $("#PiBox").removeClass("ProfileboxBIG");
    $("#PiBox").addClass("ProfileboxSMALL");
    $("div.profmenuitem a").removeClass("linkitem");
    $("div.profmenuitem a").addClass("linkitemSMALL");
    $("#idtitle").removeClass("titlecover");
    $("#idtitle").addClass("titlecoverSMALL");
    $("#headtext").removeClass("enfasi");
    $("#headtext").addClass("enfasiSMALL");
    $("#avtarea").removeClass("avatararea");
    $("#avtarea").addClass("avatarareaVERYSMALL");
   }
   else
   {
    $("#ibox").removeClass("avatarboxsmall");
    $("#ibox").addClass("avatarboxbig");
    $("#PiBox").removeClass("ProfileboxSMALL");
    $("#PiBox").addClass("ProfileboxBIG");
    $("div.profmenuitem a").removeClass("linkitemSMALL");
    $("div.profmenuitem a").addClass("linkitem");
    $("#idtitle").removeClass("titlecoverSMALL");
    $("#idtitle").addClass("titlecover");
    $("#headtext").removeClass("enfasiSMALL");
    $("#headtext").addClass("enfasi");
    $("#avtarea").removeClass("avatarareaVERYSMALL");
    $("#avtarea").addClass("avatararea");     
   }
   $("#profmenu").css("display","block");
   if($("#PiBox").height()>(newh-50)) $("#profmenu").css("display","none");
   else $("#profmenu").css("display","block");
  }
  else
  {
   if(newh<200) 
   {
    $("div.profmenuitem a").removeClass("linkitem");
    $("div.profmenuitem a").addClass("linkitemSMALL");
    $("#idtitle").removeClass("titlecover");
    $("#idtitle").addClass("titlecoverSMALL");
    $("#headtext").removeClass("enfasi");
    $("#headtext").addClass("enfasiSMALL");
    $("#avtarea").removeClass("avatararea");
    $("#avtarea").addClass("avatarareaVERYSMALL");
   }
   else
   {
    $("div.profmenuitem a").removeClass("linkitemSMALL");
    $("div.profmenuitem a").addClass("linkitem");
    $("#idtitle").removeClass("titlecoverSMALL");
    $("#idtitle").addClass("titlecover");
    $("#headtext").removeClass("enfasiSMALL");
    $("#headtext").addClass("enfasi");
    $("#avtarea").removeClass("avatarareaVERYSMALL");
    $("#avtarea").addClass("avatararea");     
   }
   $("#profmenu").css("display","block");
   if($("#PiBox").height()>(newh-50)) $("#profmenu").css("display","none");
   else $("#profmenu").css("display","block");
  }  
 });
});

</script>
<?php
$estrazione_foto = "SELECT sys_albums_objects.id_object,sys_albums_objects.obj_order, sys_albums.Caption ,bx_photos_main.ID,bx_photos_main.Title,bx_photos_main.Hash,bx_photos_main.Ext,bx_photos_main.Title  FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID = sys_albums_objects.id_album)
               INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID='".$id_album_predef."' AND bx_photos_main.Title!='' ORDER BY obj_order ASC, id_object DESC LIMIT 0,20";
$runquery_foto = mysqli_query($estrazione_foto);
$numphotoscover=mysqli_num_rows($runquery_foto);


?>
<div id="infopoint" style="display:none;" onclick="openexplainations();"><i class="sys-icon question"></i></div>
<div id="suggestions" style="display:none;"><div id="textis"><?php echo _t('_ibdw_profilecover_suggestions');?></div><div id="check" onclick="closetext();"><i class="sys-icon check"></i></div></div>

<div id="alignator">
<div class="exit_alb" onclick="closechange_album();stopmn=0;"><i class="sys-icon remove"></i></div>
<h2 class="chooseboxtitle"><?php echo _t('_ibdw_profilecover_select_image');?></h2>
<div id="albumimgboxPC">

<?php
$countimage=0;
while($foto = mysqli_fetch_array($runquery_foto)) 
{
 $countimage++;
 echo '<img src="'.$site['url'].'m/photos/get_image/browse/'.$foto['Hash'].'.'.$foto['Ext'].'" id="imgp'.$countimage.'" style="display:none;" alt="" />';
 echo '<div id="sel'.$countimage.'" onclick="change_album(\''.$foto['Hash'].'\');" class="ibdw_photo_mainphotoC" style="background-color: #fff;background-position: center center;background-repeat: no-repeat;background-image: url(&quot;'.$site['url'].'/modules/ibdw/profilecover/templates/base/images/big-loader.gif&quot;);"></div>';
 ?>
 <script>
  $('#imgp'+<?php echo $countimage;?>).ready(function() {
        $("#sel"+<?php echo $countimage;?>).css("background-image","url(<?php echo $site['url'].'m/photos/get_image/browse/'.$foto['Hash'].'.'.$foto['Ext'];?>)"); 
    });
 </script>
 <?php
}
?>
</div></div>

<div id="profilecover_main" onmouseover="if (stopmn==0) displaymenu();" onmouseout="if (stopmn==0) closemenu(0);">
<?php
$profilethumb = get_member_thumbnail($idprofile, 'none', false);
$ProfileNameis=getNickname($idprofile);

if ($profileimagetypeis=='Picture')
{
 //Profile's photo
 $size="big";
 $nomeutente = getUsername($idprofile);
 $ottienialbum = "SELECT VALUE FROM sys_options WHERE Name='bx_photos_profile_album_name'"; 
 $runqueryalbum = mysqli_query($ottienialbum);
 $fetchalbum= mysqli_fetch_assoc($runqueryalbum);
 $nalbumm = $fetchalbum['VALUE'];
 $nomealbum = uriFilter(str_replace("{nickname}",$nomeutente,$nalbumm));
 $nomealbumtrue = str_replace("{nickname}",$nomeutente,$nalbumm); 
 $nomealbumtrue=addslashes($nomealbumtrue);
 
 $getpictid="SELECT id_object FROM sys_albums_objects WHERE id_album IN (SELECT ID FROM sys_albums WHERE sys_albums.Uri='$nomealbum') ORDER BY obj_order ASC LIMIT 1";
 $runthis=mysqli_query($getpictid);
 $checkif=mysqli_num_rows($runthis);
 
 if($activeEditProfileThumbsLink=="on" and $IDmio==$idprofile)
  {
   echo "<style>#ibox:hover{cursor:pointer}</style>";
   $actionlink=getEditProfilePhotoUri($idprofile);
   $actiontext='onclick=location.href="'.$actionlink.'"';
  }
  else $actiontext="";
 
 if($checkif>0)
 {
  $idpicis=mysqli_fetch_assoc($runthis);
  $idis=$idpicis['id_object']; 
  $getpictid="SELECT bx_photos_main.Hash FROM bx_photos_main WHERE ID=".$idis." AND Owner=".$idprofile;
  $runthis=mysqli_query($getpictid);
   
  $contarisfoto=mysqli_num_rows($runthis);
  if ($contarisfoto>0)
  {
   $mainfoto=mysqli_fetch_assoc($runthis);
   $profilethumb="<div id='avtarea' class='avatararea'><div id='ibox' ".$actiontext." class='avatarboxbig' style='background-image:url(".BX_DOL_URL_ROOT."m/photos/get_image/file/".$mainfoto[Hash].".jpg)'>";
  }
  else $profilethumb="<div id='avtarea' class='avatararea'><div id='ibox' class='avatarboxbig' style=''><i class='sys-icon user'></i>";
 }
 else $profilethumb="<div id='avtarea' class='avatararea'><div id='ibox' class='avatarboxbig' style=''><i class='sys-icon user'></i>"; 
}
else 
{
 $profilethumb = "<div id='avtarea' class='avatararea'><div id='ibox' class='avatarbox'>".get_member_thumbnail($profilevector['ID'], 'none', false);
}
echo $profilethumb."</div>";

if ($profileimagetypeis=='Picture')
{
 echo '<div id="PiBox" class="ProfileboxBIG"><div id="idtitle" class="titlecover">'.$ProfileNameis.'</div>';
}
else echo '<div id="PiBox" class="Profilebox"><div id="idtitle" class="titlecover">'.$ProfileNameis.'</div>';
if ($Displayheadline=="on") 
{ 
 //check if the status message is available in the Profiles Table
 $chSM="SHOW COLUMNS FROM `Profiles` LIKE 'UserStatusMessage'";
 $result = mysqli_query($chSM);
 $exists = (mysqli_num_rows($result))?TRUE:FALSE;
 
 if ($exists)
 {
  $getSMQ="SELECT UserStatusMessage FROM Profiles WHERE ID=".$idprofile;
  $result=mysqli_query($getSMQ);
  $exists = (mysqli_num_rows($result))?TRUE:FALSE;
  if ($exists)
  {
   $getStatusMessage=mysqli_fetch_assoc($result);
   $statusMessage=$getStatusMessage['UserStatusMessage'];
   if ($statusMessage<>"") echo '<div id="headtext" class="enfasi">'.$statusMessage.'</div>';
  }
 }
}
echo '<div id="profmenu">';
if($friends=='on')
{
 //friends button
 if (getFriendNumber($idprofile)>0) $numberoffriends=" <span class='infobase'>".getFriendNumber($idprofile)."</span> ";
 else $numberoffriends="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'viewFriends.php?iUser='.$idprofile.'" class="linkitem">'._t("_Friends").$numberoffriends.'</a></div>';
}
if($photos=='on')
{
 //photos button
 $getnumphoto=mysqli_query("SELECT COUNT(*) as total FROM bx_photos_main WHERE Owner=".$idprofile);
 $nphoto=mysqli_fetch_assoc($getnumphoto);
 if ($nphoto['total']>0) $numberofphotos=" <span class='infobase'>".$nphoto['total']."</span> ";
 else $numberofphotos="";
 if($photodeluxeinstalled==1) echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'page/photodeluxe?ui='.$profilevector['NickName'].'&profileID='.$idprofile.'" class="linkitem">'._t("_bx_photos").$numberofphotos.'</a></div>';
 else echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/photos/albums/browse/owner/'.$profilevector['NickName'].'" class="linkitem">'._t("_bx_photos").$numberofphotos.'</a></div>';
}
if($sounds=='on')
{
 //sounds button
 $getnumsounds=mysqli_query("SELECT COUNT(*) as total FROM RayMp3Files WHERE Owner=".$idprofile);
 $nsounds=mysqli_fetch_assoc($getnumsounds);
 if ($nsounds['total']>0) $numberofsounds=" <span class='infobase'>".$nsounds['total']."</span> ";
 else $numberofsounds="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/sounds/albums/browse/owner/'.$profilevector['NickName'].'" class="linkitem">'._t("_bx_sounds").$numberofsounds.'</a></div>';
}

if($videos=='on')
{
 //videos button
 $getnumvideos=mysqli_query("SELECT COUNT(*) as total FROM RayVideoFiles WHERE Owner=".$idprofile);
 $nvideos=mysqli_fetch_assoc($getnumvideos);
 if ($nvideos['total']>0) $numberofvideos=" <span class='infobase'>".$nvideos['total']."</span> ";
 else $numberofvideos="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/videos/albums/browse/owner/'.$profilevector['NickName'].'" class="linkitem">'._t("_bx_videos").$numberofvideos.'</a></div>';
}

if($groups=='on' and $groupsmod=='Boonex')
{
 //groups button 
 $getnumgroups=mysqli_query("SELECT COUNT(*) as total FROM bx_groups_main WHERE author_id=".$idprofile);
 $ngroups=mysqli_fetch_assoc($getnumgroups);
 if ($ngroups['total']>0) $numberofgroups=" <span class='infobase'>".$ngroups['total']."</span> ";
 else $numberofgroups="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/groups/browse/user/'.$profilevector['NickName'].'" class="linkitem">'._t("_bx_groups").$numberofgroups.'</a></div>';
}
elseif($groups=='on' and $groupsmod=='Modzzz')
{
 //groups button 
 $getnumgroups=mysqli_query("SELECT COUNT(*) as total FROM bx_groups_main WHERE author_id=".$idprofile);
 $ngroups=mysqli_fetch_assoc($getnumgroups);
 if ($ngroups['total']>0) $numberofgroups=" <span class='infobase'>".$ngroups['total']."</span> ";
 else $numberofgroups="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/groups/browse/user/'.$profilevector['NickName'].'" class="linkitem">'._t("_bx_groups").$numberofgroups.'</a></div>';
}

if($events=='on' and $groupsmod=='Boonex')
{
 //events button 
 $getnumevents=mysqli_query("SELECT COUNT(*) as total FROM bx_events_main WHERE ResponsibleID=".$idprofile);
 $nevents=mysqli_fetch_assoc($getnumevents);
 if ($nevents['total']>0) $numberofevents=" <span class='infobase'>".$nevents['total']."</span> ";
 else $numberofevents="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/events/browse/user/'.$profilevector['NickName'].'" class="linkitem">'._t("_bx_events").$numberofevents.'</a></div>';
}
elseif($events=='on' and $groupsmod=='Modzzz')
{
 //events button 
 $getnumevents=mysqli_query("SELECT COUNT(*) as total FROM bx_events_main WHERE ResponsibleID=".$idprofile);
 $nevents=mysqli_fetch_assoc($getnumevents);
 if ($nevents['total']>0) $numberofevents=" <span class='infobase'>".$nevents['total']."</span> ";
 else $numberofevents="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/events/browse/user/'.$profilevector['NickName'].'" class="linkitem">'._t("_bx_events").$numberofevents.'</a></div>';
}

if($ads=='on' and $adsmod=='Boonex')
{
 //ads button 
 $getnumads=mysqli_query("SELECT COUNT(*) as total FROM bx_ads_main WHERE IDProfile=".$idprofile);
 $nads=mysqli_fetch_assoc($getnumads);
 if ($nads['total']>0) $numberofads=" <span class='infobase'>".$nads['total']."</span> ";
 else $numberofads="";
 if ($IDmio<>$idprofile) $adspath="ads/member_ads/".$idprofile;
 else $adspath="ads/my_page/";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.$adspath.'" class="linkitem">'._t("_bx_ads_Ads").$numberofads.'</a></div>';
}
else if($ads=='on' and $adsmod=='Modzzz')
{
 //ads button 
 $getnumads=mysqli_query("SELECT COUNT(*) as total FROM modzzz_classified_main WHERE author_id=".$idprofile);
 $nads=mysqli_fetch_assoc($getnumads);
 if ($nads['total']>0) $numberofads=" <span class='infobase'>".$nads['total']."</span> ";
 else $numberofads="";
 if ($IDmio<>$idprofile) $adspath="m/classified/browse/user/".$profilevector['NickName'];
 else $adspath="m/classified/browse/my";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.$adspath.'" class="linkitem">'._t("_sys_module_classified").$numberofads.'</a></div>';
}

if($polls=='on')
{
 //polls button 
 $getnumpolls=mysqli_query("SELECT COUNT(*) as total FROM bx_poll_data WHERE id_profile=".$idprofile);
 $npolls=mysqli_fetch_assoc($getnumpolls);
 if ($npolls['total']>0) $numberofpolls=" <span class='infobase'>".$npolls['total']."</span> ";
 else $numberofpolls="";
 if ($IDmio<>$idprofile) $pollpath="m/poll/&action=user&nickname=".$profilevector['NickName'];
 else $pollpath="m/poll/&action=my";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.$pollpath.'" class="linkitem">'._t("_bx_polls").$numberofpolls.'</a></div>';
}

if($sites=='on')
{
 //sites button 
 $getnumsites=mysqli_query("SELECT COUNT(*) as total FROM bx_sites_main WHERE ownerid=".$idprofile);
 $nsites=mysqli_fetch_assoc($getnumsites);
 if ($nsites['total']>0) $numberofsites=" <span class='infobase'>".$nsites['total']."</span> ";
 else $numberofsites="";
 if ($IDmio<>$idprofile) $sitespath="m/sites/browse/user/".$profilevector['NickName'];
 else $sitespath="m/sites/browse/my";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.$sitespath.'" class="linkitem">'._t("_bx_sites").$numberofsites.'</a></div>';
}

if($blogs=='on' and $blogsmod=='Boonex')
{
 //blogs button 
 $getnumblogs=mysqli_query("SELECT COUNT(*) as total FROM bx_blogs_main WHERE OwnerID=".$idprofile);
 $nblogs=mysqli_fetch_assoc($getnumblogs);
 if ($nblogs['total']>0) $numberofblogs=" <span class='infobase'>".$nblogs['total']."</span> ";
 else $numberofblogs="";
 if ($IDmio<>$idprofile) $blogspath="blogs/member_posts/".$profilevector['ID'];
 else $blogspath="blogs/my_page/";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.$blogspath.'" class="linkitem">'._t("_bx_blog_Blogs").$numberofblogs.'</a></div>';
}
elseif($blogs=='on' and $blogsmod=='Modzzz')
{
 //blogs button 
 $getnumblogs=mysqli_query("SELECT COUNT(*) as total FROM modzzz_blogger_main WHERE author_id=".$idprofile);
 $nblogs=mysqli_fetch_assoc($getnumblogs);
 if ($nblogs['total']>0) $numberofblogs=" <span class='infobase'>".$nblogs['total']."</span> ";
 else $numberofblogs="";
 if ($IDmio<>$idprofile) $blogspath="m/blogger/browse/user/".$profilevector['ID'];
 else $blogspath="m/blogger/browse/my";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.$blogspath.'" class="linkitem">'._t("_modzzz_blogger").$numberofblogs.'</a></div>';
}

if($pages=='on')
{
 if ($pagesmod=="Zarcon") 
 {
 //pages button 
 $getnumpages=mysqli_query("SELECT COUNT(*) as total FROM bx_pages_main WHERE author_id=".$idprofile);
 $npages=mysqli_fetch_assoc($getnumpages);
 if ($npages['total']>0) $numberofpages=" <span class='infobase'>".$npages['total']."</span> ";
 else $numberofpages="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/pages/browse/user/'.$profilevector['NickName'].'" class="linkitem">'._t("_bx_pages").$numberofpages.'</a></div>';
 }
 elseif ($pagesmod=="AntonLV") 
 {
 //pages button 
 $getnumpages=mysqli_query("SELECT COUNT(*) as total FROM aqb_pages_main WHERE author_id=".$idprofile);
 $npages=mysqli_fetch_assoc($getnumpages);
 if ($npages['total']>0) $numberofpages=" <span class='infobase'>".$npages['total']."</span> ";
 else $numberofpages="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/aqb_pages/browse/user/'.$profilevector['NickName'].'" class="linkitem">'._t("_aqb_pages_menu_root").$numberofpages.'</a></div>';
 }
 elseif ($pagesmod=="Modzzz") 
 {
 //pages button 
 $getnumpages=mysqli_query("SELECT COUNT(*) as total FROM modzzz_page_main WHERE author_id=".$idprofile);
 $npages=mysqli_fetch_assoc($getnumpages);
 if ($npages['total']>0) $numberofpages=" <span class='infobase'>".$npages['total']."</span> ";
 else $numberofpages="";
 echo '<div class="profmenuitem"><a href="'.BX_DOL_URL_ROOT.'m/page/browse/user/'.$profilevector['NickName'].'" class="linkitem">'._t("_modzzz_page_pages").$numberofpages.'</a></div>';
 }
}



if (isLogged())
{

 if ($IDmio==$idprofile) echo '<div class="profmenuitem"><a class="linkitem" onclick="window.open (\''.BX_DOL_URL_ROOT.'pedit.php?ID='.$idprofile.'\',\'_self\');">'._t('_Edit').'</a></div>';
 if (!issubscribed($IDmio,$idprofile)) echo '<div class="profmenuitem"><a class="linkitem" onclick="oBxDolSubscription.subscribe('.$IDmio.', \'profile\', \'\', '.$idprofile.')">'._t('_Subscribe').'</a></div>';
 else echo '<div class="profmenuitem"><a class="linkitem" onclick="oBxDolSubscription.unsubscribe('.$IDmio.', \'profile\', \'\', '.$idprofile.')">'._t('_sys_btn_sbs_unsubscribe').'</a></div>';
 if ($IDmio<>$idprofile and is_friends($IDmio,$idprofile)) echo '<div class="profmenuitem"><a class="linkitem" onclick="$.post(\'list_pop.php?action=remove_friend\', {ID: '.$idprofile.'}, function(sData){document.location.href=document.location.href;}); return false;">'._t('_Remove friend').'</a></div>';
 elseif($IDmio<>$idprofile) echo '<div class="profmenuitem"><a class="linkitem" onclick="$.post(\'list_pop.php?action=friend\', {ID: '.$idprofile.'}, function(sData){$(\'#ajaxy_popup_result_div_'.$idprofile.'\').html(sData);}); return false;">'._t('_Befriend').'</a></div>';
 if($IDmio<>$idprofile) echo '<div class="profmenuitem"><a class="linkitem" onclick="window.open (\''.BX_DOL_URL_ROOT.'mail.php?mode=compose&amp;recipient_id='.$idprofile.'\',\'_self\');">'._t('_Message').'</a></div>';

}
echo '</div>';

echo "</div></div>";
echo '<input type="hidden" id="posX" value="'.$mainpf['PositionX'].'"><input type="hidden" id="posY" value="'.$mainpf['PositionY'].'">';
if ($IDmio==$idprofile) 
{
 
 echo 
 '<div id="covermainmenu">
   <div id="buttonitems"><button id="mainb" class="bx-btn bx-btn-small bx-btn-ifont" onclick="openmenu();"><i class="sys-icon pencil" id="iconmain"></i>'._t('_ibdw_profilecover_covermainmenu').'</button></div>
   <div id="submenus">';
    if($numphotoscover>0) echo '<div class="menuline" onclick="openchange_album();stopmn=1;closemenu();"><i class="sys-icon picture-o"></i><span>'._t('_ibdw_profilecover_fromalbum').'</span></div>';   
    if ($photodeluxeinstalled>0) echo '<div class="menuline" onclick="location.href=\'page/photodeluxe\'"><i class="sys-icon folder"></i><span>'._t('_ibdw_profilecover_otheralbums').'</span></div>';
    echo '<div class="menuline" onclick="ibdw_cover_frompc('.$id_album_predef.','.$IDmio.');stopmn=1;closemenu();"><i class="sys-icon upload"></i><span>'._t('_ibdw_profilecover_upload').'</span></div>';
    if ($contarespf>0) echo '<div class="menuline" onclick="moveimagey('.$IDmio.',\''.$mainpf['Hash'].'\',0,document.getElementById(\'posY\').value);" id="movemenu"><i class="sys-icon move"></i><span>'._t('_ibdw_profilecover_position').'</span></div>';
    if ($contarespf>0) echo '<div class="menuline" onclick="ibdw_cover_remove('.$IDmio.',&quot;'.$mainpf['Hash'].'&quot;,\''.BX_DOL_URL_ROOT.'\');" id="removemenu"><i class="sys-icon remove"></i><span>'._t('_ibdw_profilecover_remove').'</span></div>';
 echo '</div></div>';
}
echo '<div id="modificaalbums"></div>';
?>
</div>
<?php
if(isset($_POST['ajax2']))
{
?>
<script>
$(document).ready(function() 
{ 
 $("#posY").val(0); 
 $("#profilecover_main").css('backgroundPosition', '0 0');
 
 $('#dummy').load(function () {
  moveimagey(<?php echo $IDmio;?>,'<?php echo $mainpf['Hash'];?>',0,document.getElementById("posY").value);
  openmenu(); 
 });
 
}); 
</script>
<?php
}
?>