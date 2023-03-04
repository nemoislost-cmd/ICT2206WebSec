<<<<<<< Updated upstream
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reaction Time Test</title>
        <style>
            #box {
                width: 800px;
                height: 600px;
                background-color: blue;
                margin: auto;
                margin-top : 50px;
            }
            
            #text-container {
                position: relative;
                top: 50%;
                transform: translateY(-50%);
                text-align: center;
            }
            
            #test-text {
                font-size:20px;
                color:white;
            }
            
            .counter {
                font-size: 20px;
                margin-top: 20px;
                text-align: center;
                color: black;
                display: inline-block;
                margin-right: 20px;
            }
            
            #reactiondiv {
                display: inline-block;
            }
            
            #reactiontime {
                font-size: 20px;
                text-align: center;
                color: black;
                display: inline-block;
            }
            
            .button {
                padding: 15px 30px;
                background-color: #3498db;
                color: #fff;
                border-radius: 5px;
                font-size: 18px;
                cursor: pointer;
                transition: background-color 0.2s ease-in-out;
            }
            
            .button:disabled{
                background-color: #ccc;
                cursor : not-allowed;
            }
            
            .buttondiv{
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
            }

            .button:hover {
                background-color: #2980b9;
            }
            
            #text1{
                font-size : 20px;
                color : white;
                
            }
        </style>
    </head>
    <body>
        <div id="box" onclick="startTest()">
            <div id="text-container">
                <p id="test-text">Human Reaction Time Test. <br> 
                    When the red box turns green, click as quickly as you can within the box. <br>
                    Click anywhere within the rectangle to get started!</p>
            </div>
        </div>
        <div>
            <p class ="counter">Experiment Statistic</p>
            <div id ="counterdiv">
             <p class="counter" id="counter">0</p>   
            </div>
            <div id ="reactiondiv">
                <p id ="reactiontime">0ms</p> 
            </div>
        </div>
        <div class="buttondiv">
            <form id ="color-data" action="loading.php" method ="post" onsubmit ="return sendData(event)">
            <input type ="hidden" name="data" id="data" value="">
            <input type ="hidden" name="timestamp" id="timestamp" value="">
            <input type ="hidden" name="device" id="device" value="">
            <button class="button" onclick="sendData(event)" type="submit">Finish</button>
            </form>
        </div>
       
        <script>
            var counter= 0;
            var data = [];
            
            
            function resetState(counter,reactionTime){
                
                 var box = document.getElementById("box");
                 var textContainer = document.getElementById("text-container");
                 var counterdiv = document.getElementById("counterdiv");
                 var reactiondiv = document.getElementById("reactiondiv");
              
                 box.style.backgroundColor = "blue";
                 textContainer.innerHTML = "<p id='text'>Good Job! Click to start again!</p>";
                 counterdiv.innerHTML = "<p id = 'counter' >Test Number "+counter+" </p>";
                 reactiondiv.innerHTML = "<p id ='reactiontime' >"+reactionTime+" ms </p>";
                 box.onclick = function(){
                     startTest();
                 };
             }
             
             function sendData(event){
                 
                 event.preventDefault();
                 var dataInput = document.getElementById("data");
                 dataInput.value = JSON.stringify(data);
                 var currTime = Date.now();
                 var timestamp = document.getElementById("timestamp");
                 timestamp.value = currTime;
                 var device = document.getElementById("device");
                 device.value = "mouse";
                 document.getElementById("color-data").submit();
                 
             };
             function setFinishState(counter,reactionTime){
                
                 var box = document.getElementById("box");
                 var textContainer = document.getElementById("text-container");
                 var counterdiv = document.getElementById("counterdiv");
                 var reactiondiv = document.getElementById("reactiondiv");
                 box.style.backgroundColor = "pink";
                 textContainer.innerHTML = "<p id='text1'>Test has concluded. Click Finish to continue!</p>";
                 counterdiv.innerHTML = "<p id = 'counter' >Test Number "+counter+" </p>";
                 reactiondiv.innerHTML = "<p id ='reactiontime' >"+reactionTime+" ms </p>";
                 console.log(data);
             
             
            };
                 //var button = document.querySelector("#buttondiv button");
                 //button.disabled = false;             
            

            function startTest() {
                var box = document.getElementById("box");
                box.style.backgroundColor = "red";
                var textContainer = document.getElementById("text-container");
               textContainer.innerHTML = "<p id='text'>Wait for it.. Click anywhere in the box when it turns green</p>";
                
                var time = Math.random() * 6000 + 1000;
                setTimeout(function() {
                    box.style.backgroundColor = "green";
                    var startTime = Date.now();
                     box.onclick = function() {
                         var endTime = Date.now();
                         var reactionTime = endTime - startTime;
                         counter++;
                         if (counter <= 4){
                             console.log("working");
                             data.push(reactionTime);
                             //setFinishState(counter,reactionTime);
                         }else if (counter ===5) {
                             data.push(reactionTime);
                             setFinishState(counter,reactionTime);
                         }
                         resetState(counter,reactionTime);
                         
                     };
                }, time);
            }
        </script>
    </body>
</html>
=======
<?php
session_start();

// Include the database configuration file
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Get user's security question and answer from database
$username = $_SESSION["username"];
$sql = "SELECT question, answer FROM user_accounts WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":username", $username, PDO::PARAM_STR);

$stmt->execute();

// Check if username exists
if ($stmt->rowCount() == 1){
    //Fetch the row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $security_question = $row["question"];
    $security_answer = $row["answer"];

    // Check if user has already set a security question and answer
    if (!$security_question || !$security_answer) {
        // Redirect to security question page
        header("Location: security-question.php");
        exit();
    }

}else {
    // Display an error message if username doesn't exist
    $username_err = "No account found with that username.";
    
    // Redirect to home page
    header("location: login.php");
    exit();
}

// If logout button is clicked
if (isset($_POST["logout"])) {
    // Destroy session and redirect to login page
    session_destroy();
    header("Location: login.php");
    exit();
}

unset($stmt); // Close statement
unset($pdo); // Close connection

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
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION["username"]; ?>!</h1>
        <h2>Currently in , 
        <?php 
        $servername = "localhost";
        $username = "admin";
        $password = "123456";
        $dbname = "reaction_time";

    // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        $daysql = "SELECT * FROM color_data WHERE username='" . $_SESSION["username"] . "'";
        $nightsql = "SELECT * FROM captcha_data WHERE username='" . $_SESSION["username"] . "'";
        $dayresult = mysqli_query($conn, $daysql);
        $nightresult = mysqli_query($conn, $nightsql);
        $currentHour = date("H");
        if (mysqli_num_rows($dayresult) == 0){
             $_SESSION['day_records'] = 0;
             echo "DAY RECORDS NOT FOUND. <br>";
        }else{
            $_SESSION['day_records'] = 1;
            echo "DAY RECORDS PRESENT <br>";
        }
        
        if (mysqli_num_rows($nightresult) == 0){
             $_SESSION['night_records'] = 0;
              echo "NIGHT RECORDS NOT FOUND <br>";
             
        }else{
            $_SESSION['night_records'] = 1;
            echo "NIGHT RECORDS PRESENT <br>";
        }
        if ($currentHour >= 7 && $currentHour < 19) {
         echo "Day Period  <br> ";
         $_SESSION['time_period'] = "day";
        } else {
            
        echo "Night Period <br> ";
        $_SESSION['time_period'] = "night";
        
         } 
         ?>
        
        </h2>
        <form method="post">
            <button type="submit" name="logout" class="btn btn-default btn-sm pull-right">Log out</button>
        </form>
        <p>Homepage</p>
    </div>
        <style>
              button {
        border: none;
        padding: 0;
         margin: 0;
        background: none;
        }

        .image-button1, .image-button2 {
        display: inline-block;
        vertical-align: top; /* to align the buttons at the top */
        
        }
        .image-button1 {
         margin-right: 15px; /* add some right margin to the first button */
         border : 1px solid black;
        }

        .image-button1 img, .image-button2 img {
        display: block;
         width: 100%; /* set the image width to fill the button */
         height: auto; /* set the image height to maintain aspect ratio */
        }
        
          .button-container {
              margin-left: 100px;
              margin-right: 300px;
   
  }
  
.countdown-box {
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: Arial, sans-serif;
  font-size: 36px;
  font-weight: bold;
  color: #333;
  text-shadow: 1px 1px #ccc;
  background-color: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.5);
  margin-top: 10px;
}

.countdown-box > div {
  margin-right: 20px;
  text-align: center;
}

.countdown-box > div:last-child {
  margin-right: 0;
}

.countdown-box .number {
  background-color: #333;
  color: #fff;
  padding: 10px;
  border-radius: 5px;
  box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.5);
}

.countdown-box .label {
  margin-top: 10px;
  text-transform: uppercase;
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
        <div class="countdown-box" style="display : none">
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


<script>
            
function checkSessionDay() {
    
    var curr_period = '<?= $_SESSION['time_period'] ?>';
    var day_records= '<?=$_SESSION['day_records'] ?>';
    
    if (curr_period === "day" && day_records === '0' ){
       window.location.href = 'reactiontime.php';
    }else if(curr_period === "day" && day_records === '1'){
        alert("403 Forbidden. Records already exist!");
        
    }else{
       alert("403 Forbidden. This resource cannot be accessed at this time!");  
    }
    }

function checkSessionNight() {
    var curr_period = '<?= $_SESSION['time_period'] ?>';
    var night_records= '<?=$_SESSION['night_records'] ?>';
    if (curr_period === "night" && night_records === '0' ){
       window.location.href = 'reactiontime.php';
    }else if(curr_period === "night" && night_records === '1'){
        alert("403 Forbidden. Records already exist!");
        
    }else{
       alert("403 Forbidden. This resource cannot be accessed at this time!");  
    }
}
                
            
var time = {
  hours: document.querySelector(".hours .number"),
  mins: document.querySelector(".mins .number"),
  secs: document.querySelector(".secs .number")
};

// countdown setup
var countdownDate = new Date();
countdownDate.setHours(countdownDate.getHours() + 12);

var timeCapture = function () {
  setTimeout(function () {
    var now = new Date();
    var remainingTime = countdownDate - now;

    if (remainingTime < 0) {
      time.hours.innerText = "00";
      time.mins.innerText = "00";
      time.secs.innerText = "00";
      return;
    }

    var hours = Math.floor(remainingTime / 3600000);
    var mins = Math.floor((remainingTime % 3600000) / 60000);
    var secs = Math.floor((remainingTime % 60000) / 1000);

    time.hours.innerText = hours < 10 ? "0" + hours : hours;
    time.mins.innerText = mins < 10 ? "0" + mins : mins;
    time.secs.innerText = secs < 10 ? "0" + secs : secs;

    timeCapture();
  }, 1000);
};

timeCapture();
            
</script>

</body>
</html>
>>>>>>> Stashed changes
