<?php
/***************************************************************************
*                                 GeoDistance
*                              -------------------
*     copyright (C) 2013 Dately
*
*     This is a commercial product made by Dately
*     Do not copy, reproduce, distribute, sell or offer it for sale, publish,
*     display, perform, modify, create derivative works, transmit, or in any
*     way exploit any content of this module without written permission by
*     the author. For each domain you want to install this module you need its own license!
*
*     Email: dolmods@gmail.com
*
***************************************************************************/

bx_import('BxDolModule');

class DatelyGeoDModule extends BxDolModule {

    var $finetune;
    var $outputformat;
    var $outputunit;
    var $outputunit2;
    var $moduleactive;

    function DatelyGeoDModule(&$aModule) {
        parent::__construct($aModule);
        $this->finetune = $this->_oDb->getParameterFromSettings('Dately_geodistance_finetune');
        $this->outputformat = $this->_oDb->getParameterFromSettings('Dately_geodistance_format');
        $this->outputunit = $this->_oDb->getParameterFromSettings('Dately_geodistance_use_miles') == 'on' ? _t('_Dately_geodistance_miles') : _t('_Dately_geodistance_km');
        $this->outputunit2 = $this->_oDb->getParameterFromSettings('Dately_geodistance_use_unit') != 'on' ? "" : " " . $this->outputunit;
	$this->moduleactive = $this->_oDb->getParameterFromSettings('Dately_geodistance_switch');
    }

    function actionAdministration () {

        if (!$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the admin design

        $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
        if(empty($iId)) { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_Dately_geodistance'));
            return;
        }

        bx_import('BxDolAdminSettings'); // import class

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) { // save settings
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId); // get display form code
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult)) // attach any resulted messages at the form beginning
            $sResult = $mixedResult . $sResult;

        echo DesignBoxAdmin (_t('_Dately_geodistance'), $sResult); // dsiplay box

        $this->_oTemplate->pageCodeAdmin (_t('_Dately_geodistance')); // output is completed, admin page will be displaed here
    }

function GetDistance($UserID1, $UserID2)
    {
        $aLocationUser1 = $this->GetCoords($UserID1);
        $aLocationUser2 = $this->GetCoords($UserID2);

        if ($aLocationUser1 && $aLocationUser2) {
                $x1 = $aLocationUser1['lng'];
                $y1 = $aLocationUser1['lat'];
                $x2 = $aLocationUser2['lng'];
                $y2 = $aLocationUser2['lat'];

		eval(gzinflate(base64_decode(rawurldecode('XVa1DsQGDP2ctsoQJlUdLsx8oaUKM3O%2BvjfXqyVbsh8VZ9L%2FmSZbQWD%2F5kU25cWf1XuM2TTMa7Ft%2F2v9USiX3%2B9IsKyTW%2FvUzJjDfXJVKD585QpvJ6CyVBuQ89kVVQU1SAFPt%2FNZM%2FDCdYBFXz9Q5bXgrqQDSIzRq3iROkz5xFe4SKxxycmQAwLeMm0XtqpD9G2KMq3lywIGEKaNO0m0Iy%2B7zPb648RgStqXryV1mc4JxyE378qU8VCQCIS21Ff3NA2X0h6KEUj4pN2Q4FaN7MzpM5%2F3bURVTLprstTmqdev30GUNpLLDTSwVDZvG5lZuT1zWca7AwJ3v2vRwvTqfCyb4vXOV0%2BZjBaXbrB9hEbRSa2Z5%2FGRHvbU6JP7JEb4IyaH1y5ybxCmmhCzmSREBxGfyYrRJC5umaRkIueb8e1lj%2BYojVpwors47oh1aQdA0m4RH96lhXDvBZO7C0zT4%2BZ5YSOQ3ot4%2ByzAhiDtFGfA53RLTGiI81S8ejkfDY%2BKldvlLzB4NOmCpPS3ffT1UiRw0%2FiNxOQ6JHSmvrSPFbUH43E0TUa4WF%2F6ETteb1Qr%2BFXJeLBbU6PV4CPWclEKN9hMcW9GNLvypNVi7bXoJbzcIpawc00s64sHH9x5ohkLlWP6Qp3dCelQwGoLb3jhKCSqZuwQq0e%2F98%2F8dA6jMLFi1zVg5t7Ff52%2BRzPwWg0Mz1eEOPLnS51JVzelKjgHt%2FQpQAg0RYjEtsgLJo1l83Ba09PifHgQUiV%2Be0%2FVYGsVWq7xY2g576Gm2vRHu9Kx3%2B2M0%2BtrmMJ3zTd1Ag788Awob0BeAUiSqBQkGtq%2BGi2TW83PGWWrmRP8AA4xTHfC4mgXtMdx2stkOhjv9lFW2tsJcRsa5tZRNh0cseSowOZwpDijMuCmKfR1VA7vD1DcclBFwy5QAdduq%2BuvyzhVmjZcKgLAXxM1fCKgQel5OYRT2fyQBrFWDtqcXfLuZMhAxQudsDPdfRPl6VHO0x4L2PUAY6fglNUrhrOjj2nkV85R30NWrzm3sOi%2BjIdbt2hLoeJsvrFcpubjqsERwnotCB7EOjXvXxiy4738GFuZqTDrl2vn6RGBffSEYUzr3KZGSgawGqhVOJzMY27oq%2BmmNh3i%2FZQlEBnsaNeH%2Bt3XgTf7oUccpU9nJtBiB40qXaqc4nP3p4ZNDnllkA0iUDLX%2FXYuAASL%2BE0zpVmEPJmOZ44rhYya2nWhdpTYzzaZnM%2BGfG1L21ac6BDfCp2%2FBn1mr0tWjydZVio4IkPxF0dozZ75TTrxYkIxiFuB6oA6HXZjP85gtFbCLHo0PfeQeHosTalD%2B7aHJJLqOHKfcjIuTQrmZXOg3XuQkkNHiMJp1g0ynQG8yELX9C3czjQz1nswDJNiMKe07M3ycrWM6LT4mYUQ85C2mz3FGY2290OZji453a47Nd54o17GCw7ruNDESeMEWi1NXzA4VBCzXQuQiQp%2F3I%2F8gwBL0TNypgtktw8u3rF%2FMjfmnwMXFc4lzJIKfILUOgMSTFFwfWzPvMktuTEguifWzIdUTyvo2wSWG%2BzrFep2qlVF785pnqFNV1QU7hQiOWRAmW6FNc6b1xV7sEn0RycR9J7iYDsotO39z8dXFuLzu44rtOaxHy%2BkO50PcR6tLWSbBzfTJCicYADATZ1v06h%2Bzxi%2Bx9P5OSnqGTF8gibL7X9IPyBxIy3bRCVIlzZ4k0Yl75nFj76tfK73EymIbyIZgSoVJirTTwlJ5OPbzoNCLktB2T0QXr30mqyK6o8tX88AHiAJc%2BquLOYZoHHInlnia5wqXXzlVrtp7pm5DYcyfq%2BCGadJzcg4wYYnjJO4INIfOACV3L6OvXglXhmR5I%2B5LgEM2FkmjmUaXYWAtF7Z3zHXJmuXWJ9IInIHhgNitLgvq8fl%2FVWSlvGkCylIOkqWeah9TAQZDix0x%2FEHtDnfsSTixheclNEWa2y6OG8nwi%2Fws5zBPDSaoXOOUffCjGsoar7lVwiVdFhF%2BiJukZ%2Fbu%2FOKUX%2BLtjdLxFmS64uQ5wsTHSktZZiDgbsDe52N3yQU0tilnEV9X72SLeJqPCrS9%2Fpmo5%2Bo6fncUhxZYWACsRLSMyBBJ%2B0T7jdjEIwbWG9r8LjaTdrPaHRBODoc1BY1Mw8pE3cp%2BwGetfxNpFD5q5N1QHjDzO7rOstQrrGafbhz9UN6zYfSs0BiQsQINiSl4GJWpaWmRFoCo8xcDm%2FMSHlfKgoVKu3CAqrI9rGPA9%2FMjmGtBQ6DsRmt73iR6lYugbGZmOHRP%2B8Ig0TglTRGQhbvhfD8voCWaHNPRiRDcQ8YXYTRqYp5PwA7AVrDm7Wf%2BRIitIVinXiDUNEqh2HbVnDTBz2Fs3t0JK80Rzi6gPnZSwmZUhltxoeM2mC7mtbMfWe4XocrFWKyzeCRDxs5oi8PLPdv5WqCVE6%2B%2FfWt2Ve9OtQatF09kcz5ICvbMXeCfIak0YSQUqUOvV9jiLzNsoSj%2Bosao6d6BsShy9PiUa3pGvOh8J%2B7SRGxN3cCjHmSPoHm3PtpxCT%2FC1yNz1wwXOSyDv%2Bkz7io8EAU%2B2muTWAdIUuVGwEhYX3y8S3BX1TKndz%2F%2FPPPH3%2F96u%2F%2FAA%3D%3D'))));

		if ($distance < 0) {$distance = 0;};
		if ($this->outputunit == "mi") $distance /= 1.609344;
                $output = sprintf($this->outputformat, $distance) . $this->outputunit2;
                return $output;
        } else {
                return _t('_Dately_geodistance_nolocdata');
        }
    }

    function GetCoords ($iProfId) {
        $iProfId = (int)$iProfId;

        $aLocation = $iProfId ? $this->_oDb->getProfileById($iProfId) : false;
        if (!$aLocation)
	{
            return false;
	} else {
	   if ($aLocation['lng'] == '' || $aLocation['lat'] == '')
		return false;
	}
        return $aLocation;
    }

    function serviceGetDistance($UserID1, $UserID2)
    {
	if($UserID1 && $UserID2 && $this->moduleactive == 'on')
		return $this->GetDistance($UserID1, $UserID2);
	return _t('_Dately_geodistance_nolocdata_available');
    }

}

// (c) dately 2013 - All rights reserved

?>
