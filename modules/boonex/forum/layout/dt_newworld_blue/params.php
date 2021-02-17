<?php
	if( isset($_REQUEST['gConf']) ) die; // globals hack prevention
	require_once ($gConf['dir']['layouts'] . 'base/params.php');

    $gConf['dir']['xsl'] = $gConf['dir']['layouts'] . 'dt_newworld_blue/xsl/';	// xsl dir

    $gConf['url']['css'] = $gConf['url']['layouts'] . 'dt_newworld_blue/css/';	// css url
    $gConf['url']['xsl'] = $gConf['url']['layouts'] . 'dt_newworld_blue/xsl/';	// xsl url

?>
