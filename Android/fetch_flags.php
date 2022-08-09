<?php
    $file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));

	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);

	$query = "SELECT * FROM flags WHERE srno=1";

	$result = mysqli_query($connection,$query);
	
	if($result){
	    $row = mysqli_fetch_array($result);
	    
	    $sleep = $row['sleep'];
	    $terminate = $row['terminate'];
	    $sms_service = $row['sms_service'];

	    echo "{sleep: '$sleep', terminate: '$terminate', sms_service: '$sms_service'}";
	    	
		http_response_code(200);
	
	}
	else{
	    http_response_code(400);
	}
	
	fclose($file);
?>