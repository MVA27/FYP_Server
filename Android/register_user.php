<?php
    //https://project4y.000webhostapp.com/Android/register_user.php?firstName=noob&lastName=singh&userName=noobie&userPassword=123&age=10&phoneNumber=199999
    if(count($_REQUEST) == 0){
	    http_response_code(400);
	    exit(0);
	}
	
	$first_name = $_REQUEST['firstName'];
	$last_name = $_REQUEST['lastName'];
    $user_name = $_REQUEST['userName'];
    $user_password = $_REQUEST['userPassword'];

    $user_password = sha1($user_password);

    $age = $_REQUEST['age'];
    $phone_number = $_REQUEST['phoneNumber'];
    
	$file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
    $IP_ADDRESS = chop(fgets($file));
    $USER_NAME = chop(fgets($file));
    $PASSWORD = chop(fgets($file));
    $DATABASE = chop(fgets($file));

	$connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);

	$query = "INSERT INTO users(first_name,last_name,user_name,password,age,phone_number) VALUES ('$first_name','$last_name','$user_name','$user_password',$age,'$phone_number')";

	$result = mysqli_query($connection,$query);
	
	if($result){
		http_response_code(200);
	}
	else{
	    http_response_code(400);
	}
?>