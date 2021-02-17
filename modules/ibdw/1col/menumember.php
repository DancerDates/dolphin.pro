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
} ?><?php include BX_DIRECTORY_PATH_MODULES . 'ibdw/1col/myconfig.php'; ?>
<script>
var tf;
function aggiornajx() {
  $.ajax({
   url: 'modules/ibdw/1col/query_onecol.php',
   cache: false,
    success: function(data) {
     $('#maincont1col').html(data);
    }
});
tf=setTimeout('aggiornajx()',<?php echo $timereload; ?> );
}
tf=setTimeout('aggiornajx()',<?php echo $timereload; ?> );
function stopaggiornamento()
{
clearTimeout(tf);
}
</script>

<?php echo '<link href="modules/ibdw/1col/templates/base/css/style.css" rel="stylesheet" type="text/css" />';
echo '<div id="ajaxload" style="display:none;"> </div>';
echo '<div id="richieste_ajx">';
mysqli_query("SET NAMES 'utf8'");
$controllopass = "SELECT * FROM one_code LIMIT 0,1";

$risultato = mysqli_query($controllopass);// or die('1col-menumember.php: '.mysqli_error());
$estrazione = mysqli_fetch_assoc($risultato);
$controllo = $estrazione['code'];
$onecript = "dsjfspfbdisbfs82342432pbdfuibfuidsbfur7384476353453432dasddsfsfsds";
$twocript = $_SERVER['HTTP_HOST'];
$trecript = "dsfsfd7875474g3yuewyrfoggogtoreyut7834733429362dd6sfisgfffegregege803";
$genera = $onecript . $twocript . $trecript;
if (false) echo '<b>' . _t('_ibdw_1col_sicurity') . '</b>';
else {
    $ottieniID = (int)$_COOKIE['memberID'];
    $valoriutente = getProfileInfo($ottieniID);
    $MiaCitta = $valoriutente['City'];
    $MioStato = $valoriutente['Status'];
    $visitato = (int)$valoriutente['Views'];
    $MiaEmail = $valoriutente['Email'];
    $NomeUtente = getNickname($ottieniID);
    $LinkUtente = getUsername($ottieniID);
    echo '<div><div class="menuelement1"><div class="infoutentecont"><div class="infoutente1">';
    echo '<div class="mioavatar1col">' . get_member_thumbnail($ottieniID, 'none', false) . '</div>';
    echo '<div id="infomembercont">';
    echo '<div class="spacer1"><a href="' . $LinkUtente . '">' . $NomeUtente . '</a></div><div class="spacer2"><a href="pedit.php?ID=' . $ottieniID . '">' . _t('_ibdw_1col_settings') . '</a></div>';
    if ($scity == 'ON') echo '<div class="spacer3">' . $MiaCitta . '</div>';
    if ($status == 'ON') {
        echo '<div class="spacer4">' . _t('_ibdw_1col_status') . ' <b>' . $sMessaggioStato . '</b> '; ?>
 (<a onclick="javascript:window.open('explanation.php?explain=<?php echo $MioStato; ?>','','width=660,height=200,menubar=no,status=no,resizable=no,scrollbars=yes,toolbar=no, location=no' );" href="javascript:void(0);"><?php echo _t('_ibdw_1col_expl'); ?></a>, <a href="change_status.php"><?php echo _t('_ibdw_1col_suspend'); ?></a>)</div>
 <?php
    }
    if ($shemaila == "ON") echo '<div class="spacer5">' . _t('_ibdw_1col_email') . ' ' . $MiaEmail . '</div>';
    echo '</div></div></div><div style="clear:both;"></div>';
    echo '<div id="maincont1col">';
    include BX_DIRECTORY_PATH_MODULES . 'ibdw/1col/query_onecol.php';
    echo '</div>';
}
echo '</div>'; ?>
<div class="clear_both"></div>