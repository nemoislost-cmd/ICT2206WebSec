<?php
    
    $servername = "CHANGETOYOURS";
    $username = "CHANGETOYOURS";
    $password = "CHANGETOYOURS";
    $dbname = "CHANGETOYOURS";
    
    

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }
