<?php


session_start();

if (isset($_SESSION['startCountdown'])){
    if ($_SESSION['startCountdown']==1){
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

    $currentUser = $_SESSION['username'];
    $query = "SELECT futuretimestamp FROM color_testcase WHERE username = '$currentUser' AND device = 'trackpad'";
    $result = mysqli_query($conn, $query);
    // Check if the query was successful and the countdownDate value was retrieved
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $countdownDate = $row['futuretimestamp'];
        $_SESSION['target_date']= $row['futuretimestamp'];
        
        } else {
       // Handle the case where the countdownDate value was not found
        }   //$countdownDate = time();
// Calculate the remaining time
$convertedUnixTime = strtotime($countdownDate);
$remainingTime = $convertedUnixTime - time();

// Return the remaining time in JSON format
header('Content-Type: application/json');
echo json_encode(['remainingTime' => $remainingTime]);
        
    }
}


