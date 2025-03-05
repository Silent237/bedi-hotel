<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");

date_default_timezone_set('Asia/Calcutta');

$name = date('YmdHis');
$newname="cust_images_temp/".$name.".jpg";
$file = file_put_contents( $newname, file_get_contents('php://input') );
if (!$file) {
	print "ERROR: Failed to write data to $filename, check permissions\n";
	exit();
}

$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $newname;
print "$newname";

?>
