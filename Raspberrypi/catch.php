<?php
    $temperature = $_REQUEST['t'];
    $pressure = $_REQUEST['p'];
    $humidity = $_REQUEST['h'];
    $airQuality = $_REQUEST['a'];
	
	$connection = mysqli_connect("localhost","id18875890_admin","/%|h&cw<k8S4hqBQ","id18875890_project4y");
	
	$query = "INSERT INTO parameters(temperature,pressure,humidity,air_quality) VALUES ($temperature,$pressure,$humidity,$airQuality)";
	
	if($connection){
		$result = mysqli_query($connection,$query);
	}
	else{
		echo "error";
	}
?>