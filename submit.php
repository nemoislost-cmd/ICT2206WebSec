<?php
$username = $answer = $new_answer = $errorMsg = "";
$success = true;
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

if (!empty($_POST["answer"]))
{
    $new_answer = sanitize_input($_POST["answer"]);
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function checkUserData()
{
    global $username, $answer, $new_answer, $errorMsg, $success;
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
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT answer 
            FROM user_accounts 
            WHERE username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $_SESSION["username"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $answer = $row["answer"];
            if ($answer != $new_answer)
            {
                $_SESSION["intended_user"]= "no";
            }
            else
            {
                $_SESSION["intended_user"]= "yes";
            }
        }
        else
        {
            $errorMsg = "User data not found!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function saveToDB()
{
    global $new_answer, $errorMsg, $success;
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
        device, test_period, color_time, captcha_time, answer, result, intended_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Bind & execute the query statement:
        $stmt->bind_param("ssssiisss", $_SESSION["username"], $timestamp, $_SESSION["device"], $_SESSION["period"], $_SESSION["color_time"], $captcha, $new_answer, $result, $_SESSION["intended_user"]);
        if (!$stmt->execute())
        {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        }        
        $stmt->close();
    }
    $conn->close();
}

checkUserData();
saveToDB();
?>
<html>
    <head>
        <?php
            include "header.php";
        ?>
    </head>
    <body>
        <?php
        include "nav.php";
        ?>
        <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
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
