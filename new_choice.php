<?php
session_start();
$success = true;

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: new_login.php");
    exit();
}

// Get user's security question and answer from database
$username = $_SESSION["username"];

function checkExistingData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1)
        {
            $errorMsg = "MultiAuth data not found!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkDayData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.test_period='day'
            AND captcha_data.test_period='day'
            AND color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1)
        {
            $errorMsg = "Day data not found!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkNightData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.test_period='night'
            AND captcha_data.test_period='night'
            AND color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1)
        {
            $errorMsg = "Night data not found!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkTrackpadData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.device='trackpad'
            AND captcha_data.device='trackpad'
            AND color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 2)
        {
            $errorMsg = "Trackpad data incomplete!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkMouseData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.device='mouse'
            AND captcha_data.device='mouse'
            AND color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 2)
        {
            $errorMsg = "Mouse data incomplete!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkPeriod(){
    $currentHour = date("H");
    if ($currentHour >= 7 && $currentHour < 19) {
        $_SESSION['period'] = "day";
    } 
    else {
        $_SESSION['period'] = "night";
    } 
}

checkExistingData();
checkDayData();
checkNightData();
checkTrackpadData();
checkMouseData();
checkPeriod();
?>

<!DOCTYPE html>
<html lang="en">
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
                echo "<h2>Login successful!</h2>";
                echo "<h4>Welcome back, " . $_SESSION["name"] . ".</h4>";
                echo "<h4>Choose either trackpad or mouse for your next step.</h4>";?>
                <div class="text-center mt-4 pt-1 pb-1">
                    <a href='new_trackpad.php' class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" >Trackpad</a>
              </div>
              <div class="text-center mt-4 pt-1 pb-1">
                    <a href='new_mouse.php' class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" >Mouse</a>
              </div>
                <?php
            }
            else
            {
                echo "<h2>Oops!</h2>";
                echo "<h4>The following errors were detected:</h4>";
                echo "<p>" . $errorMsg . "</p>";?>          
            <?php
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