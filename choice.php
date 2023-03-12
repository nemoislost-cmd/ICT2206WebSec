<?php
$answer = $new_answer = $errorMsg = "";
session_start();
$success = true;

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

if (!empty($_POST["answer"])){
    $new_answer = sanitize_input($_POST["answer"]);
    $_SESSION["answer"] = $new_answer;
}

function sanitize_input($user_answer){
    $user_answer = strtolower(trim(preg_replace('/\s+/', ' ', $user_answer)));
    $user_answer = stripslashes($user_answer);
    $user_answer = htmlspecialchars($user_answer);
    return $user_answer;
}

function checkUserData() {
    global $username, $answer, $new_answer, $errorMsg, $success;
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT answer FROM user_accounts WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $answer = $row["answer"];
            if (preg_match('/^[a-zA-Z\s]+$/', $new_answer)) {
                // Compare the sanitized user input with the stored security answer using a case-insensitive comparison.
                if (strcasecmp($new_answer, $answer) == 0) {
                    // Answer is correct.
                    // Do something here, like redirecting to a success page or setting a session variable.
                    $_SESSION["intended_user"] = "yes";
                } else {
                    // Answer is incorrect.
                    // Do something here, like displaying an error message or redirecting to a failure page.
                    $_SESSION["intended_user"] = "no";
                }
            } else {
                // Invalid input, do something here
                $_SESSION["intended_user"] = "no";
            }
        } else {
            $errorMsg = "User data not found!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}


function checkDayData(){
    global $username, $errorMsg, $success;
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    // Check connection
    if ($conn->connect_error){
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT captcha_data.id, color_data.id 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.test_period='day'
            AND captcha_data.test_period='day'
            AND color_data.username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 4) {
            $errorMsg = "Test data incomplete!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkNightData(){
    global $username, $errorMsg, $success;
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT captcha_data.id, color_data.id 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.test_period='night'
            AND captcha_data.test_period='night'
            AND color_data.username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 4) {
            $errorMsg = "Test data incomplete!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkTrackpadData(){
    global $username, $errorMsg, $success;
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    if ($conn->connect_error){
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT captcha_data.id, color_data.id 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.device='trackpad'
            AND captcha_data.device='trackpad'
            AND color_data.username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 4) {
            $errorMsg = "Test data incomplete!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkMouseData(){
    global $username, $errorMsg, $success;
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else{
        $stmt = $conn->prepare("SELECT captcha_data.id, color_data.id 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.device='mouse'
            AND captcha_data.device='mouse'
            AND color_data.username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 4) {
            $errorMsg = "Test data incomplete!";
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
    } else {
        $_SESSION['period'] = "night";
    } 
}

checkUserData();
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
            if ($success) {
                echo "<h4>Choose either trackpad or mouse for your next step.</h4>";?>
                <div class="text-center mt-4 pt-1 pb-1">
                    <a href='trackpad.php' class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" >Trackpad</a>
              </div>
              <div class="text-center mt-4 pt-1 pb-1">
                    <a href='mouse.php' class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" >Mouse</a>
              </div>
                <?php
            } else {
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
