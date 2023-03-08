<?php 

insertCaptchaData(captcha_data, captcha_timestap, $curr_device);
$mean = calculate_mean($captcha_data);
$max = calculate_max($captcha_data);
$min = calculate_min($captcha_data);
$median = calculate_median($captcha_data);

function insertCaptchaData($data,$data_timestamp,$curr_device){
    $mean = calculate_mean($data);
    $max = calculate_max($data);
    $min = calculate_min($data);
    $median = calculate_median($data);
    $margin = calculateMarginOfError($data, 0.95);
    $lower_margin = calculateLowerMargin($min, $margin); 
    $upper_margin = calculateUpperMargin($max, $margin); 
    echo $min;
    echo "<br>";
    echo $max;
     echo "<br>";
    echo $lower_margin;
     echo "<br>";
    echo $upper_margin;
     echo "<br>";
    $sd = calculate_sd($data);
    $value1 = $_SESSION["username"];
    $value2 = $data[0];
    $value3 = $data[1];
    $value4 = $data[2];
    $value5 = $data[3];
    $value6 = $data[4];
    $value7 = date('Y-m-d H:i:s',$data_timestamp/1000);
    $tempdate = strtotime($value7);
    $tempdate+= 12 * 60 * 60 ;
    $targetdate = date('Y-m-d H:i:s', $tempdate);
    $value8 = $_SESSION["time_period"];
    $value9 = $curr_device ;
    
    
    $servername = "localhost";
    $username = "admin";
    $password = "123456";
    $dbname = "reaction_time";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }


    
   $stmt1 = $conn->prepare("INSERT INTO chaptcha_testcase (username,T1,T2,T3,T4,T5,timestamp,test_period,device,futuretimestamp) VALUES (?,?,?,?,?,?,?,?,?,?)");
   $stmt1->bind_param("siiiiissss", $value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8, $value9,$targetdate);
    // Set parameters and execute
    $stmt1->execute();
$stmt2 = $conn->prepare("INSERT INTO captcha_data (username,mean,median,test_period,sd,lower,upper,margin,device,lower_margin,upper_margin) VALUES (?,?,?,?,?,?,?,?,?,?,?)");     
    $stmt2->bind_param("siisiiiisii", $value1, $mean, $median, $value8, $sd, $min, $max, $margin, $value9, $lower_margin, $upper_margin);     $stmt2->execute();
    if (isset($_SESSION["curr_device"])){
        if ($_SESSION["curr_device"] == "mouse"){
             echo '<form action="inform_change_test.php"><button type="submit" style="background-color: blue; color: white; padding: 10px;">Next</button></form>';
            
        }else{
            $_SESSION['startCountdown']=1;
            echo '<form action="index.php" onsubmit="location.reload()"><button type="submit" style="background-color: blue; color: white; padding: 10px;">Finish</button></form>';
            
            
        }
    }
    //echo '<form action="reactiontimetrackpad.php"><button type="submit" style="background-color: blue; color: white; padding: 10px;">Next</button></form>';

    
    mysqli_close($conn);
    

    
}


?>