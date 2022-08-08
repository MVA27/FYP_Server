<?php
	//use 'composer require vonage/client' command To install API 
	require_once 'vendor/autoload.php';

	//Importatnt functions that displays error
	// error_reporting(E_ALL);
	// ini_set("display_errors",1);

	/**
	 * When this file is called (i.e catch.php) First check: 
	 * 	If no parameters are passed in $_REQUEST array 
	 * 		- return flags and exit
	 * 
	 * 	If terminate flag is 1 
	 * 		- Dont update the 'parameters' table with received values
	 * 		- Update 'real_time_parameters' table with 0s
	 * , 	- return flags
	 * */

	$file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));
	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);

	//For Blank Request (without data), just send flag values
    if(count($_REQUEST) == 0){
	    returnFlags($connection);
	    exit(0);
	}

	//If terminate flag is 1, return flags and exit 
	if(isTerminated($connection)){
		$query = "UPDATE real_time_parameters SET temperature=0,pressure=0,humidity=0,air_quality=-1 WHERE srno=1";
		$result = mysqli_query($connection,$query);
		returnFlags($connection);
	    exit(0);
	}

	//Extract parameters from HTTP message
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

	//Query for 'parameters' table
	$query1 = "INSERT INTO parameters(day,month,year,hours,minutes,seconds,temperature,pressure,humidity,air_quality) VALUES ($day,$month,$year,$hours,$minutes,$seconds,$temperature,$pressure,$humidity,$airQuality)";

	//Query for 'real_time_parameters' table
	$query2 = "UPDATE real_time_parameters SET temperature=$temperature,pressure=$pressure,humidity=$humidity,air_quality=$airQuality WHERE srno=1";
	

	if($connection){

		$result1 = mysqli_query($connection,$query1); //INSERT 'parameters' table
		$result2 = mysqli_query($connection,$query2); //UPDATE 'real_time_parameters' table

		if($result1){ //If Insertion in 'parameters' table is successful
		    if($result2){ //If Insertion in 'real_time_parameters' table is successful
		    	
				updateStatusTable($temperature,
									$pressure,
									$humidity,
									$airQuality,
									$connection,
									"$day/$month/$year $hours:$minutes:$seconds"
									); //Update 'status' Table 

			    returnFlags($connection); //Return all flags as a response 
		    }
		}
		
	}


	function returnFlags($connection){
		$query = "SELECT * FROM flags";
		$result = mysqli_query($connection,$query);
		    
		if($result){ // If the flags are successfully fetched
		    $row = mysqli_fetch_array($result);
		    $sleep = $row['sleep'];
		    $terminate = $row['terminate'];
		    echo '{"sleep": "'.$sleep.'", "terminate": "'.$terminate.'"}'; //This removes erron when pasring JSON object using response.json()
		}
	}

	function isTerminated($connection){ //Checks if the terminate flag == 1, returns true if(terminate flag == 1)

		$query = "SELECT * FROM flags";
		$result = mysqli_query($connection,$query);
		    
		if($result){ // If the flags are successfully fetched
		    $row = mysqli_fetch_array($result);
		    $terminate = $row['terminate'];
		    if($terminate == 1) return true;
		}

		return false;
	}

	function updateStatusTable($temperature,$pressure,$humidity,$airQuality,$connection,$date_time){
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

		    //STEP 2.1 : Send notification to user (via Vonage API)
		    //Access the number of root user
		    $query = "SELECT * FROM users u LEFT OUTER JOIN root_users ru ON u.id = ru.id WHERE isroot=1";
			if($connection){
		    	$result = mysqli_query($connection,$query);
		    	$row = mysqli_fetch_array($result);
		    	$phone_number = "91".$row['phone_number'];
		    }

		    //Send SMS
		    if($status_overall == 1) {
		    	$basic  = new \Vonage\Client\Credentials\Basic("bbbd316d", "10ZOlPuwnL7A69oQ");
				$client = new \Vonage\Client($basic);

				//TODO : check id number is not null
				//$response = $client->sms()->send(new \Vonage\SMS\Message\SMS($phone_number, "RTEMS", "Threshold Exceeded at ".$date_time));
				//$message = $response->current();
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
