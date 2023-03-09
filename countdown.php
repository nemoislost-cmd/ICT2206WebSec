<?php


session_start();






        $countdownDate=$_SESSION['target_date'];
        $convertedUnixTime = strtotime($countdownDate);
        $remainingTime = $convertedUnixTime - time();

// Return the remaining time in JSON format
header('Content-Type: application/json');
echo json_encode(['remainingTime' => $remainingTime]);
        




