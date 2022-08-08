<?php
    if(count($_REQUEST) == 0){
	    http_response_code(400);
	    exit(0);
	}   
	
	class Users
	{
	    public $id;
	    public $first_name;
	    public $last_name;
	    public $user_name;
	    public $age;
	    public $phone_number;
	    public $isroot;
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
	
	if($result){
		$row = mysqli_fetch_array($result);
		
		$db_password = $row['password'];
		
		if($db_password ==  sha1($user_password)){
			fetchAndReturnUserDetails($connection,$user_name);
		    http_response_code(200);
		}
		else{
		    http_response_code(400);
		}
	}
	
	function fetchAndReturnUserDetails($connection,$user_name){
		 
		$query = "SELECT u.id,u.first_name,u.last_name,u.user_name,u.age,u.phone_number,ru.isroot FROM users u LEFT OUTER JOIN root_users ru ON u.id = ru.id WHERE user_name='$user_name'";
		$result = mysqli_query($connection,$query);

		if($result){

			$row=mysqli_fetch_array($result);
			$users = new Users();
			$users->id = $row['id'];
			$users->first_name = $row['first_name'];
			$users->last_name = $row['last_name'];
			$users->user_name = $row['user_name'];
			$users->age = $row['age'];
			$users->phone_number = $row['phone_number'];
			if($row['isroot'] == 1) $users->isroot = true;
			else $users->isroot = false;

			echo json_encode($users);
		}
	}
?>