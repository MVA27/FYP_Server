<?php
    //https://project4y.000webhostapp.com/Android/set_flags.php?
    //https://project4y.000webhostapp.com/Android/set_flags.php?terminate=1
    //https://project4y.000webhostapp.com/Android/set_flags.php?sleep=10
    
    if(count($_REQUEST) == 0){
	    http_response_code(400);
	    exit(0);
	}

    $query = "UPDATE flags SET ";

    if(array_key_exists('sleep', $_REQUEST)){
    	$sleep = $_REQUEST['sleep'];
    	$query = $query . "sleep=".$sleep;
    }

    if(array_key_exists('terminate', $_REQUEST)){
    	$terminate = $_REQUEST['terminate'];
    	$query = $query . "terminate=".$terminate;
    }
    
    $query = $query . " WHERE srno=1";

	$file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));

	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);
	$result = mysqli_query($connection,$query);
	 if($result){
		http_response_code(200);
	 }
	 else{
	     http_response_code(400);
	 }
?>