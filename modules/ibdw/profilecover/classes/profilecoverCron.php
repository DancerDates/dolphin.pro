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

bx_import('BxDolCron');
require_once('profilecoverModule.php');
class profilecoverCron extends BxDolCron 
{
 var $oModule;
 /*Class constructor;*/
 function profilecoverCron() 
 {
  $this -> oModule = BxDolModule::getInstance('profilecoverModule');
 }
 function processing() 
 {
  $this -> oModule -> _oDb -> removeOldFiles();
 }
}