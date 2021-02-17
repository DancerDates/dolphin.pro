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
} ?><script type="text/javascript" src="js/jmini.js" /></script>
<script>
    $jqspywall = jQuery.noConflict();
</script>
<?php require_once ('../../../inc/header.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES . 'ibdw/1col/myconfig.php';
$userid = (int)$_COOKIE['memberID'];
if (!isAdmin()) {
    exit;
}
mysqli_query("SET NAMES 'utf8'");
$controllorilevanza = "SELECT * FROM `one_code` LIMIT 0 , 30";
$resultax = mysqli_query($controllorilevanza);
$conteggio = mysqli_num_rows($resultax);
if ($conteggio == 0) 
{
    $inserimentoprimorecord = "INSERT INTO one_code (id,code)     VALUES ('1','0')";
    $resultrec = mysqli_query($inserimentoprimorecord);
}
$controlloattivazione = "SELECT * FROM `one_code` WHERE id =1 LIMIT 0 , 30";
$resultaa = mysqli_query($controlloattivazione);
$rowa = mysqli_fetch_assoc($resultaa);
$confronto = $rowa['code'];
$onecript = "dsjfspfbdisbfs82342432pbdfuibfuidsbfur7384476353453432dasddsfsfsds";
$twocript = $_SERVER['HTTP_HOST'];
$trecript = "dsfsfd7875474g3yuewyrfoggogtoreyut7834733429362dd6sfisgfffegregege803";
$genera = $onecript . $twocript . $trecript; ?>
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
height:645px;
margin:30px auto auto;
padding:20px;
width:900px;
}
#form_invio {
border:1px solid #FFFFFF;
float:left;
font-size:15px;
line-height:34px;
margin-left:187px;
margin-top:44px;
padding:20px;
width:500px;
}
#form_conferma {
border:1px solid #666666;
float:left;
font-size:16px;
height:189px;
line-height:45px;
margin-left:187px;
margin-top:14px;
padding:20px;
width:500px;
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
.classeform1 {
color:#999999;
font-size:14px;
height:36px;
width:294px;
}
.classeform2 {
color:#999999;
font-size:11px;
height:36px;
width:358px;
}
.subclass {
background:none repeat scroll 0 0 #000000;
border:medium none;
color:#FFFFFF;
font-size:11px;
margin:13px 13px 0;
padding:7px;
text-transform:uppercase;
}
#return {
border:1px solid #FFFFFF;
color:#FFFFFF;
font-size:15px;
height:31px;
line-height:27px;
margin-left:93px;
margin-top:48px;
width:315px;
}
#return:hover {
background:none repeat scroll 0 0 #333333;
}
#step1 {
font-size:24px;
}
#step2 {
font-size:24px;}

</style>

<html>
<body>
  <div id="pagina">
  <div id="introright">
    <span class="title"><?php echo _t("_ibdw_1col_activaintro"); ?></span>   <br/>
    <span class="dett_activ"><?php echo _t("_ibdw_1col_spycodereq"); ?></span>
  </div>
    <?php if (md5($genera) === $confronto) {
    echo '<div id="notifica">' . _t("_ibdw_1col_yosattiva") . ' </div> <div id="introswich">      <a href="delete.php">' . _t("_ibdw_1col_sostituiscibott") . '</a></div> </div></div></body> </html>';
    exit;
} ?>
    
    
    <div id="form_invio">
    <div id="step1"> Step 1 </div>
    <form action="requirex.php" method="post">
    <span class="dett_activ"><?php echo _t("_ibdw_1col_introattivazione"); ?></span>  <br/><br/>
    <input type="text" name="paypal" value="Insert your email address (Paypal/Echeck)" size="37" id="reset" onclick="resetta();" class="classeform1"> <br/> 
    <input type="submit" value="Send Request" class="subclass">
    
    <script>
    function resetta(){
    $jqspywall("#reset").val("");
    $jqspywall(".classeform1").css("color","black");
    }
    </script>
    <br/>
    </form>
    </div>
    
    <div id="form_conferma">
    <div id="step2"> Step 2 </div>
    <form action ="redirect.php" method="post">
    CODE ACTIVATION<br/>
    <span class="dett_activ"><?php echo _t("_ibdw_1col_entrecode"); ?></span>
    <input type="text" size="62" name="code" value="Insert the activation code you receive via email (ACTIVATION CODE)" id="resettwo" onclick="resettatwo();" class="classeform2"><br/>
    <input type="submit" value="ACTIVATE" class="subclass">
    </form>
    <script>
    function resettatwo(){
    $jqspywall("#resettwo").val("");
    $jqspywall(".classeform2").css("color","black");
    
    }
    </script>
    <div id="return"><a href="../../../<?php echo $admin_dir; ?>"><?php echo _t("_ibdw_1col_backadmin"); ?></a></div>  <br/>   <br/>
    </div>
  </div>
</body>
</html>