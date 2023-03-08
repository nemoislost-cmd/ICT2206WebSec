<?php

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