<?php
$color_lower = $color_upper = $captcha_lower = $captcha_upper = $errorMsg = "";
$color_time = $color_result = $captcha_result = 0;
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
  // Preparing data set for result for image verification
  $captcha_data = $_SESSION["captcha_data"];
}

function getColorData(){
    global $color_lower, $color_upper, $errorMsg, $success;
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    if ($conn->connect_error){
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT lower_margin, upper_margin
            FROM color_data 
            WHERE username=?
            AND device=? 
            AND test_period=?");
        $stmt->bind_param("sss", $_SESSION["username"], $_SESSION["device"], $_SESSION["period"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $color_lower = $row["lower_margin"];
            $color_upper = $row["upper_margin"];
        } else {
            $errorMsg = "Error with getting the color data";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function getCaptchaData(){
    global $captcha_lower, $captcha_upper, $errorMsg, $success;
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else{
        $stmt = $conn->prepare("SELECT lower_margin, upper_margin
            FROM captcha_data 
            WHERE username=?
            AND device=? 
            AND test_period=?");
        $stmt->bind_param("sss", $_SESSION["username"], $_SESSION["device"], $_SESSION["period"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $captcha_lower = $row["lower_margin"];
            $captcha_upper = $row["upper_margin"];
        } else {
            $errorMsg = "Error with getting the captcha data";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkMarginRange(){
    global $color_data, $color_lower, $color_upper, $captcha_data, $captcha_lower, $captcha_upper;
    if (($color_lower < $color_data) && ($color_data < $color_upper) && 
            ($captcha_lower < $captcha_data) && ($captcha_data < $captcha_upper)) {
        $_SESSION["result"]= "pass";
        $_SESSION["color_result"]= 1;
        $_SESSION["captcha_result"]= 1;
    }
    else {
        $_SESSION["result"]= "fail";
        if (($color_lower < $color_data) && ($color_data < $color_upper)) {
            $_SESSION["color_result"]= 1;
        }
        else {
            $_SESSION["color_result"]= 0;
        }
        if (($captcha_lower < $captcha_data) && ($captcha_data < $captcha_upper)) {
            $_SESSION["captcha_result"]= 1;
        }
        else {
            $_SESSION["captcha_result"]= 0;
        }
    }
    $_SESSION["color_time"] = $color_data;
    $_SESSION["captcha_time"] = $captcha_data;
}

function saveToDB(){
    global $errorMsg, $success;
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    if ($conn->connect_error){
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $username = $_SESSION["username"];
        $timestamp = date('Y-m-d H:i:s');
        $device = $_SESSION["device"];
        $period = $_SESSION["period"];
        $color_time = $_SESSION["color_time"];
        $color_result = $_SESSION["color_result"];
        $captcha_time = $_SESSION["captcha_time"];
        $captcha_result = $_SESSION["captcha_result"];
        $result = $_SESSION["result"];
        $answer = $_SESSION["answer"];
        $intended_user = $_SESSION["intended_user"];
        $stmt = $conn->prepare("INSERT INTO login_history (username, timestamp, 
        device, test_period, color_time, color_result, captcha_time, captcha_result, result, answer, intended_user) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiiiisss", $username, $timestamp, $device, $period, $color_time, $color_result, $captcha_time, $captcha_result, $result, $answer, $intended_user);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        }        
        $stmt->close();
    }
    $conn->close();
}

getColorData();
getCaptchaData();
checkMarginRange();
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
            if ($_SESSION["intended_user"] == 'yes' && $_SESSION["result"] =='pass')  {
                echo "<h2>Thank you!</h2>";
                echo "<h4>You have completed the procedure</h4>";
                ?>
                <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Secret Site:</p>
                    <a role="button" class="btn btn-outline-danger" href="landingpage.php">Enter</a>
                </div>
              
            <?php }
            else  {
                echo "<h2>Thank you!</h2>";
                echo "<h4>You have completed the procedure</h4>";
            }
            ?>
        </div>
      </div>
    </div>
  </div>
        <?php
            include "footer.php";
        ?>
    </body>
</html>
