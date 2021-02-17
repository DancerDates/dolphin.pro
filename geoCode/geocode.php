<?php
error_reporting(0);
function request_cache($url, $dest_file, $timeout=43200) 
{  
	if(!file_exists($dest_file) || filemtime($dest_file) < (time()-$timeout)) 
	{    
		$stream = fopen($url,'r');    
		$tmpf = tempnam('/tmp','YWS');   
		file_put_contents($tmpf, $stream);   
		fclose($stream);    
		rename($tmpf, $dest_file);  
	 }
}
	$location=$_GET['location'];
	$q = 'http://api.local.yahoo.com/MapsService/V1/geocode';

	$q .= '?appid=rlerdorf&location='.rawurlencode($location);  
	$tmp = '/tmp/yws_geo_'.md5($q);  
	request_cache($q, $tmp, 43200);  
	libxml_use_internal_errors(true); 
	$xml = simplexml_load_file($tmp);   
	$ret['precision'] = (string)$xml->Result['precision'];  
	foreach($xml->Result->children() as $key=>$val)
	{   
		if(strlen($val)) 
			$ret[(string)$key] =  (string)$val;  
	}  
	echo $ret['City'];

?>