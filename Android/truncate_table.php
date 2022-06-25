<?php
	//If there is any GET/POST data then only execute else exit
	if(count($_REQUEST) == 0){
	    http_response_code(400);
	    exit(0);
	}

	$table_name = $_REQUEST['tableName'];

	$file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));

	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);

	$query = "TRUNCATE TABLE $table_name";

	$result = mysqli_query($connection,$query);
	
	if($result){
		http_response_code(200);
	}
	else{
	    http_response_code(400);
	}
?>