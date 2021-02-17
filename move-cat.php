<?php

require_once('inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );

$parentsm = explode(";", $_POST['order']);

$count=count($parentsm)-1;

for($i=0;$i<$count;$i++) {

	$parentsm_data = explode("|", $parentsm[$i]);

	$Category=$parentsm_data[0];
	$Type=$parentsm_data[1];

	db_res("UPDATE `sys_categories` SET `Order`='$i' WHERE `Category`='$Category' AND `Type`='$Type';");

}

	
?>