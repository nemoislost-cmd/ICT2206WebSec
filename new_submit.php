<?php
$username = $answer = $errorMsg = "";
$success = true;
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: new_login.php");
    exit();
}

// Get user's security question and answer from database
$username = $_SESSION["username"];

if (!empty($_POST["answer"]))
{
    $answer = sanitize_input($_POST["answer"]);
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
        $result = $_SESSION["result"];
        $captcha = 0;
        // Prepare the statement:
        $stmt = $conn->prepare("INSERT INTO login_history (username, timestamp, 
        device, test_period, color_time, captcha_time, answer, result) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        // Bind & execute the query statement:
        $stmt->bind_param("ssssiiss", $_SESSION["username"], $timestamp, $_SESSION["device"], $_SESSION["period"], $_SESSION["color_time"], $captcha, $answer, $result);
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
            include "new_header.php";
        ?>
    </head>
    <body>
        <?php
        include "new_nav.php";
        ?>
        <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header">
            <h4 class="mb-0">Hello</h4>
          </div>
          <div class="card-body">
            <?php
            if ($success)
            {
                echo "<h2>Thank you!</h2>";
                echo "<h4>You have completed the procedure, " . $_SESSION["name"] . ".</h4>";
            }
            else
            {
                echo "<h2>Oops!</h2>";
                echo "<h4>The following errors were detected:</h4>";
                echo "<p>" . $errorMsg . "</p>";
            }
            ?>
            </div>
        </div>
      </div>
    </div>
  </div>
        <?php
            include "footer.php";
        ?>
    </body>
</html>
