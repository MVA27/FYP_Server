<?php
    if(count($_REQUEST) == 0){
	    http_response_code(400);
	    exit(0);
	}
	
    $user_name = $_REQUEST['userName'];
    $user_password = $_REQUEST['userPassword'];
    
	$file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));

	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);

	$query = "SELECT * FROM users where user_name='$user_name'";

	$result = mysqli_query($connection,$query);
	
	if($result)
	{
		$row = mysqli_fetch_array($result);
		
		$db_password = $row['password'];
		
		if($db_password ==  sha1($user_password)){
		    http_response_code(200);
		}
		else{
		    http_response_code(400);
		}
	}
?>