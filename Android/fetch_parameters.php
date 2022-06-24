<?php
	$file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));

	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);

	$query = "SELECT * FROM parameters WHERE srno=(SELECT MAX(srno) FROM parameters)";

	$result = mysqli_query($connection,$query);
	
	if($result){
	    $row = mysqli_fetch_array($result);
	    
	    $temperature = $row['temperature'];
	    $pressure = $row['pressure'];
	    $humidity = $row['humidity'];
	    $air_quality = $row['air_quality'];
	    
	    echo "{temperature: '$temperature',pressure: '$pressure',humidity: '$humidity',air_quality: '$air_quality'}";
	    	
		http_response_code(200);
	
	}
	else{
	    http_response_code(400);
	}
?>