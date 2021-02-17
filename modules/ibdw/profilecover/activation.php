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
} ?><?php require_once ('../../../inc/header.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES . 'ibdw/profilecover/config.php';
if (!isAdmin()) {
    exit;
}
mysqli_query("SET NAMES 'utf8'");
$retriveidevowall = "SELECT ID FROM sys_options_cats WHERE name='Profile Cover'";
$risultatoid = mysqli_query($retriveidevowall);
$idmodulo = mysqli_fetch_assoc($risultatoid);
$querydiconfigurazione = "SELECT * FROM sys_options WHERE name='KeyCode' and kateg=" . $idmodulo['ID'];
$risultato = mysqli_query($querydiconfigurazione);
$riga = mysqli_fetch_assoc($risultato);
$confronto = $riga['VALUE'];
$onecript = "yh432adddhjasladash246asdjsddll46";
$twocript = $_SERVER['HTTP_HOST'];
$trecript = "klaAWER455SGTUYsdasd3k3vxx3kl3jssa";
$genera = $onecript . $twocript . $trecript;
$veremail = "SELECT * FROM profilecover_code_reminder";
$getemail = mysqli_query($veremail);
$ema = mysqli_fetch_assoc($getemail);
if ($ema['addressr'] != NULL) {
    $predefinito = $ema['addressr'];
} else {
    $predefinito = _t("_ibdw_profilecover_eml_t_add");
}
echo '<html><head><title>PROFILE COVER - Activation\'s procedure</title><link href="' . BX_DOL_URL_MODULES . 'ibdw/profilecover/templates/uni/css/adminprofilecover.css" rel="stylesheet" type="text/css" /><script language="javascript" type="text/javascript" src="' . BX_DOL_URL_PLUGINS . 'jquery/jquery.js"></script></head>'; ?>
<body>
 <div id="pagina">
  <div id="introright">
   <span class="title"><?php echo _t("_ibdw_profilecover_activaintro"); ?></span><br>
   <span class="dett_activ"><?php echo _t("_ibdw_profilecover_spycodereq"); ?></span>
  </div>
  <?php if (md5($genera) === $confronto) {
    echo '<div id="notifica">' . _t("_ibdw_profilecover_yosattiva") . '</div><div id="tutto"><div id="return2" class="subclass2"><a href="' . BX_DOL_URL_ROOT . 'modules/?r=profilecover/administration/">' . _t("_ibdw_profilecover_unsure") . '</a></div></div></div></body></html>';
    exit;
} ?>
  <div id="form_invio">
   <div id="step1"><?php echo _t("_ibdw_profilecover_1step"); ?></div>
   <div id="descriptionatt"><div class="dett_activ"><?php echo _t("_ibdw_profilecover_introattivazione"); ?></div></div>
    <form class="email_form" action="requirex.php" method="post">
    <input type="text" name="paypal" value='<?php echo $predefinito; ?>' size="37" id="reset" onclick="resetta();" class="classeform1"><br/> 
    <input type="submit" value='<?php echo _t("_ibdw_profilecover_send_rqs"); ?>' class="subclass"> 
    <script>
    function resetta(){
    $("#reset").val("");
    $(".classeform1").css("color","black");
    }
    </script>
    <br/>
    </form>
   </div>
  </div>
  <div id="footer">Powered by: <a class="ibdw" href="http://www.ilbellodelweb.it">IlBelloDelWEB.it</a></div>
</body>
</html>
