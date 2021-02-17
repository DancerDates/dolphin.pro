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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');
class profilecoverConfig extends BxDolConfig 
{
 var $iKeyCode;
 var $iusedefaultCover;
 var $iAlbumCoverName;
 var $iFriends;
 var $iPhotos;
 var $iSounds;
 var $iVideos;
 var $iGroups;
 var $igroupsmod;
 var $iEvents;
 var $ieventsmod;
 var $iAds;
 var $iadsmod;
 var $iPolls;
 var $ipolls_mod;
 var $iSites;
 var $iBlogs;
 var $iblogsmod;
 var $iPages;
 var $ipagesmod;
 var $iDisplayheadline;
 var $imaxfilesize;
 var $iprofileimaget;
 var $ixyfactor;
 var $iEnableEditProfilePictureLink;
 
 function profilecoverConfig($aModule) 
 {
  parent::__construct($aModule);
  $this -> iKeyCode = getParam('KeyCode');
  $this -> iusedefaultCover = getParam('usedefaultCover') ? true : false;
  $this -> iAlbumCoverName = getParam('AlbumCoverName');
  $this -> iFriends  = getParam('Friends') ? true : false;
  $this -> iPhotos  = getParam('Photos') ? true : false;
  $this -> iSounds  = getParam('Sounds') ? true : false;
  $this -> iVideos  = getParam('Videos') ? true : false;
  $this -> iGroups  = getParam('Groups') ? true : false;
  $this -> igroupsmod  = getParam('groupsmod');
  $this -> iEvents  = getParam('Events') ? true : false;
  $this -> ieventsmod  = getParam('eventsmod');
  $this -> iAds  = getParam('Ads') ? true : false;
  $this -> iadsmod  = getParam('adsmod');
  $this -> iPolls  = getParam('Polls') ? true : false;
  $this -> ipolls_mod  = getParam('polls_mod');
  $this -> iSites  = getParam('Sites') ? true : false;
  $this -> iBlogs  = getParam('Blogs') ? true : false;
  $this -> iblogsmod  = getParam('blogsmod');
  $this -> iPages  = getParam('Pages') ? true : false;
  $this -> ipagesmod  = getParam('pagesmod');
  $this -> iDisplayheadline  = getParam('Displayheadline') ? true : false;
  $this -> imaxfilesize = getParam('maxfilesize');
  $this -> iprofileimaget = getParam('profileimaget');
  $this -> ixyfactor = getParam('xyfactor');
  $this -> iEnableEditProfilePictureLink = getParam('EnableEditProfilePictureLink') ? true : false;
 }
}
?>