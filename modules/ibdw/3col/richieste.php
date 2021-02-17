<?php include BX_DIRECTORY_PATH_MODULES . 'ibdw/3col/config.php'; ?>
<script>
function aggiornaajax()
{
 $.ajax({url: '<?php echo BX_DOL_URL_MODULES; ?>ibdw/3col/query_request.php',cache: false,success: function(data) {$('#richieste_ajax').html(data);}});
}
setInterval('aggiornaajax()',<?php echo $timereload; ?>);
</script>
<?php echo '<link href="modules/ibdw/3col/templates/base/css/style.css" rel="stylesheet" type="text/css" />';
echo '<div id="richieste_ajax">';
mysqli_query("SET NAMES 'utf8'");
$controllopass = "SELECT * FROM third_code LIMIT 0,1";
$risultato = mysqli_query($controllopass);// or die(mysql_error());
$estrazione = mysqli_fetch_assoc($risultato);
$controllo = $estrazione['code'];
$onecript = "ddjdlsbfudisf02131bdjsfpbdsfbdrrrew2212343425438ggds";
$twocript = $_SERVER['HTTP_HOST'];
$trecript = "df8dd834892382dhdfgghfsfhduwwew23232fdobfdgdfogburiotruotth784548457";
$genera = $onecript . $twocript . $trecript;
if (false) echo '<b>' . _t('_ibdw_thirdcolumn_sicurity') . '</b></div>';
else {
    $arraydegliesclusi = "";
    include 'modules/ibdw/3col/query_request.php';
    echo '</div>';
    if ($contaamici > 0) {
        $queryverifica = "SELECT uri FROM sys_modules WHERE uri='events'";
        $queryverifica_exe = mysql_query($queryverifica);
        $numero_ver_event = mysql_num_rows($queryverifica_exe);
        if ($showevents == "ON" AND $numero_ver_event != 0) {
            include 'modules/ibdw/3col/event_section.php';
        }
        if ($showbirthdate == "ON") {
            $condizione1 = "( MONTH(DateOfBirth)=MONTH(CURDATE()) AND DAY(DateOfBirth)>=DAY(CURDATE()) )";
            $condizione2 = "( MONTH(DateOfBirth)>MONTH(CURDATE()) AND MONTH(DateOfBirth)< (MONTH(CURDATE( ))+2) )";
            $querycompleanni = "SELECT ID, NickName, DateOfBirth FROM `Profiles` WHERE ((" . $condizione1 . " OR " . $condizione2 . ") AND (`ID` IN (";
            for ($sfoglia2 = 0;$sfoglia2 < $contaamici;$sfoglia2++) {
                if ($sfoglia2 < ($contaamici - 1)) $querycompleanni = $querycompleanni . $amico[$sfoglia2] . ", ";
                else $querycompleanni = $querycompleanni . $amico[$sfoglia2];
            }
            $querycompleanni = $querycompleanni . "))) ORDER BY MONTH(DateOfBirth),DAY(DateOfBirth) ASC";
            $resultcompleanni = mysqli_query($querycompleanni);
            $contacompleanni = mysqli_num_rows($resultcompleanni);
            if ($contacompleanni > 0) {
                echo '<div class="rhegionmenuelement3"><div class="rigamenumodificadx"><div class="titlecompleannirec"><i class="sys-icon gift" alt=""></i>' . _t('_ibdw_thirdcolumn_birthdays') . '</div></div>';
                for ($acca = 0;$acca < $contacompleanni;$acca++) {
                    $compleanno = mysqli_fetch_array($resultcompleanni);
                    $data1 = mktime(0, 0, 0, (int)date("m", strtotime($compleanno[2])), (int)date("d", strtotime($compleanno[2])), 0);
                    $data2 = mktime(0, 0, 0, (int)date("m", time()), (int)date("d", time()), 0);
                    $diff = (int)(($data1 - $data2) / (3600 * 24));
                    if ($diff < $maxdaybirthdays) {
                        $nomecompleanno = getNickname($compleanno[0]);
                        echo '<div class="rigamenuev"><a href="' . getProfileLink($compleanno[0]) . '">' . $nomecompleanno . '</a>';
                        if ($diff == - 1) echo '<div class="eventino">(' . _t('_ibdw_thirdcolumn_yesterdayname') . ')</div></div>';
                        elseif ($diff == 0) echo '<div class="eventino">(' . _t('_ibdw_thirdcolumn_todayname') . ')</div></div>';
                        elseif ($diff == 1) echo '<div class="eventino">(' . _t('_ibdw_thirdcolumn_tomorrowname') . ')</div></div>';
                        else {
                            if ($dateFormatC == "uni") $infoaggiuntive = date("m/d", strtotime($compleanno[2]));
                            else if ($dateFormatC == "eur") $infoaggiuntive = date("d/m", strtotime($compleanno[2]));
                            else if ($dateFormatC == "jpn") $infoaggiuntive = date("m/d", strtotime($compleanno[2]));
                            echo '<div class="eventino">(' . _t('_ibdw_thirdcolumn_bename') . ' ' . $diff . ' ' . _t('_ibdw_thirdcolumn_dayname') . ' - ' . $infoaggiuntive . ')</div></div>';
                        }
                    }
                }
                echo '</div>';
            }
        }
        if ($showminispy == "ON" AND getID($_REQUEST['ID']) == (int)$_COOKIE['memberID']) {
            $chimispia = "SELECT DISTINCT(sender_id) FROM bx_spy_data WHERE ( ((now()-date)<" . $timeminispy . ") AND (lang_key='_bx_spy_profile_has_viewed') AND (recipient_id=" . $userid . ")";
            $chimispia = $chimispia . ") ORDER BY date DESC LIMIT 15";
            $resultspia = mysqli_query($chimispia);
            $contaspioni = mysqli_num_rows($resultspia);
            if ($contaspioni > 0) {
                echo '<div class="rhegionmenuelement3"><div class="rigamenumodificadx"><div class="titlespionerec">' . _t('_ibdw_thirdcolumn_spyprofile') . '</div></div>';
                for ($zeta = 0;$zeta < $contaspioni;$zeta++) {
                    $spione = mysqli_fetch_array($resultspia);
                    $infospione = getProfileInfo($spione[0]);
                    $icona = get_member_icon($spione[0], 'none', false);
                    $LinkUtente = getProfileLink($spione[0]);
                    $NomeSpione = getNickname($spione[0]);
                    echo '<div class="rigamenuspy">';
                    echo '<div class="iconsuggested">' . $icona . '</div>';
                    echo '<div class="mioutentesmall2"><a href="' . getProfileLink($spione[0]) . '">' . $NomeSpione . '</a></div></div>';
                }
                echo '</div>';
            }
        }
    }
    if ($showmoreinfo == "ON" AND getID($_REQUEST['ID']) == (int)$_COOKIE['memberID']) {
        echo '<div class="rhegionmenuelement3"><div class="rigamenumodificadx"><div class="titlealtro"><i class="sys-icon asterisk" alt=""></i>' . _t('_ibdw_thirdcolumn_more') . '</div></div><div class="rigamenu3">';
        if ($defaultinviter == "ON") echo '<a onclick="return launchTellFriend();" href="javascript:void(0)" class="titleinvita"><i class="sys-icon envelope-alt" alt=""></i>';
        else echo $linktoinviter;
        echo _t('_ibdw_thirdcolumn_friend') . '</a></div>';
        if ($contasal > 0) echo '<div class="rigamenu3"><a href="' . BX_DOL_URL_ROOT . 'mail.php?mode=inbox" class="titlesaluti"><i class="sys-icon envelope" alt=""></i>' . _t('_ibdw_thirdcolumn_greetings') . '</a><div class="bubble3col">' . $contasal . '</div></div>';
        echo '</div>';
    }
} ?>
<div class="clear_both"></div>