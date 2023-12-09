<?php
	$host = "synthwave-samples.cxyp7u6ndnrf.eu-north-1.rds.amazonaws.com";
	$user = "admin_synthw";
	$pass = "v=<5+UCg[m*-PK^;(eRg87";
	$db_name = "synthwave_samples";
	
	$conn = new mysqli($host, $user, $pass, $db_name);
	if($conn->connect_error){
		die('Connection error'. $conn->connect_error);
	}
	
?>