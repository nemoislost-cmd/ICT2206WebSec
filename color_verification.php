<?php
$lower = $upper = $errorMsg = "";
$color_time = 0;
$success = true;
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data = json_decode($_REQUEST["data"], true);
  $color_data = $data;
}

function getColorData()
{
    global $lower, $upper, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT lower_margin, upper_margin
            FROM color_data 
            WHERE username=?
            AND device=? 
            AND test_period=?");
        // Bind & execute the query statement:
        $stmt->bind_param("sss", $_SESSION["username"], $_SESSION["device"], $_SESSION["period"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 2)
        {
            $row = $result->fetch_assoc();
            $lower = $row["lower_margin"];
            $upper = $row["upper_margin"];
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
    global $color_data, $lower, $upper, $errorMsg, $success;
    if (($lower < $color_data) && ($color_data < $upper)) {
        $_SESSION["result"]= "pass";
    }
    else {
        $_SESSION["result"]= "fail";
    }
    $_SESSION["color_time"] = $color_data;
}

getColorData();
checkMarginRange();
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
                echo "<h2>Verification successful!</h2>";
                echo "<h4>Move along.</h4>";
            }
            else
            {
                
                echo "<h2>Verification failure!</h2>";
                echo "<h4>Move along.</h4>";
            }
            ?>
              <div class="text-center mt-4 pt-1 pb-1">
                    <a href='security.php' class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" >Proceed to next step!</a>
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
