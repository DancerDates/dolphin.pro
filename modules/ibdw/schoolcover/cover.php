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
} ?><?php include BX_DIRECTORY_PATH_MODULES . 'ibdw/schoolcover/config.php';
session_start();
$controllo = $KeyCodeSCH;
$onecript = "244FGt58sa32afssladashf8883Lh4d6647";
$twocript = $_SERVER['HTTP_HOST'];
$trecript = "f33Vtfea445sxSsssa1AW4335FaGadv3dfk3";
$genera = $onecript . $twocript . $trecript;
mysqli_query("SET NAMES 'utf8'");
if (false) echo '<b>' . _t('_ibdw_schoolcore_sicurity') . '</b>';
else {
    if (!$_SESSION['this_url']) $_SESSION['this_url'] = $_SERVER['REQUEST_URI'];
    if ($_SERVER['REQUEST_URI'] != $_SESSION['this_url']) {
        if (!strstr($_SERVER['REQUEST_URI'], "m/school_customize/schoolblock")) $_SESSION['this_url'] = $_SERVER['REQUEST_URI'];
    }
    $pagina = $_SESSION['this_url'];
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
    $crpageaddress = $protocol . "://" . $_SERVER['HTTP_HOST'] . $pagina;
    $prima_occorrenza = strpos($pagina, "m/schools/view/");
    $uriis = substr($pagina, $prima_occorrenza);
    $uriis = rawurldecode(str_replace("m/schools/view/", "", $uriis));
    echo '<input type="hidden" name="pageuriis" value="' . $uriis . '" id="uripage">';
    $qis = "SELECT id, title FROM `modzzz_schools_main` WHERE uri='" . $uriis . "'";
    $mquis = mysqli_query($qis);
    $idd = mysqli_fetch_array($mquis);
    $schoolid = $idd[0];
    $schoolname = $idd[1];
    echo '<input type="hidden" name="pageidschool" value="' . $schoolid . '" id="schoolidis">';
    $getautorid = "SELECT author_id FROM modzzz_schools_main WHERE id=" . $schoolid;
    $rungetauth = mysqli_query($getautorid);
    $fetcha = mysqli_fetch_array($rungetauth);
    $autoris = $fetcha[0];
    if (isAdmin() or $accountid == $autoris) $amministratore = 1;
    else $amministratore = 0;
    $IDmio = $_COOKIE['memberID'];
    $idprofile = getID($_REQUEST['ID']);
    if ($IDmio == $idprofile) {
        $nomealbum = "schoolcover";
        $nomealbumtrue = addslashes($coveralbumname);
        $estrazione = "SELECT ID,ObjCount,Uri FROM sys_albums WHERE Owner='$IDmio' AND Uri='$nomealbum'";
        $runquery = mysqli_query($estrazione);
        $verificanumero = mysqli_num_rows($runquery);
        if ($verificanumero == 0) {
            $inserimento = "INSERT INTO sys_albums (Caption,Uri,Location,Type,Owner,Status,ObjCount,LastObjId,AllowAlbumView,Date) VALUES ('" . $nomealbumtrue . "','" . $nomealbum . "','Undefined','bx_photos','" . $IDmio . "','Active','0','0','3',UNIX_TIMESTAMP( ))";
            $runquery_exe = mysqli_query($inserimento);
        }
    } ?>
<link href="<?php echo BX_DOL_URL_ROOT . 'modules/ibdw/schoolcover/templates/base/css/style.css'; ?>" rel="stylesheet" type="text/css" />
<div id="loading_div_Start"><img src="<?php echo BX_DOL_URL_ROOT . 'modules/ibdw/schoolcover/templates/base/images/big-loader.gif'; ?>"></div>
<div id="pfblockconteiner"> 
<?php include ('script.php');
    include ('core.php');
    echo '</div><div id="loading_div"><img src="' . BX_DOL_URL_ROOT . 'modules/ibdw/schoolcover/templates/base/images/big-loader.gif"></div><div class="clear_both"></div>';
} ?>