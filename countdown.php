<?php
session_start();

if (isset($_SESSION['countdownDay']) || isset($_SESSION['countdownNight'])){
    if ($_SESSION['countdownDay'] ===1 || $_SESSION['countdownNight'] === 1  ){
        $countdownDate=$_SESSION['target_date'];
$convertedUnixTime = strtotime($countdownDate);
$remainingTime = $convertedUnixTime - time();

// Return the remaining time in JSON format
header('Content-Type: application/json');
echo json_encode(['remainingTime' => $remainingTime]);
        
        
    }
    
}




        




