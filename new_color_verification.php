<?php
$lower = $upper = $margin = $new_lower = $new_upper = $errorMsg = "";
$color_time = 0;
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data = json_decode($_REQUEST["data"], true);
  $color_data = $data;
}

function getColorData()
{
    global $lower, $upper, $margin, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT lower, upper, margin 
            FROM color_data 
            WHERE username=?
            AND device=? 
            AND test_period=?");
        // Bind & execute the query statement:
        $stmt->bind_param("sss", $_SESSION["username"], $_SESSION["device"], $_SESSION["period"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $lower = $row["lower"];
            $upper = $row["upper"];
            $margin = $row["margin"];
            $_SESSION["lower"]= $lower;
            $_SESSION["upper"]= $upper;
            $_SESSION["margin"]= $margin;
        }
        else
        {
            $errorMsg = "Error with getting the color data";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkMarginRange(){
    global $color_data, $new_lower, $new_upper, $errorMsg, $success;
    $new_lower = $_SESSION["lower"] - ($_SESSION["margin"] / 100 * $_SESSION["lower"]);
    $new_upper = $_SESSION["upper"] + ($_SESSION["margin"] / 100 * $_SESSION["upper"]);
    if (($new_lower < $color_data) && ($color_data < $new_upper)) {
        $_SESSION["color_time"] = $color_data;
        $_SESSION["result"]= "pass";
    }
    else {
        $errorMsg = "Not within acceptable range.";
        $success = false;
        $_SESSION["color_time"] = $color_data;
        $_SESSION["result"]= "fail";
    }
}

getColorData();
checkMarginRange();
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
                echo "<h2>Verification successful!</h2>";
                echo "<h4>It's within acceptable range.</h4>";
            }
            else
            {
                echo "<h2>Oops!</h2>";
                echo "<h4>The following errors were detected:</h4>";
                echo "<p>" . $errorMsg . "</p>";
            }
            ?>
              <div class="text-center mt-4 pt-1 pb-1">
                    <a href='new_security.php' class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" >Proceed to next step!</a>
              </div>
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
