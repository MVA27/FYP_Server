<?php
	$file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));
	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);

	$query_truncate_parameters = "TRUNCATE TABLE parameters";
	$query_truncate_status = "TRUNCATE TABLE status";

	//Truncate status table first
	$result = mysqli_query($connection,$query_truncate_status);
	if($result){

		//Truncate parameters table afterwards due to foreign key
		$result = mysqli_query($connection,$query_truncate_parameters);
		if($result) http_response_code(200);
		else http_response_code(400);

	}
	else http_response_code(400);
?>