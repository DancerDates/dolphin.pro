<?php
  require_once( '../../../inc/header.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
  $ewsa = $_COOKIE['memberID']; 
  $hash = $_POST['hashe'];  
  $verifica = "SELECT ID FROM ibdw_profile_cover WHERE Owner = ".$ewsa." LIMIT 1";
  $exeverifica = mysqli_query($verifica);
  $num_rows = mysqli_num_rows($exeverifica);
    
  if($num_rows==0){ 
    $instquery = "INSERT INTO ibdw_profile_cover (Owner,Hash) VALUES ('".$ewsa."','".$hash."')";
    $runquery = mysqli_query($instquery);
  }
  else { 
    $num_fetch = mysqli_fetch_assoc($exeverifica);
    $id_elemento = $num_fetch['ID']; 
    $instquery = "UPDATE `ibdw_profile_cover` SET `Hash` = '".$hash."' WHERE `ID` = ".$id_elemento;
    $runquery = mysqli_query($instquery);
  }
  
  $slx = "SELECT uri FROM bx_photos_main WHERE `Hash` = '".$hash."'";
  $exeslx = mysqli_query($slx);
  $fetchslx = mysqli_fetch_assoc($exeslx);
  $namefile = $fetchslx['uri'];
  
  $profilevector = getProfileInfo($ewsa);
  $ProfileNameis=getNickname($ewsa);
  
   
  $array["profile_link"] = BX_DOL_URL_ROOT.$profilevector['NickName'];
  $array["profile_nick"] = $ProfileNameis;
  $array["entry_url"] = BX_DOL_URL_ROOT.'m/photos/view/'.$namefile;
  $array["recipient_p_link"] = BX_DOL_URL_ROOT.$profilevector['NickName'];
  $array["recipient_p_nick"] = $ProfileNameis;
  $array["currenthash"] = $hash;
  $str = serialize($array); 
  if ($profilevector['Sex']=="male") $key='_ibdw_profilecover_update_male';
  elseif ($profilevector['Sex']=="female") $key='_ibdw_profilecover_update_female';
  else $key='_ibdw_profilecover_update';
  $instquery = "INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES('".$ewsa."','".$key."','".$str."')";
  $runquery = mysqli_query($instquery);
?>