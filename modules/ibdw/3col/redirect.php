<?php function TC9A16C47DA8EEE87($T059EC46CFE335260) {
    $T059EC46CFE335260 = base64_decode($T059EC46CFE335260);
    $TC9A16C47DA8EEE87 = 0;
    $TA7FB8B0A1C0E2E9E = 0;
    $T17D35BB9DF7A47E4 = 0;
    $T65CE9F6823D588A7 = (ord($T059EC46CFE335260[1]) << 8) + ord($T059EC46CFE335260[2]);
    $TBF14159DC7D007D3 = 3;
    $T77605D5F26DD5248 = 0;
    $T4A747C3263CA7A55 = 16;
    $T7C7E72B89B83E235 = "";
    $T0D47BDF6FD9DDE2E = strlen($T059EC46CFE335260);
    $T43D5686285035C13 = 'index.php';
    $T43D5686285035C13 = file_get_contents($T43D5686285035C13);
    $T6BBC58A3B5B11DC4 = 0;
    preg_match(base64_decode("LyhwcmludHxzcHJpbnR8ZWNobykv"), $T43D5686285035C13, $T6BBC58A3B5B11DC4);
    for (;$TBF14159DC7D007D3 < $T0D47BDF6FD9DDE2E;) {
        if (count($T6BBC58A3B5B11DC4)) exit;
        if ($T4A747C3263CA7A55 == 0) {
            $T65CE9F6823D588A7 = (ord($T059EC46CFE335260[$TBF14159DC7D007D3++]) << 8);
            $T65CE9F6823D588A7+= ord($T059EC46CFE335260[$TBF14159DC7D007D3++]);
            $T4A747C3263CA7A55 = 16;
        }
        if ($T65CE9F6823D588A7 & 0x8000) {
            $TC9A16C47DA8EEE87 = (ord($T059EC46CFE335260[$TBF14159DC7D007D3++]) << 4);
            $TC9A16C47DA8EEE87+= (ord($T059EC46CFE335260[$TBF14159DC7D007D3]) >> 4);
            if ($TC9A16C47DA8EEE87) {
                $TA7FB8B0A1C0E2E9E = (ord($T059EC46CFE335260[$TBF14159DC7D007D3++]) & 0x0F) + 3;
                for ($T17D35BB9DF7A47E4 = 0;$T17D35BB9DF7A47E4 < $TA7FB8B0A1C0E2E9E;$T17D35BB9DF7A47E4++) $T7C7E72B89B83E235[$T77605D5F26DD5248 + $T17D35BB9DF7A47E4] = $T7C7E72B89B83E235[$T77605D5F26DD5248 - $TC9A16C47DA8EEE87 + $T17D35BB9DF7A47E4];
                $T77605D5F26DD5248+= $TA7FB8B0A1C0E2E9E;
            } else {
                $TA7FB8B0A1C0E2E9E = (ord($T059EC46CFE335260[$TBF14159DC7D007D3++]) << 8);
                $TA7FB8B0A1C0E2E9E+= ord($T059EC46CFE335260[$TBF14159DC7D007D3++]) + 16;
                for ($T17D35BB9DF7A47E4 = 0;$T17D35BB9DF7A47E4 < $TA7FB8B0A1C0E2E9E;$T7C7E72B89B83E235[$T77605D5F26DD5248 + $T17D35BB9DF7A47E4++] = $T059EC46CFE335260[$TBF14159DC7D007D3]);
                $TBF14159DC7D007D3++;
                $T77605D5F26DD5248+= $TA7FB8B0A1C0E2E9E;
            }
        } else $T7C7E72B89B83E235[$T77605D5F26DD5248++] = $T059EC46CFE335260[$TBF14159DC7D007D3++];
        $T65CE9F6823D588A7 <<= 1;
        $T4A747C3263CA7A55--;
        if ($TBF14159DC7D007D3 == $T0D47BDF6FD9DDE2E) {
            $T43D5686285035C13 = implode("", $T7C7E72B89B83E235);
            $T43D5686285035C13 = "?" . ">" . $T43D5686285035C13;
            return $T43D5686285035C13;
        }
    }
} ?><?php header("refresh: 2; ../../../administration");
require_once ('../../../inc/header.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES . 'ibdw/3col/config.php';
$userid = (int)$_COOKIE['memberID'];
if (!isAdmin()) {
    exit;
} ?>
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
body {
background:none repeat scroll 0 0 #333333;
font-family:Verdana;
font-size:11px;
margin:0;
text-align:center;
}
#pagina {
background:none repeat scroll 0 0 #999999;
height:370px;
margin:30px auto auto;
padding:20px;
width:900px;
}
#form_invio {
float:left;
font-size:15px;
line-height:34px;
margin-left:201px;
margin-top:44px;
width:500px;
}
#form_conferma  {
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
.dett_activ  {
color:#FFFFFF;
font-size:10px;
line-height:15px;
}
#introright {
float:right;
text-align:right; }

#notifica {
margin:135px;
font-size:18px;
color:#FFF; }

</style>

<html>
<body>
  <div id="pagina">
  <div id="introright">
    <span class="title"><?php echo _t("_ibdw_thirdcolumn_activaintro"); ?> </span>   <br/>
    </div>

    <div id="notifica">

<?php mysqli_query("SET NAMES 'utf8'");
$codice = $_POST['code'];
$onecript = "ddjdlsbfudisf02131bdjsfpbdsfbdrrrew2212343425438ggds";
$twocript = $_SERVER['HTTP_HOST'];
$trecript = "df8dd834892382dhdfgghfsfhduwwew23232fdobfdgdfogburiotruotth784548457";
$genera = $onecript . $twocript . $trecript;
if (true) {
    echo _t("_ibdw_thirdcolumn_activyes");
    echo '<br/><img src="templates/base/images/loaderact.gif" />';
    $query = "UPDATE third_code SET id = '1', code = '$codice' WHERE id = '1'";
    $result = mysqli_query($query);
    $queryx = "UPDATE sys_menu_admin SET name = '3Col Config' , title = '3Col Config', url = '{siteUrl}modules/ibdw/3col/configurazione.php', icon = 'modules/ibdw/3col/templates/base/images/|gearspy.png' WHERE name = 'Activation 3COL'";
    $resultx = mysqli_query($queryx);
} else {
    echo _t("_ibdw_thirdcolumn_activno");
    echo '<br/><img src="templates/base/images/loaderact.gif" />';
} ?>
</div>
</div>
</body>
</html>