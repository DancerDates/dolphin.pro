<?php
/**********************************************************************************
*                            IBDW Profile Cover for Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mar 18 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW Profile Cover is not free and you cannot redistribute and/or modify it.
* 
* IBDW Profile Cover is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

bx_import('BxDolModuleDb');
bx_import('BxDolModule');
bx_import('BxDolInstallerUtils');

class profilecoverModule extends BxDolModule 
{
 var $aModuleInfo;
 var $sPathToModule;
 var $sHomeUrl;
 
 function profilecoverModule(&$aModule) 
 {        
  parent::__construct($aModule);
  // prepare the location link ;
  $this -> sPathToModule  = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
  $this -> aModuleInfo    = $aModule;
  $this -> sHomeUrl       = $this ->_oConfig -> _sHomeUrl;
  // Settings
  $this -> aCoreSettings = array (
   'KeyCode' 				    => $this -> _oConfig -> iKeyCode,
   'usedefaultCover'    => $this -> _oConfig -> iusedefaultCover,
   'AlbumCoverName'  	  => $this -> _oConfig -> iAlbumCoverName,
   'Friends'  	        => $this -> _oConfig -> iFriends,
   'Photos'  	          => $this -> _oConfig -> iPhotos,
   'Sounds'  	          => $this -> _oConfig -> iSounds,
   'Videos'  	          => $this -> _oConfig -> iVideos,
   'Groups'  	          => $this -> _oConfig -> iGroups,
   'groupsmod'  	      => $this -> _oConfig -> igroupsmod,
   'Events'  	          => $this -> _oConfig -> iEvents,
   'eventsmod'  	      => $this -> _oConfig -> ieventsmod,
   'Ads'  	            => $this -> _oConfig -> iAds,
   'adsmod'  	          => $this -> _oConfig -> iadsmod,
   'Polls'  	          => $this -> _oConfig -> iPolls,
   'polls_mod'  	      => $this -> _oConfig -> ipolls_mod,
   'Sites'  	          => $this -> _oConfig -> iSites,
   'Blogs'  	          => $this -> _oConfig -> iBlogs,
   'blogsmod'  	        => $this -> _oConfig -> iblogsmod,
   'Pages'  	          => $this -> _oConfig -> iPages,
   'pagesmod'  	        => $this -> _oConfig -> ipagesmod,
   'Displayheadline'  	=> $this -> _oConfig -> iDisplayheadline,
   'maxfilesize'  	    => $this -> _oConfig -> imaxfilesize,
   'profileimaget'  	  => $this -> _oConfig -> iprofileimaget,
   'xyfactor'  	        => $this -> _oConfig -> ixyfactor,
   'EnableEditProfilePictureLink' => $this -> _oConfig -> iEnableEditProfilePictureLink,
   );
 }
 
function actionAdministration()
 {
  if( !isAdmin() ) {header('location: ' . BX_DOL_URL_ROOT);}
  // get sys_option's category id;
  $iCatId = $this-> _oDb -> getSettingsCategory();
  if(!$iCatId) {$sOptions = MsgBox( _t('_Empty') );}
  else 
  {
   bx_import('BxDolAdminSettings');
   $oSettings = new BxDolAdminSettings($iCatId);               
   $mixedResult = '';
   if(isset($_POST['save']) && isset($_POST['cat'])) {$mixedResult = $oSettings -> saveChanges($_POST);}
   // get option's form;
   $sOptions = $oSettings -> getForm();
   if($mixedResult !== true && !empty($mixedResult)) {$sOptions = $mixedResult . $sOptions;}
  }
  $this -> _oTemplate -> addCss('forms_adv.css');
  $this -> _oTemplate-> pageCodeAdminStart();
  echo DesignBoxAdmin( _t('_ibdw_profilecover_informations')
                        , $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => _t('_ibdw_profilecover_information_block', BX_DOL_URL_ROOT))) );
  echo DesignBoxAdmin( _t('_Settings'), $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => $sOptions) ));
  $this -> _oTemplate->pageCodeAdmin( 'Profile Cover' );
 }     
}
?>