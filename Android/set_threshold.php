<?php
    //https://project4y.000webhostapp.com/Android/set_threshold.php?application=pb&temperature=35&pressure=999&humidity=50&airquality=90
    if(count($_REQUEST) == 0){
	    http_response_code(400);
	    exit(0);
	}

    $query = "UPDATE threshold SET ";

    if(array_key_exists('temperature', $_REQUEST)){
    	$temperature = $_REQUEST['temperature'];
    	$query = $query."temperature=$temperature,";
    }

    if(array_key_exists('pressure', $_REQUEST)){
    	$pressure = $_REQUEST['pressure'];
    	$query = $query."pressure=$pressure,";
    }

    
    if(array_key_exists('humidity', $_REQUEST)){
    	$humidity = $_REQUEST['humidity'];
    	$query = $query."humidity=$humidity,";
    }

    if(array_key_exists('airquality', $_REQUEST)){
    	$airQuality = $_REQUEST['airquality'];
    	$query = $query."air_quality=$airQuality,";
    }
    

    $query = substr_replace($query ,"",-1);

    if(array_key_exists('application', $_REQUEST)){
    	$application = $_REQUEST['application'];
    	$query = $query." WHERE application='pb'";
    }

    echo $query . "<br>";

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