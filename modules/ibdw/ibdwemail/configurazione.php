<?
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysqli_query("SET NAMES 'utf8'");
  $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'like_action'";
  $verifica_exe = mysqli_query($verifica);
  $riga_exe = mysqli_fetch_assoc($verifica_exe);
  $istruzione_1 = $riga_exe['active'];
  
  $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'share'";
  $verifica_exe = mysqli_query($verifica);
  $riga_exe = mysqli_fetch_assoc($verifica_exe);
  $istruzione_2 = $riga_exe['active']; 
  
  $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'messaggiowall'";
  $verifica_exe = mysqli_query($verifica);
  $riga_exe = mysqli_fetch_assoc($verifica_exe);
  $istruzione_3 = $riga_exe['active']; 
  
  $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'commento'";
  $verifica_exe = mysqli_query($verifica);
  $riga_exe = mysqli_fetch_assoc($verifica_exe);
  $istruzione_4 = $riga_exe['active']; 
  
  $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'richiesta_amicizia'";
  $verifica_exe = mysqli_query($verifica);
  $riga_exe = mysqli_fetch_assoc($verifica_exe);
  $istruzione_5 = $riga_exe['active'];

?>
<style>
.semicolumn
{
 width:50%;
 float:left;
}
a
{
color:#000000;
text-decoration:none;
}
a:hover 
{
color:#FFFFFF;
text-decoration:none;
}
body
{
background:none repeat scroll 0 0 #334962;
font-family:Verdana;
font-size:11px;
margin:0;
text-align:center; 
}
#pagina {
    background: url("templates/logoconfig.jpg") no-repeat scroll 35px 22px #283B51;
    border: 7px solid #FFFFFF;
    color: #FFFFFF;
    height: 340px;
    margin: 20px auto;
    padding: 20px;
    width: 900px;
}
#form_invio
{
float:left;
font-size:15px;
line-height:34px;
margin-left:201px;
margin-top:44px;
width:500px;
}
#form_conferma
{
float:left;
font-size:16px;
line-height:45px;
margin-left:225px;
margin-top:25px;
width:429px;
}
.title
{
font-size:27px;
text-transform:uppercase;
}
.dett_activ
{
color:#FFFFFF;
font-size:10px;
line-height:15px;
}
#introright
{
float:right;
text-align:right;
}
#notifica
{
color:#FFFFFF;
font-size:18px;
margin:135px;
}
#boxgeneraleconfigurazione
{
float:left;
margin-top:110px;
padding:5px;
text-align:left;
}
.introtitle
{
font-size:14px;
font-weight:bold;
line-height:23px;
}
.introdesc
{
color:Yellow;
font-size:11px;
font-style:italic;
line-height:13px;
}
#contentbox {
    background-color: #4682B4;
    border: 2px solid #FFFFFF;
    font-size: 10px;
    height: 155px;
    line-height: 11px;
    margin: 10px;
    padding: 10px;
    width: 845px;
}
#return
{
border:1px solid #FFFFFF;
color:#FFFFFF;
float:right;
font-size:15px;
height:31px;
line-height:27px;
margin-right:15px;
margin-top:-105px;
padding:0 13px;
}
#return:hover
{
background:none repeat scroll 0 0 #999999;
}
#return a
{
color:#FFF;
}
.medios1
{
 float:left;
 width:50%;
 margin-top:4px;
}
.medios2 {
float:left;
margin-bottom:4px;
margin-top:0;
width:50%;
}
.unterzo {
float:left;
margin-top:4px;
width:30%;
}
.unquarto {
float:left;
padding-bottom:6px;
width:25%;
}
.unquarto2 {
float:left;
padding-bottom:6px;
width:23%;
}
.unquarto3 {
float:left;
padding-bottom:6px;
width:27%;
}
.unquarto4 {
float:left;
margin-top:4px;
padding-bottom:6px;
width:30%;
}
.unquarto5 {
float:left;
padding-bottom:6px;
width:30%;
}
.unsesto {
float:left;
margin-top:4px;
width:23%;
}
.dueterzi {
float:left;
margin-bottom:6px;
width:70%;
}
.contentcon {
border-bottom:1px solid #FFFFFF;
float:left;
margin:3px 2px 2px;
width:100%;
}
.spazio
{
float:left;
height:20px;
width:100%;
}
</style>
<html>
<body>
<div id="pagina">
 <div id="boxgeneraleconfigurazione">
 <div id="return"><a href="../../../<?php echo $admin_dir;?>">Return to main administration</a></div>
 <div class="semicolumn">
  <div id="contentbox">
   <span class="introtitle">Settings</span><br/>
   <span class="introdesc">Choose the notifications you want enable for the IBDW modules - For each language you can customize the email text editing the language files you can see into the folder yoursite/modules/ibdw/ibdwemail/install/langs/</span>
   <div class="spazio"></div>
	<form action="<?php echo BX_DOL_URL_MODULES;?>ibdw/ibdwemail/updateconfig.php" method="POST">
	 <div class="contentcon">
	  <div class="unsesto"><b>"I Like"</b> notifications</div><div class="unquarto"><input type="radio" name="like" value="1" <?php if($istruzione_1=='1') {echo 'checked';}?> />ON <input type="radio" name="like" value="0" <?php if($istruzione_1=='0') {echo 'checked';}?>/>OFF</div>
	  <div class="unsesto"><b>"Share"</b>  notifications</div><div class="unquarto"><input type="radio" name="share" value="1" <?php if($istruzione_2=='1') {echo 'checked';}?>/>ON <input type="radio" name="share" value="0" <?php if($istruzione_2=='0') {echo 'checked';}?>/>OFF</div>
     </div>
	 <div class="contentcon">
	  <div class="unsesto"><b>"Comments"</b>  notifications</div><div class="unquarto"><input type="radio" name="comment" value="1" <?php if($istruzione_3=='1') {echo 'checked';}?>/>ON <input type="radio" name="comment" value="0" <?php if($istruzione_3=='0') {echo 'checked';}?>/>OFF</div>
	  <div class="unsesto"><b>"Personal messages"</b>  notifications</div><div class="unquarto"><input type="radio" name="wall" value="1" <?php if($istruzione_4=='1') {echo 'checked';}?>/>ON <input type="radio" name="wall" value="0" <?php if($istruzione_4=='0') {echo 'checked';}?>/>OFF</div>
	 </div>
	 <div class="contentcon">
	  <div class="unsesto"><b>"Friend requests"</b>  notifications</div><div class="unquarto"><input type="radio" name="friend" value="1" <?php if($istruzione_5=='1') {echo 'checked';}?>/>ON <input type="radio" name="friend" value="0" <?php if($istruzione_5=='0') {echo 'checked';}?>/>OFF</div>
	 </div>
  </div> 
 </div>
 </div>
 <input type="submit" value="Save"></form>
</div>
</body>
</html>