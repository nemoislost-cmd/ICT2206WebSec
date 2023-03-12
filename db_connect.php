<?php
    
    $servername = "localhost";
    $username = "admin";
    $password = "123456";
    $dbname = "reaction_time";
    
    /*
    $host = "localhost"; // database hostname
    $dbname = "authreact"; // database name
    $username = "authreact_dba"; // database username
    $password = "Adapt_2_survive"; // database password
    */
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }