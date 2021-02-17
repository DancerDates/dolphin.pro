<?php
	if( isset($_REQUEST['gConf']) ) die; // globals hack prevention
	require_once ($gConf['dir']['layouts'] . 'base/params.php');

    $gConf['dir']['xsl'] = $gConf['dir']['layouts'] . 'dfl/xsl/';	// xsl dir

    $gConf['url']['css'] = $gConf['url']['layouts'] . 'dfl/css/';	// css url
    $gConf['url']['xsl'] = $gConf['url']['layouts'] . 'dfl/xsl/';	// xsl url

?>
