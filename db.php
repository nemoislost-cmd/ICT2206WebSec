<?php
    
    
    $host = "CHANGETOYOURS";
    $username = "CHANGETOYOURS";
    $password = "CHANGETOYOURS";
    $dbname = "CHANGETOYOURS";
    
    

// connect to the database using PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // display an error message if the connection fails
    echo "Connection failed: " . $e->getMessage();
    exit;
}

?>
