<?php
if(isset($_POST['ajax'])){
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/eventcover/config.php';
}

if ($uriis=="") 
{
 $eventid=$_POST['EventID'];
 $UriSelect="SELECT EntryUri, Title From ".$eventstableis." where ID=".$eventid; 
 $getUri=mysqli_query($UriSelect);
 $UriRetrieve=mysqli_fetch_assoc($getUri);
 $Uri= $UriRetrieve['EntryUri'];
 $uriis=$Uri;
 $eventname=$UriRetrieve['Title'];
}
//definisco il profilo amministratore se � l'amministratore del sito o l'autore del gruppo
 $getautorid="SELECT ResponsibleID FROM ".$eventstableis." WHERE id=".$eventid;
 $rungetauth=mysqli_query($getautorid);
 $fetcha=mysqli_fetch_array($rungetauth);
 $autoris=$fetcha[0];
 $IDmio=$_COOKIE['memberID'];
 $idprofile=getID($_REQUEST['ID']);
 
if (isAdmin() or $IDmio==$autoris) $amministratore=1;
else $amministratore=0;

//IF NOT EXIST, THE PAGE COVER ALBUM WILL BE CREATED
if ($IDmio==$autoris)
{
 $nomealbum="eventcover";
 $nomealbumtrue=addslashes($coveralbumname);
 
 $estrazione="SELECT ID,ObjCount,Uri FROM sys_albums WHERE Owner='$IDmio' AND Uri='$nomealbum'";
 $runquery=mysqli_query($estrazione);
 $verificanumero=mysqli_num_rows($runquery);
 if($verificanumero==0)
 { 
  $insequery="INSERT INTO sys_albums (Caption,Uri,Location,Type,Owner,Status,ObjCount,LastObjId,AllowAlbumView,Date) VALUES ('".$nomealbumtrue."','".$nomealbum."','Undefined','bx_photos','".$IDmio."','Active','0','0','3',UNIX_TIMESTAMP( ))";
  $runquery_exe = mysqli_query($insequery);
 }
 else {
 $estrazione_albumid = mysqli_fetch_assoc($runquery);
 $id_album_predef = $estrazione_albumid['ID'];
 }
}
$qpf="SELECT Hash, PositionY, PositionX, Uri, ibdw_event_cover.width FROM ibdw_event_cover WHERE Owner=".$autoris. " AND Uri='".$uriis."' ORDER BY ID DESC Limit 0,1";   
$resultpf = mysqli_query($qpf);
$contarespf = mysqli_num_rows($resultpf);
$mainpf = mysqli_fetch_assoc($resultpf);
if ($contarespf==0)
{
 $defaultimage='modules/ibdw/eventcover/templates/base/images/default.jpg';
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
  $defaultimage='modules/ibdw/eventcover/templates/base/images/default.jpg';
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
 var initw = $("#eventcover_main").width();
 if (initw==0) {initw=$(".page_column_single").width()-2;$("#eventcover_main").width(initw);}
 
  
  var wratio=(initw/<?php if ($defaultimagetwidth==0 || $defaultimagetwidth =='')$defaultimagetwidth=0.25; echo (int)$defaultimagetwidth;?>); 
  var inith = initw*<?php if ($xyfactor==0 || $xyfactor =='')$xyfactor=0.25; echo $xyfactor;?>;
  
 $("#eventcover_main").height(inith);
 var scale = <?php if ($xyfactorG==0 || $xyfactorG =='')$xyfactorG=1; echo $xyfactorG;?>;
 initposy=<?php echo $defY;?>*wratio;
 $("#eventcover_main").css("background-position","0 "+initposy+"px");
 
 $('#dummy').ready(function() {
        $("#eventcover_main").css("background-image","url(<?php echo BX_DOL_URL_ROOT.$defaultimage;?>)");
        $("#pfblockconteiner").addClass("coversloaded");
        $("#loading_div_Start").delay(1500).hide(0); 
    });  
 
 $(window).resize(function()
 {
 
  initposy=document.getElementById('posY').value*wratio;
  var neww=$("#eventcover_main").width();
  var newh=neww*scale;
  
  $("#eventcover_main").css("height",newh);
  
  var newyposition= newh*initposy/inith;
  $("#eventcover_main").css("background-position","0 "+newyposition+"px");
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
<div id="suggestions" style="display:none;"><div id="textis"><?php echo _t('_ibdw_eventcover_suggestions');?></div><div id="check" onclick="closetext();"><i class="sys-icon check"></i></div></div>
<div id="alignator">
<div class="exit_alb" onclick="closechange_album();stopmn=0;"><i class="sys-icon remove"></i></div>
<h2 class="chooseboxtitle"><?php echo _t('_ibdw_eventcover_select_image');?></h2>
<div id="albumimgboxPC">
<?php
$countimage=0;
while($foto = mysqli_fetch_array($runquery_foto)) 
{
 $countimage++;
 echo '<img src="'.$site['url'].'m/photos/get_image/browse/'.$foto['Hash'].'.'.$foto['Ext'].'" id="imgp'.$countimage.'" style="display:none;" alt="" />';
 echo '<div id="sel'.$countimage.'" onclick="change_album(\''.$foto['Hash'].'\','.$eventid.');" class="ibdw_photo_mainphotoC" style="background-color: #fff;background-position: center center;background-repeat: no-repeat;background-image: url(&quot;'.$site['url'].'/modules/ibdw/eventcover/templates/base/images/big-loader.gif&quot;);"></div>';
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

<div id="eventcover_main" onmouseover="if (stopmn==0) displaymenu();" onmouseout="if (stopmn==0) closemenu(0);">
<?php 
$profilethumb = get_member_thumbnail($autoris, 'none', false);
$ProfileNameis=getNickname($autoris);
echo '<div id="avtarea" class="avatararea"><div id="ibox" class="avatarbox">'.$profilethumb.'</div><div id="PiBox" class="Eventbox">';
if ($Displaytitle=="on") echo '<div class="titlecover">'.$eventname.'</div>';
if ($Displayauthor=="on") echo '<div class="enfasi">'._t('_ibdw_eventcover_author').$ProfileNameis.'</div>';
echo '</div></div>';
echo '<input type="hidden" id="posX" value="'.$mainpf['PositionX'].'"><input type="hidden" id="posY" value="'.$defY.'">';
if ($amministratore==1) 
{
 
 echo 
 '<div id="covermainmenu">
   <div id="buttonitems"><button id="mainb" class="bx-btn bx-btn-small bx-btn-ifont" onclick="openmenu();"><i class="sys-icon pencil" id="iconmain"></i>'._t('_ibdw_eventcover_covermainmenu').'</button></div>
   <div id="submenus">';
    if($numphotoscover>0) echo '<div class="menuline" onclick="openchange_album();stopmn=1;closemenu();"><i class="sys-icon picture-o"></i><span>'._t('_ibdw_eventcover_fromalbum').'</span></div>';   
    
    echo '<div class="menuline" onclick="ibdw_cover_frompc('.$id_album_predef.','.$IDmio.','.$eventid.');stopmn=1;closemenu();"><i class="sys-icon upload"></i><span>'._t('_ibdw_eventcover_upload').'</span></div>';
    if ($contarespf>0) echo '<div class="menuline" onclick="moveimagey('.$IDmio.',\''.$mainpf['Hash'].'\',0,document.getElementById(\'posY\').value,'.$eventid.');" id="movemenu"><i class="sys-icon move"></i><span>'._t('_ibdw_eventcover_position').'</span></div>';
    if ($contarespf>0) echo '<div class="menuline" onclick="ibdw_cover_remove('.$IDmio.',&quot;'.$mainpf['Hash'].'&quot;,\''.BX_DOL_URL_ROOT.'\','.$eventid.');" id="removemenu"><i class="sys-icon remove"></i><span>'._t('_ibdw_eventcover_remove').'</span></div>';
 echo '</div></div>';
}                     
echo '<div id="modificaalbums"></div>';
?>
</div>
<?php

if(isset($_POST['ajax']))
{
?>
<script>
$(document).ready(function() 
{ 
 $("#posY").val(0); 
 $("#eventcover_main").css('backgroundPosition', '0 0');
 
 $('#dummy').load(function () {
  moveimagey(<?php echo $IDmio;?>,'<?php echo $mainpf['Hash'];?>',0,document.getElementById("posY").value,<?php echo $eventid;?>);
  openmenu(); 
 });
 
}); 
</script>
<?php
}
?>
