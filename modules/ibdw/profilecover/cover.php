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
} ?><?php include BX_DIRECTORY_PATH_MODULES . 'ibdw/profilecover/config.php';
$controllo = $KeyCode;
$onecript = "yh432adddhjasladash246asdjsddll46";
$twocript = $_SERVER['HTTP_HOST'];
$trecript = "klaAWER455SGTUYsdasd3k3vxx3kl3jssa";
$genera = $onecript . $twocript . $trecript;
if (false) echo '<b>' . _t('_ibdw_profilecore_sicurity') . '</b>';
else {
    mysqli_query("SET NAMES 'utf8'");
    $photodeluxeinstalled = 0;
    $verphotodeluxe = "SELECT uri FROM sys_modules WHERE uri = 'photo_deluxe'";
    $runverphotodeluxe = mysqli_query($verphotodeluxe);
    $ifonephotodeluxe = mysqli_num_rows($runverphotodeluxe);
    if ($ifonephotodeluxe != 0) {
        $photodeluxeinstalled = 1;
    }
    $IDmio = $_COOKIE['memberID'];
    $idprofile = getID($_REQUEST['ID']);
    $profilevector = getProfileInfo($idprofile);
    function getCoverAlbumUri($idprofile) {
        $usernameis = getUsername($idprofile);
        $defaultcoveralbumname = "SELECT `VALUE` from sys_options WHERE Name='bx_photos_profile_cover_album_name'";
        $runthisq = mysqli_query($defaultcoveralbumname);
        $getifexists = mysqli_num_rows($runthisq);
        if ($getifexists > 0) {
            $acnameis = mysqli_fetch_assoc($runthisq);
            $can = $acnameis['VALUE'];
            $coveralbumname = str_replace("{nickname}", $usernameis, $can);
            $defaulturiis = "SELECT Uri FROM sys_albums WHERE Caption='" . addslashes($coveralbumname) . "'";
            $runthisq = mysqli_query($defaulturiis);
            $getifexists = mysqli_num_rows($runthisq);
            if ($runthisq > 0) {
                $cauriget = mysqli_fetch_assoc($runthisq);
                $coveralbumUri = $cauriget['Uri'];
            }
            $coveralbumurl = BX_DOL_URL_ROOT . 'm/photos/albums/my/add_objects/' . $coveralbumUri . '/owner/' . $usernameis;
        }
        return $coveralbumUri;
    }
    if ($IDmio == $idprofile) {
        if ($usedefaultCover == "on") {
            $nomealbum = getCoverAlbumUri($idprofile);
            $nomealbumtrue = addslashes($nomealbum);
            $estrazione = "SELECT ID,ObjCount,Uri FROM sys_albums WHERE Owner='$IDmio' AND Uri='$nomealbum'";
        } else {
            $nomealbum = "profilecover";
            $nomealbumtrue = addslashes($coveralbumname);
            $estrazione = "SELECT ID,ObjCount,Uri FROM sys_albums WHERE Owner='$IDmio' AND Uri='$nomealbum'";
        }
        $runquery = mysqli_query($estrazione);
        $verificanumero = mysqli_num_rows($runquery);
        if ($verificanumero == 0) {
            $instquery = "INSERT INTO sys_albums (Caption,Uri,Location,Type,Owner,Status,ObjCount,LastObjId,AllowAlbumView,Date) VALUES ('" . $nomealbumtrue . "','" . $nomealbum . "','Undefined','bx_photos','" . $IDmio . "','Active','0','0','3',UNIX_TIMESTAMP( ))";
            $runquery_exe = mysqli_query($instquery);
        }
    } ?>
<link href="<?php echo BX_DOL_URL_ROOT . 'modules/ibdw/profilecover/templates/base/css/style.css'; ?>" rel="stylesheet" type="text/css" />
<div id="loading_div_Start"><img src="<?php echo BX_DOL_URL_ROOT . 'modules/ibdw/profilecover/templates/base/images/big-loader.gif'; ?>"></div>
<div id="pfblockconteiner"> 
<?php include ('script.php');
    include ('core.php');
    echo '</div><div id="loading_div"><img src="' . BX_DOL_URL_ROOT . 'modules/ibdw/profilecover/templates/base/images/big-loader.gif"></div><div class="clear_both"></div>';
} ?>