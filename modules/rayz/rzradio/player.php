<?php
/**
 * @version 1.0
 * @copyright Copyright (C) 2014 rayzzz.com. All rights reserved.
 * @license GNU/GPL2, see LICENSE.txt
 * @website http://rayzzz.com
 * @twitter @rayzzzcom
 * @email rayzexpert@gmail.com
 */
$sFlashVars = "url=XML.php&amp;title=" . $_GET['title'] . "&amp;playlist=" . $_GET['playlist'];
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $_GET['title']; ?></title>
</head>
<body style="margin:0">
<div style="width:250px;height:150px;">
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="100%" height="100%" id="rzplayer" align="middle">
    <param name="movie" value="app/player.swf"/>
    <param name="flashvars" value="<?php echo $sFlashVars; ?>"/>
    <!--[if !IE]>-->
    <object type="application/x-shockwave-flash" data="app/player.swf" width="100%" height="100%" flashvars="<?php echo $sFlashVars; ?>">
        <param name="movie" value="app/player.swf"/>
        <param name="flashvars" value="<?php echo $sFlashVars; ?>"/>
    <!--<![endif]-->
        <a href="http://www.adobe.com/go/getflash">
            <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"/>
        </a>
    <!--[if !IE]>-->
    </object>
    <!--<![endif]-->
</object>
</div>
</body>
</html>