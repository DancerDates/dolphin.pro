<?php
  require_once( '../../../inc/header.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
  include BX_DIRECTORY_PATH_MODULES.'ibdw/eventcover/config.php';
  $ewsa=$_COOKIE['memberID']; 
  $hash=$_POST['hashe'];
  $id=$_POST['id'];
  
  $UriSelect="SELECT EntryUri From ".$eventstableis." where ID=".$id;
  $getUri=mysqli_query($UriSelect);
  $UriRetrieve=mysqli_fetch_assoc($getUri);
  $Uri= $UriRetrieve['EntryUri'];
  
  $verifica = "SELECT ID FROM ibdw_event_cover WHERE Owner = ".$ewsa." AND Uri='".$Uri."' LIMIT 1";
  $exeverifica = mysqli_query($verifica);
  $num_rows = mysqli_num_rows($exeverifica);
    
  if($num_rows==0){ 
    $insequery = "INSERT INTO ibdw_event_cover (Owner,Hash,Uri) VALUES ('".$ewsa."','".$hash."','".$Uri."')";
    $runquery = mysqli_query($insequery);
  }
  else { 
    $num_fetch = mysqli_fetch_assoc($exeverifica);
    $id_elemento = $num_fetch['ID']; 
    $insequery = "UPDATE `ibdw_event_cover` SET `Hash` = '".$hash."' WHERE `ID` = ".$id_elemento. " AND Uri='".$Uri."'";
    $runquery = mysqli_query($insequery);
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
  $array["event_uri"] = BX_DOL_URL_ROOT.$eventpath."view/".$Uri;
  $array["currenthash"] = $hash;

  $str = serialize($array);
  
  if ($profilevector['Sex']=="male") $key='_ibdw_eventcover_update_male';
  elseif ($profilevector['Sex']=="female") $key='_ibdw_eventcover_update_female';
  else $key='_ibdw_eventcover_update';
  $insequery = "INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES('".$ewsa."','".$key."','".$str."')";
  $runquery = mysqli_query($insequery);
?>