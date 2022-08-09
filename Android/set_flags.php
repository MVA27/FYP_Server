<?php
    //https://project4y.000webhostapp.com/Android/set_flags.php?
    //https://project4y.000webhostapp.com/Android/set_flags.php?terminate=1
    //https://project4y.000webhostapp.com/Android/set_flags.php?sleep=10

	/**
	 * If $_REQUEST is empty (i.e no arguments passed) then exit
	 * At a time only one flag will be passed 
	 * 		- i.e 'set_flags.php?terminate=1' OR 'set_flags.php?sleep=10'
	 * 		- It cannot be 'set_flags.php?terminate=1&sleep=10'
	 * Here if the flag passed is 'terminate' flag then
	 * 		- Check the value for terminate flag
	 * 		- If 'terminate == 1' UPDATE 'real_time_parameters' table to 0s
	 * 		- Else do nothing
	 * */
    if(count($_REQUEST) == 0){
	    http_response_code(400);
	    exit(0);
	}

	$file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));

    $query = "UPDATE flags SET ";

    if(array_key_exists('sleep', $_REQUEST)){
    	$sleep = $_REQUEST['sleep'];
    	$query = $query . "sleep=".$sleep;
    }

    if(array_key_exists('terminate', $_REQUEST)){
    	$terminate = $_REQUEST['terminate'];
    	$query = $query . "terminate=".$terminate;
    }

    if(array_key_exists('sms', $_REQUEST)){
    	$sms = $_REQUEST['sms'];
    	$query = $query . "sms_service=".$sms;
    }
    
    $query = $query . " WHERE srno=1";

    //Set All Flags
	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);
	$result = mysqli_query($connection,$query);

	if($result){	
		//if $terminate == 1 UPDATE 'real_time_parameters' table values to 0s
		if($terminate == 1){
			$query = "UPDATE real_time_parameters SET temperature=0,pressure=0,humidity=0,air_quality=-1 WHERE srno=1";
			$result = mysqli_query($connection,$query);
		}

		http_response_code(200);
	 }
	 else{
	    http_response_code(400);
	 }
?>