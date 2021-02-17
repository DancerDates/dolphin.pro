<?php
/**
 * @version 1.0
 * @copyright Copyright (C) 2014 rayzzz.com. All rights reserved.
 * @license GNU/GPL2, see LICENSE.txt
 * @website http://rayzzz.com
 * @twitter @rayzzzcom
 * @email rayzexpert@gmail.com
 */
require_once("include/xml.php");
require_once("include/db.php");
require_once("include/base.php");
require_once("include/integration.php");
require_once("include/module.php");

$https = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off') ? 's://' : '://';
$sBaseUrl = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
$sBaseUrl = substr($sBaseUrl, 0, -7);
$sBasePath = dirname(__FILE__) . "/";
$oModule = new RzModule($sBasePath, $sBaseUrl);

$sMethod = "action" . ucfirst($oModule->getRequestVar("rzaction"));
if(method_exists($oModule, $sMethod))
	$sContents = call_user_func_array(array($oModule, $sMethod), array());
else
	$sContents = "";

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header ('Content-Type: application/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>" . $oModule->makeGroup($sContents);
