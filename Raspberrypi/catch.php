<?php
    if(count($_REQUEST) == 0){
	    http_response_code(400);
	    exit(0);
	}

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

	$query = "INSERT INTO parameters(day,month,year,hours,minutes,seconds,temperature,pressure,humidity,air_quality) VALUES ($day,$month,$year,$hours,$minutes,$seconds,$temperature,$pressure,$humidity,$airQuality)";
	
	if($connection){

		$result = mysqli_query($connection,$query);
		
		//If Insertion is successful
		if($result){ 
		
			//Update Status Table 
			updateStatusTable($temperature,$pressure,$humidity,$airQuality,$connection);

			//Return a response 
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

	function updateStatusTable($temperature,$pressure,$humidity,$airQuality,$connection){
		//STEP 1: Fetch the threshold first
		$query = "SELECT * FROM threshold WHERE application='pb'";
		$result = mysqli_query($connection,$query);

		if($result){
		    $row = mysqli_fetch_array($result);
		    
			$threshold_temperature = $row['temperature'];
		    $threshold_pressure = $row['pressure'];
		    $threshold_humidity = $row['humidity'];
		    $threshold_airQuality = $row['air_quality'];

			$status_temperature = 0;
		    $status_pressure = 0;
		    $status_humidity = 0;
		    $status_airQuality = 0;
		    $status_overall = 0;

		    //STEP 2 : Validate 
		    if($temperature >= $threshold_temperature){
		    	$status_temperature = 1;
		    }
		    if($pressure >= $threshold_pressure){
		    	$status_pressure = 1;
		    }
		    if($humidity >= $threshold_humidity){
		    	$status_humidity = 1;
		    }
		    if($airQuality <= $threshold_airQuality){
		    	$status_airQuality = 1;
		    }
		    if($status_temperature == 1 || $status_pressure == 1 || $status_humidity == 1 || $status_airQuality == 1){
		    	$status_overall = 1;
		    }

		    //STEP 3 : Prepare the query
		    $query = "INSERT INTO status(temperature, pressure, humidity, air_quality, status) VALUES ($status_temperature,$status_pressure,$status_humidity,$status_airQuality,$status_overall)";

		    //STEP 4 : Execute the query
		    if($connection){
		    	$result = mysqli_query($connection,$query);
		    }
		}
	}
?>
