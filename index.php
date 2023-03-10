<?php
session_start();

// Include the necessary files
require_once 'db.php';
require_once 'security-validate-sanitise.php';

// Define variable and initialize with empty value
$username = "";

// Validate username
$username = validate_username($_SESSION["username"]);

// Check if user is logged in
if (!isset($username)) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Check if user exist and get their security questions and answers
if (!isset($message["Error"]["Username"])){

    // Prepare an insert statement
    $sql = "SELECT question, answer FROM user_accounts WHERE username = :username";
    
    if ($stmt = $pdo->prepare($sql)) {
        // Bind the values to the prepared statement
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {

            // Check if username exists
            if ($stmt->rowCount() == 1) {
                //Fetch the row
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $security_question = $row["question"];
                $security_answer = $row["answer"];

                // Check if user haven't set their security question and answer
                if (empty($security_question) || empty($security_answer)) {
                    // Redirect to security question page
                    header("Location: security-question.php");
                    exit();
                }
                
            } else {
                // Display an error message if username doesn't exist
                // $_SESSION["Error"]["General"]  = "No account found with that username.";
                
                // Redirect to home page
                header("location: login.php");
                exit();
            }

        }

        // Close statement
        unset($stmt);
    }
}

// Close connection
unset($pdo);

// If logout button is clicked
if (isset($_POST["logout"])) {
    // Destroy session and redirect to login page
    session_destroy();
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home Page</title>

        <link rel="stylesheet" href="style.css" >

        <!-- Font Awesome -->
        <link
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
            rel="stylesheet"
            />
        <!-- Google Fonts -->
        <link
            href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
            rel="stylesheet"
            />
        <!-- MDB -->
        <link
            href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css"
            rel="stylesheet"
            />

    </head>
    <body>
        <style>
            h1, h2 {
                font-family: 'Roboto', sans-serif;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 2px;
                line-height: 1.2;
            }

            h1 {
                font-size: 48px;
                color: #333;
                margin-bottom: 20px;
            }

            h2 {
                font-size: 24px;
                color: #666;
                margin-bottom: 10px;
            }
        </style>
        <div class="container">
            <h1>Welcome, <?php echo $_SESSION["username"]; ?>!</h1>
            <h2> 
<?php
require_once "db_connect.php";
$daysql_color =
    "SELECT * FROM color_data WHERE username='" .
    $_SESSION["username"] .
    "' AND test_period='day' ";
$daysql_captcha =
    "SELECT * FROM captcha_data WHERE username='" .
    $_SESSION["username"] .
    "' AND test_period='day' ";
$nightsql_color =
    "SELECT * FROM color_data WHERE username='" .
    $_SESSION["username"] .
    "' AND test_period='night' ";
$nightsql_captcha =
    "SELECT * FROM captcha_data WHERE username='" .
    $_SESSION["username"] .
    "' AND test_period='night' ";
$dayresult_color = mysqli_query($conn, $daysql_color);
$nightresult_color = mysqli_query($conn, $nightsql_color);
$dayresult_captcha = mysqli_query($conn, $daysql_captcha);
$nightresult_captcha = mysqli_query($conn, $nightsql_captcha);
$currentHour = date("H");
if (
    mysqli_num_rows($dayresult_color) == 0 &&
    mysqli_num_rows($dayresult_captcha) == 0
) {
    $_SESSION["day_records"] = 0;
    echo "DAY RECORDS NOT FOUND. <br>";
} else {
    $_SESSION["day_records"] = 1;
    echo "DAY RECORDS PRESENT <br>";
}

if (
    mysqli_num_rows($nightresult_color) == 0 &&
    mysqli_num_rows($nightresult_captcha) == 0
) {
    $_SESSION["night_records"] = 0;
    echo "NIGHT RECORDS NOT FOUND <br>";
} else {
    $_SESSION["night_records"] = 1;
    echo "NIGHT RECORDS PRESENT <br>";
}
echo "Day Period is from 7am to 7pm <br>";
echo "Night Period is from 7pm to 7am <br>";

if ($currentHour >= 7 && $currentHour < 19) {
    echo "Currently in Day Period.   <br> ";
    $_SESSION["time_period"] = "day";
} else {
    echo "Currently Night Period. <br> ";
    $_SESSION["time_period"] = "night";
}
$query_countdown =
    "SELECT futuretimestamp FROM color_testcase WHERE username='" .
    $_SESSION["username"] .
    "' AND device='trackpad'";
$result_countdown = mysqli_query($conn, $query_countdown);
if (mysqli_num_rows($result_countdown) > 0) {
    while ($row = mysqli_fetch_assoc($result_countdown)) {
        $_SESSION["target_date"] = $row["futuretimestamp"];
        // Format the date and time
$formattedDate = date("M j, Y g:i A", strtotime($_SESSION["target_date"]));
$currTime = date("M j, Y g:i A");
if (strtotime($formattedDate) < strtotime($currTime)) {
    $_SESSION['countdownover']=1;
} else {
    $_SESSION['countdownover']=0;
    // do something else
}

if ($_SESSION['day_records'] ==1){
    if ($_SESSION['night_records'] ==0){
        if ($_SESSION['countdownover']==1){ # day records exist night records dont countdown is ovver
            $_SESSION["nightnotdone"] = 1; // NIGHT RECORDS IS NOT DONE
            $_SESSION["daynotdone"] = 0; // DAY RECORDS IS DONE
        }else{
            $_SESSION['countdownNight'] = 1;                                    # day records exist night records dont countdown is still running
             $message = "Day period will be available after $formattedDate";
             echo $message;
            
        }
        
    }else{
        $_SESSION["nightnotdone"] = 0; #both day and night records exist
        $_SESSION["daynotdone"] = 0; #both day and night records exist
    }
}else{
     if ($_SESSION['night_records'] == 1){ 
         if ($_SESSION['countdownover']==1){ # day records dont exists night records exist countdown is over
            $_SESSION["nightnotdone"] = 0; // NIGHT RECORDS IS DONE
            $_SESSION["daynotdone"] = 1; // DAY RECORDS IS NOT DONE
         }else{
              $_SESSION['countdownDay'] = 1;
              $message = "Day period will be available after $formattedDate";   
              echo $message ;                  # day records do not exist night records exist countdown is still running 
         }
            
     }else{
         $_SESSION["nightnotdone"] = 1;
         $_SESSION["daytnotdone"] = 1;
     }
}
        // Access other columns as needed
    }
} else {
    $_SESSION["daynotdone"]=1;
    $_SESSION["nightnotdone"]=1;
    
    
}



    
    
    


?>

            </h2>
            <div class="logout-container">
                <form method="post">
                    <button type="submit" name="logout" class="btn btn-default btn-sm pull-right">Log out</button>
                </form>
            </div>
        </div>
        <style>
            .button-container {
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
                width: 80%;
            }

            .image-button1,
            .image-button2 {
                display: inline-block;
                vertical-align: top;
                margin-right: 15px;
                border: 1px solid black;
                border-radius: 4px;
                overflow: hidden;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            }

            .image-button1 img,
            .image-button2 img {
                display: block;
                width: 100%;
                height: auto;
            }

            .image-button1:hover,
            .image-button2:hover {
                transform: translateY(-2px);
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
                transition: transform 0.3s, box-shadow 0.3s;
            }

            .countdown-box {
                position: fixed;
                top: 10px;
                right: 10px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .countdown-box > div {
                margin-right: 10px;
                text-align: center;
            }

            .countdown-box > div:last-child {
                margin-right: 0;
            }

            .countdown-box .number {
                background-color: #333;
                color: #fff;
                padding: 8px;
                border-radius: 50%;
                box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.5);
                font-size: 16px;
                width: 30px;
                height: 30px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .countdown-box .label {
                margin-top: 5px;
                text-transform: uppercase;
                font-size: 10px;
                color: #333;
                letter-spacing: 1px;
            }

            .logout-container {
                position: fixed;
                top: 60px;
                right: 10px;
            }

            .btn-logout {
                background-color: #FF0000;
                border: 2px solid #333;
                border-radius: 20px;
                color: #333;
                padding: 8px 20px;
                font-size: 14px;
                font-weight: bold;
                text-transform: uppercase;
                transition: background-color 0.2s ease, color 0.2s ease;
            }

            .btn-logout:hover {
                background-color: #333;
                color: #fff;
                cursor: pointer;
            }

        </style>
        <div class ="button-container">
            <button class="image-button1" onclick="checkSessionDay()">
                <img src="images/2.png">
            </button>

            <button class="image-button2" onclick="checkSessionNight()">
                <img src="images/1.png">
            </button>      
        </div>
        <div class="countdown-box">
            <div class="hours">
                <div class="number"></div>
                <div class="label">HOURS</div>
            </div>
            <div class="mins">
                <div class="number"></div>
                <div class="label">MINS</div>
            </div>
            <div class="secs">
                <div class="number"></div>
                <div class="label">SECS</div>
            </div>
        </div>
        <div class="target-date"></div>
        <form id="reminderForm" method="post" action="reminderMail.php" style="display:none;">
        </form>





        <script>

            //const targetDateElem = document.querySelector(".target-date");
function checkSessionDay() {
    var curr_period = '<?= $_SESSION["time_period"] ?>';
    var day_records = '<?= $_SESSION["day_records"] ?>';
    var day_countdown = '<?= $_SESSION["daynotdone"] ?>';
    var countdownStatus = '<?= $_SESSION["countdownover"] ?>';

    if (curr_period === "day" && day_records === '0') {
        window.location.href = 'captcha_challenge.php';
    } else if (day_records === '1') {
        alert("403 Forbidden. Records already exist!");
    } else if (day_countdown === '1' && countdownStatus !== '1') {
        alert("403 Forbidden. Wait for the countdown to finish to access this resource!");
    } else if (day_countdown === '0' && countdownStatus === '1') {
        alert("403 Forbidden. Countdown is over but current time does not allow for access to this resource!");
    }
    else {
        alert("403 Forbidden. This resource cannot be accessed at this time!");
    }
}

function checkSessionNight() {
    var curr_period = '<?= $_SESSION["time_period"] ?>';
    var night_records = '<?= $_SESSION["night_records"] ?>';
    var night_countdown = '<?= $_SESSION["nightnotdone"] ?>';
   var countdownStatus = '<?= $_SESSION["countdownover"] ?>';
    if (curr_period === "night" && night_records === '0') {
        window.location.href = 'captcha_challenge.php';
    } else if (night_records === '1') {
        alert("403 Forbidden. Records already exist!");
    } else if (night_countdown === '1' && countdownStatus !== '1') {
        alert("403 Forbidden. Wait for the countdown to finish to access this resource!");
    } else if (night_countdown === '1' && countdownStatus === '1') {
        alert("403 Forbidden. Countdown is over but current time does not allow for access to this resource!");
    }
    else {
        alert("403 Forbidden. This resource cannot be accessed at this time!");
    }
}


            var hoursElement = document.querySelector('.hours .number');
            var minsElement = document.querySelector('.mins .number');
            var secsElement = document.querySelector('.secs .number');
            function sendCountdownReminderEmail(){
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        console.log("email sent");
                        var response = xhr.responseText;
                        console.log(response);
                        
                    }
              };
              xhr.open('GET', 'reminderMail.php');
              xhr.send();
          }
            // Make an AJAX request to the PHP script to get the remaining time
            function updateCountdown() {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        var response = JSON.parse(this.responseText);
                        console.log(response);
                        var remainingTime = response.remainingTime;
                        // Update the countdown timer based on the remaining time
                        if (remainingTime <= 0) {
                            hoursElement.textContent = '00';
                            minsElement.textContent = '00';
                            secsElement.textContent = '00';
                            
                            sendCountdownReminderEmail();
                            
                        } else {
                            var hours = Math.floor(remainingTime / 3600);
                            var mins = Math.floor((remainingTime % 3600) / 60);
                            var secs = remainingTime % 60;

                            hoursElement.textContent = hours < 10 ? '0' + hours : hours;
                            minsElement.textContent = mins < 10 ? '0' + mins : mins;
                            secsElement.textContent = secs < 10 ? '0' + secs : secs;

                            // Schedule the next update in 1 second
                            setTimeout(updateCountdown, 1000);
                        }
                    }
                };
                xhr.open('GET', 'countdown.php');
                xhr.send();
            }

            // Call the updateCountdown function to start the countdown timer
            updateCountdown();

        </script>

    </body>
</html>