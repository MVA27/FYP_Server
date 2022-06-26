<?php
    
        class Parameters
        {
            public $day;
            public $month;
            public $year;
            public $hours;
            public $minutes;
            public $seconds;
            
            public $temperature;
            public $pressure;
            public $humidity;
            public $air_quality;
            
            public $status;
        }
        
        $parameterArray = array();
        
    
        $file = fopen("../Configuration-Files/database.config", "r") or die("Unable to open database.config file!");
        $IP_ADDRESS = chop(fgets($file));
        $USER_NAME = chop(fgets($file));
        $PASSWORD = chop(fgets($file));
        $DATABASE = chop(fgets($file));
    
        $connection = mysqli_connect($IP_ADDRESS,$USER_NAME,$PASSWORD,$DATABASE);
    
        //$query="SELECT * FROM parameters";
        $query="SELECT day,month,year,hours,minutes,seconds,p.temperature,p.pressure,p.humidity,p.air_quality,s.status FROM parameters p INNER JOIN status s ON p.srno = s.srno;";
    
        $result = mysqli_query($connection,$query);
        
        if($result)
        {
            while($row=mysqli_fetch_array($result))
            {
                $parameters = new Parameters();
                $parameters->day = $row['day'];
                $parameters->month = $row['month'];
                $parameters->year = $row['year'];
                $parameters->hours = $row['hours'];
                $parameters->minutes = $row['minutes'];
                $parameters->seconds = $row['seconds'];
                
                $parameters->temperature = $row['temperature'];
                $parameters->pressure = $row['pressure'];
                $parameters->humidity = $row['humidity'];
                $parameters->air_quality = $row['air_quality'];
                
                $parameters->status = $row['status'];
                
                array_push($parameterArray,$parameters);
            }
        }
        echo json_encode($parameterArray);
    ?>