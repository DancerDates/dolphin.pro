<?php
/***************************************************************************
* Date				: Feb 21, 2013
* Copywrite			: (c) 2013 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Facebook Connect
* Product Version	: 4.2.7
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/

require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

class BxDbcsFaceBookConnectInstaller extends BxDolInstaller 
{
	function BxDbcsFaceBookConnectInstaller(&$aConfig) 
	{
		parent::BxDolInstaller($aConfig);
	}

	function actionCheckRequirements()
	{
		$bError = (int) phpversion() >= 5 
			? BX_DOL_INSTALLER_SUCCESS 
			: BX_DOL_INSTALLER_FAILED;

		return $bError;
	}

	function actionCheckRequirementsFailed()
	{
		return '
		<div style="border:1px solid red; padding:10px;">
			You need <u>PHP 5</u> or higher!
		</div>';
	}


    function uninstall($aParams)
    {
		// Remove buttons.


        $aResult = parent::uninstall($aParams);
        return $aResult;
    }

}