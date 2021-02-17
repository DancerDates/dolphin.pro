<?php
 require_once( '../../../inc/header.inc.php' );
 require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
 
 function criptcodeemail($numero) 
 {
  $id0 = rand(1,9);
  $id1 = rand(10000,99999);    
  $mx = $numero*$id0;
  $generacriptcode = $id0.$id1.$mx;
  return ($generacriptcode); 
 }
 
 //recupero ultimo id
 mysqli_query("SET NAMES 'utf8'");
 $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'like_action'";
 $verifica_exe = mysqli_query($verifica);
 $riga_exe = mysqli_fetch_assoc($verifica_exe);
 $istruzione_1 = $riga_exe['active'];
  
 $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'share'";
 $verifica_exe = mysqli_query($verifica);
 $riga_exe = mysqli_fetch_assoc($verifica_exe);
 $istruzione_2 = $riga_exe['active']; 
  
 $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'messaggiowall'";
 $verifica_exe = mysqli_query($verifica);
 $riga_exe = mysqli_fetch_assoc($verifica_exe);
 $istruzione_3 = $riga_exe['active']; 
  
 $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'commento'";
 $verifica_exe = mysqli_query($verifica);
 $riga_exe = mysqli_fetch_assoc($verifica_exe);
 $istruzione_4 = $riga_exe['active']; 
  
 $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'richiesta_amicizia'";
 $verifica_exe = mysqli_query($verifica);
 $riga_exe = mysqli_fetch_assoc($verifica_exe);
 $istruzione_5 = $riga_exe['active']; 
  
 $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'tag_photodeluxe'";
 $verifica_exe = mysqli_query($verifica);
 $riga_exe = mysqli_fetch_assoc($verifica_exe);
 $istruzione_6 = $riga_exe['active']; 
  
 $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'comment_photodeluxe'";
 $verifica_exe = mysqli_query($verifica);
 $riga_exe = mysqli_fetch_assoc($verifica_exe);
 $istruzione_7 = $riga_exe['active']; 
  
 $verifica = "SELECT ID,active FROM ibdw_emailnotification WHERE key_actions = 'like_photodeluxe'";
 $verifica_exe = mysqli_query($verifica);
 $riga_exe = mysqli_fetch_assoc($verifica_exe);
 $istruzione_8 = $riga_exe['active']; 
    
 function email_notify($key,$proprietario,$azionista,$params) 
 {
  $titolo = "SELECT Name,sys_options.VALUE FROM sys_options WHERE Name='site_title'";
  $exe = mysqli_query($titolo);
  $titarray = mysqli_fetch_assoc($exe);
  $titolosito = $titarray['VALUE'];  
  $etitolo = "SELECT Name,sys_options.VALUE FROM sys_options WHERE Name='site_email'";
  $eexe = mysqli_query($etitolo);
  $etitarray = mysqli_fetch_assoc($eexe);
  $etitolosito = $etitarray['VALUE'];    
  $query = "SELECT * FROM ibdw_emailnotification WHERE key_actions='".$key."'";
  $esegui = mysqli_query($query);
  $assoc = mysqli_fetch_assoc($esegui);  
  if($key == 'share' OR $key == 'commento' ) {
  $paramsx = explode('###',$params);
  $params = $paramsx[0];
  $ultimo = $paramsx[1];  
 } 

$profilo1="SELECT ID,NickName,Email,EmailNotify FROM Profiles WHERE ID=".$proprietario;
$profilo1esegui=mysqli_query($profilo1);
$profilo1assoc=mysqli_fetch_assoc($profilo1esegui);
 
if ($profilo1assoc['EmailNotify']==1)
{  
 
 
 $profilo2="SELECT ID,NickName,Email FROM Profiles WHERE ID=".$azionista;
 $profilo2esegui=mysqli_query($profilo2);
 $profilo2assoc=mysqli_fetch_assoc($profilo2esegui);
  
 if($key == 'share') 
 { 
  $identifica = "SELECT ID,lang_key,params FROM bx_spy_data WHERE ID=".$ultimo;
  $exe_identifica = mysqli_query($identifica);
  $identifica_ass = mysqli_fetch_assoc($exe_identifica);   
  $para = $identifica_ass['params'];
  $elemento = $identifica_ass['lang_key'];
  $elemento_title = $identifica_ass['lang_key']; 
  if($elemento == '_bx_ads_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_ads");}
  elseif($elemento == '_bx_videotube_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_video");}
  elseif($elemento == '_bx_videolocal_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_video");}
  elseif($elemento == '_bx_gruppo_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_group");}
  elseif($elemento == '_bx_photo_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_photo");}
  elseif($elemento == '_bx_poll_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_poll");}
  elseif($elemento == '_bx_site_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_web");}
  elseif($elemento == '_bx_event_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_event");}
  elseif($elemento == '_ibdw_evowall_bx_ads_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_ads");}
  elseif($elemento == '_ibdw_evowall_bx_blogs_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_blogs");}
  elseif($elemento == '_ibdw_evowall_bx_sounds_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_sounds");}
  elseif($elemento == '_ibdw_evowall_bx_video_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_video");}
  elseif($elemento == '_ibdw_evowall_bx_gruppo_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_group");}
  elseif($elemento == '_ibdw_evowall_bx_photo_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_photo");}
  elseif($elemento == '_ibdw_evowall_bx_poll_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_poll");}
  elseif($elemento == '_ibdw_evowall_bx_site_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_web");}
  elseif($elemento == '_ibdw_evowall_bx_event_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_event");}
  elseif($elemento == '_ibdw_evowall_ue30_event_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_event");}
  elseif($elemento == '_ibdw_evowall_modzzz_property_share') { $elemento = _t("_ibdw_emailnotify_share_property");}
  elseif($elemento == '_ibdw_evowall_ue30_locations_add_condivisione') { $elemento = _t("_ibdw_emailnotify_share_location");}
 
  $parametri = unserialize($para);    
  $post = $parametri['entry_url']; 
  $titlelement = $parametri['entry_title']; 
    
  if($elemento_title == '_ibdw_evowall_bx_ads_add_condivisione') 
  {    
   $post = $parametri['ads_url']; 
   $titlelement = $parametri['ads_caption'];  
  }
    
  elseif($elemento_title == '_ibdw_evowall_bx_poll_add_condivisione') 
  {      
   $post = $parametri['poll_url']; 
   $titlelement = $parametri['poll_caption'];  
  }
  
  elseif($elemento_title == '_ibdw_evowall_bx_site_add_condivisione') 
  {    
   $post = $parametri['site_url']; 
   $titlelement = $parametri['site_caption']; 
  }  
  elseif($elemento_title == '_bx_ads_add_condivisione' OR $elemento_title == '_bx_gruppo_add_condivisione' OR $elemento_title == '_bx_event_add_condivisione' ) 
  {  
   $parametri = explode('##',$para);    
   $post = $parametri[0]; 
   $titlelement = $parametri[1]; 
  }
    
  elseif($elemento_title == '_bx_poll_add_condivisione') 
  {  
   $parametri = explode('##',$para);    
   $post = $parametri[1]; 
   $titlelement = $parametri[0]; 
  }
  
  elseif($elemento_title == '_bx_site_add_condivisione') 
  {  
   $parametri = explode('##',$para);    
   $post = BX_DOL_URL_ROOT.$parametri[0]; 
   $titlelement = $parametri[1]; 
  }
 }
  
 if($key == 'commento') 
 { 
  $commento = "SELECT id,commento FROM commenti_spy_data WHERE id =".$ultimo;
  $exe_comm = mysqli_query($commento);
  $assoc_comm = mysqli_fetch_assoc($exe_comm);
  $commento = $assoc_comm['commento'];
 }
  
 elseif($key == 'messaggiowall') 
 { 
  $commento = "SELECT params FROM bx_spy_data WHERE ID =".$params;
  $exe_comm = mysqli_query($commento);
  $assoc_comm = mysqli_fetch_assoc($exe_comm);
  $commentos = unserialize($assoc_comm['params']);
  $commento = $commentos['messaggioo'];
 }
  
 elseif($key == 'tag_photodeluxe' OR $key == 'comment_photodeluxe' OR $key == 'like_photodeluxe' ) 
 {
  $parametri = explode('##',$params);  
  $idfoto = $parametri[0];
  $idalbum = $parametri[1];
  $userid = $parametri[2];
  $generaindirizzofoto = BX_DOL_URL_ROOT.'page/photodeluxe#iff='.criptcodeemail($idfoto).'&ia='.criptcodeemail($idalbum).'&ui='.criptcodeemail($userid);
 }
 
 $destinatario = $profilo1assoc['Email'];  
 $oggetto = _t($assoc['key_title']);
 
 
 
 
 $textmsg = _t($assoc['lang_key']);
 if($key == 'richiesta_amicizia') { $urlgenera = BX_DOL_URL_ROOT.'communicator.php?person_switcher=to&communicator_mode=friends_requests';  } 
 $oggetto = str_replace('{sender}',$profilo2assoc['NickName'],$oggetto);
 $oggetto = str_replace('{recipient}',$profilo1assoc['NickName'],$oggetto);
 $oggetto = '=?UTF-8?B?' . base64_encode( $oggetto ) . '?=';
 
 
 $textmsg = str_replace('{sender}',$profilo2assoc['NickName'],$textmsg);
 $textmsg = str_replace('{recipient}',$profilo1assoc['NickName'],$textmsg);
 $textmsg = str_replace('{link}',BX_DOL_URL_ROOT.'member.php#azione'.$params,$textmsg);
 if($key == 'commento' OR $key == 'messaggiowall') { $textmsg = str_replace('{commento}',$commento,$textmsg);}
 if($key == 'share') { $textmsg = str_replace('{tipeshare}',$elemento,$textmsg);}
 if($key == 'share') { $textmsg = str_replace('{post}',$post,$textmsg);}
 if($key == 'share') { $textmsg = str_replace('{title_element}',$titlelement,$textmsg);}
 if($key == 'share') { $oggetto = str_replace('{tipeshare}',$elemento,$oggetto); }
 if($key == 'richiesta_amicizia') { $textmsg = str_replace('{html}',$urlgenera,$textmsg);}
 if($key == 'tag_photodeluxe' OR $key == 'comment_photodeluxe' OR $key == 'like_photodeluxe') { $textmsg = str_replace('{html}',$generaindirizzofoto,$textmsg);}
 $footer =  str_replace('{titlesite}',$titolosito,_t("footer_email"));
 
 $header = "From: =?UTF-8?B?" . base64_encode( $titolosito ) . "?= <{$etitolosito}>";
 $header = "MIME-Version: 1.0\r\n" . $header;
 $header = "Content-type: text/html; charset=UTF-8\r\n" . $header;
  
 $messaggio = '<html><head><title>'.$oggetto.'</title><style type="text/css">body {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#000000;}</style></head>
                <body>
                 <p>'.$textmsg.'</p>
                 --
                 <p style="color:red">'.$footer.'</p>
                </body>
               </html>'; 
  mail( $destinatario, $oggetto, $messaggio, $header );  
 }
}  
?>