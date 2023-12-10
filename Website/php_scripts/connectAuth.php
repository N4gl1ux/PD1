<?php
	$host = apache_getenv('AUTH_DATABASE_HOST');
	$user = apache_getenv('AUTH_DATABASE_USER');
	$pass = apache_getenv('AUTH_DATABASE_PASS');
	$db_name = apache_getenv('AUTH_DATABASE_NAME');
	
	$conn = new mysqli($host, $user, $pass, $db_name);
	if($conn->connect_error){
		die('Connection error'. $conn->connect_error);
	}
	
?>