<?php
    $temperature = $_REQUEST['t'];
    $pressure = $_REQUEST['p'];
    $humidity = $_REQUEST['h'];
    $airQuality = $_REQUEST['a'];
    
	date_default_timezone_set('Asia/Kolkata');
	$day = date('d');
	$month = date('m');
	$year = date('Y');	
	$hours = date('h');
	$minutes = date('i');
	$seconds = date('s');
	
	$file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));
	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);
	
	$status = setStatus($temperature,$pressure,$humidity,$airQuality,$connection);

	$query = "INSERT INTO parameters(day,month,year,hours,minutes,seconds,temperature,pressure,humidity,air_quality,status) VALUES ($day,$month,$year,$hours,$minutes,$seconds,$temperature,$pressure,$humidity,$airQuality,$status)";
	
	if($connection){
		$result = mysqli_query($connection,$query);
		
		if($result){ //If Insertion is successful
		
		    $query = "SELECT * FROM flags";
		    $result = mysqli_query($connection,$query);
		    
		    if($result){ // If the flags are successfully fetched
		        $row = mysqli_fetch_array($result);
		        $sleep = $row['sleep'];
		        echo "{sleep: $sleep}";
		    }
		    
		}
		
	}
	else{
		echo "error";
	}


	function setStatus($temperature,$pressure,$humidity,$airQuality,$connection){
		$query = "SELECT * FROM threshold WHERE application='pb'";

		$result = mysqli_query($connection,$query);
		
		if($result){
		    $row = mysqli_fetch_array($result);
		    if(($temperature >= $row['temperature']) || ($pressure >= $row['pressure']) || ($humidity >= $row['humidity']) || ($airQuality <= $row['air_quality'])){
		    	return 1; //Status Bad
		    }
		}
		return 0; //Status Good
	}
?>
