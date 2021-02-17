<?
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

//queste due righe successive sembrano ormai inutili
$userid = (int)$_COOKIE['memberID'];

if(!isAdmin()) { exit;}
mysqli_query("SET NAMES 'utf8'");
          $like = $_POST['like'];
          $share = $_POST['share'];
          $comment = $_POST['comment'];
          $wall = $_POST['wall'];
          $friend = $_POST['friend'];
          
          $inserimento = "UPDATE ibdw_emailnotification SET active = '$like' WHERE key_actions = 'like_action'";
		      $resultquery = mysqli_query($inserimento);// or die(mysqli_error());
		      
		      $inserimento = "UPDATE ibdw_emailnotification SET active = '$share' WHERE key_actions = 'share'";
		      $resultquery = mysqli_query($inserimento);// or die(mysqli_error());
		      
		      $inserimento = "UPDATE ibdw_emailnotification SET active = '$comment' WHERE key_actions = 'messaggiowall'";
		      $resultquery = mysqli_query($inserimento);// or die(mysqli_error());
		      
		      $inserimento = "UPDATE ibdw_emailnotification SET active = '$wall' WHERE key_actions = 'commento'";
		      $resultquery = mysqli_query($inserimento);// or die(mysqli_error());
		      
		      $inserimento = "UPDATE ibdw_emailnotification SET active = '$friend' WHERE key_actions = 'richiesta_amicizia'";
		      $resultquery = mysqli_query($inserimento);// or die(mysqli_error());
		      
		      
		      
?>
<style>
body, td, th {
}
a {
color:#000000;
text-decoration:none;
}
a:hover {
color:#FFFFFF;
text-decoration:none;
}
body  {
background:none repeat scroll 0 0 #334962;
font-family:Verdana;
font-size:11px;
margin:0;
text-align:center; 
}
#pagina  {
background:url("immagini/spyconfiglogo.png") no-repeat scroll 35px 22px #283B51;
border:7px solid #FFFFFF;
color:#FFFFFF;
height:1082px;
margin:30px auto auto;
padding:20px;
width:900px; }

#form_invio {
float:left;
font-size:15px;
line-height:34px;
margin-left:201px;
margin-top:44px;
width:500px;
}
#form_conferma {
float:left;
font-size:16px;
line-height:45px;
margin-left:225px;
margin-top:25px;
width:429px;
}
.title {
font-size:27px;
text-transform:uppercase;
}
.dett_activ {
color:#FFFFFF;
font-size:10px;
line-height:15px;
}
#introright {
float:right;
text-align:right;
}
#notifica {
color:#FFFFFF;
font-size:18px;
margin:135px;
}
#boxgeneraleconfigurazione  {
float:left;
margin-top:101px;
padding:20px;
text-align:left;
width:854px;
}
.introtitle {
font-size:17px;
font-weight:bold;
}
.introdesc  {
color:#5381E1;
font-size:11px;
font-style:italic;
}
#contentbox {
border:3px double #FFFFFF;
float:left;
line-height:15px;
margin:10px;
padding:10px;
width:365px; }

#return  {
border:1px solid #FFFFFF;
color:#FFFFFF;
font-size:15px;
height:31px;
line-height:27px;
width:315px;
margin-left:285px; }

#return:hover {
background:none repeat scroll 0 0 #999999;}

#return a { color:#FFF; }

</style>

<html>
<body>
  <div id="pagina">

  <div id="notifica">Update completed successfully</div>

    <div id="return"><a href="../../../<?php echo $admin_dir;?>">Return to main administration</a></div>  <br/>   <br/>
    <div id="return"><a href="<?php echo BX_DOL_URL_MODULES;?>ibdw/ibdwemail/configurazione.php">Return to IBDWEmail Configuration</a></div>
    </div>
</body>
</html>
