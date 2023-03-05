<?php
$username = $answer = $errorMsg = "";
$success = true;
session_start();

if (!empty($_POST["ans"]))
{
    $answer = sanitize_input($_POST["ans"]);
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function saveToDB()
{
    global $answer, $errorMsg, $success;
    // Create database connection.
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    // Check connection
    if ($conn->connect_error)
    {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    }
    else
    {
        $timestamp = date('Y-m-d H:i:s');
        $result = "test";
        $captcha = 0.5;
        $color = 0.5;
        // Prepare the statement:
        $stmt = $conn->prepare("INSERT INTO login_history (username, timestamp, 
        device, result, answer, color_time, captcha_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        // Bind & execute the query statement:
        $stmt->bind_param("sssssii", $_SESSION["username"], $timestamp, $_SESSION["device"], $result, $answer, $captcha, $color);
        if (!$stmt->execute())
        {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        }        
        $stmt->close();
    }
    $conn->close();
}

saveToDB();
?>
<html>
    <head>
        <?php
            include "header.php";
        ?>
    </head>
    <body>
        <main class="container">
            <div class="bgimg-1 w3-display-container w3-opacity-min">
  <div class="w3-display-middle" style="white-space:nowrap;">
     <span class="w3-center w3-padding-large w3-xlarge w3-wide">
            <hr>
            <?php
            if ($success)
            {
                echo "<h2>Thank you!</h2>";
                echo "<h4>You have completed the procedure, " . $_SESSION["name"] . ".</h4>";
                echo "<a href='logout.php' class='btn btn-success'>Proceed to Logout</a>";
                echo "<br>";
            }
            else
            {
                echo "<h2>Oops!</h2>";
                echo "<h4>The following errors were detected:</h4>";
                echo "<p>" . $errorMsg . "</p>";
                echo "<a href='logout.php' class='btn btn-warning'>Proceed to Logout</a>";
                echo "<br>";
            }
            ?>
            </span>
            </div>
            </div>
            <hr>
        </main>
        <?php
            include "footer.php";
        ?>
    </body>
</html>
