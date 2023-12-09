<?php
	$host = "synthwave-authentication.cxyp7u6ndnrf.eu-north-1.rds.amazonaws.com";
	$user = "admin_auth";
	$pass = "jbCN:xJ.;(87D5qyH3Q<wm";
	$db_name = "synth_authentication";
	
	$conn = new mysqli($host, $user, $pass, $db_name);
	if($conn->connect_error){
		die('Connection error'. $conn->connect_error);
	}
	
?>