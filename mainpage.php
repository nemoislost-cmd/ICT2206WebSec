<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reaction Time Test</title>
<<<<<<< Updated upstream
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
  
     @import url("https://fonts.googleapis.com/css2?family=Lato&display=swap");

html {
  font-size: 62.5%;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: "Lato", sans-serif;
  background: #272727;
  color: #ffd868;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.display-date {
  text-align: center;
  margin-bottom: 10px;
  font-size: 1.6rem;
  font-weight: 600;
}

.display-time {
  display: flex;
  font-size: 5rem;
  font-weight: bold;
  border: 2px solid #ffd868;
  padding: 10px 20px;
  border-radius: 5px;
  transition: ease-in-out 0.1s;
  transition-property: background, box-shadow, color;
  -webkit-box-reflect: below 2px
    linear-gradient(transparent, rgba(255, 255, 255, 0.05));
}

.display-time:hover {
  background: #ffd868;
  box-shadow: 0 0 30px#ffd868;
  color: #272727;
  cursor: pointer;
}

.container {
    margin-top: 700px;
    margin-left: -600px;
}

        </style>
=======
>>>>>>> Stashed changes
    </head>
    <body>
        <div class ="button-container">
        <button class="image-button1">
            <img src="images/2.png">
        </button>

        <button class="image-button2">
            <img src="images/1.png">
        </button>      
        </div>
       <div class="container">
      <div class="display-date">
        <span id="day">day</span>,
        <span id="daynum">00</span>
        <span id="month">month</span>
        <span id="year">0000</span>
      </div>
      <div class="display-time"></div>
    </div> 

    </body>
</html>
<script>
    const displayTime = document.querySelector(".display-time");
// Time
function showTime() {
  let time = new Date();
  displayTime.innerText = time.toLocaleTimeString("en-US", { hour12: false });
  setTimeout(showTime, 1000);
}

showTime();

// Date
function updateDate() {
  let today = new Date();

  // return number
  let dayName = today.getDay(),
    dayNum = today.getDate(),
    month = today.getMonth(),
    year = today.getFullYear();

  const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  const dayWeek = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  // value -> ID of the html element
  const IDCollection = ["day", "daynum", "month", "year"];
  // return value array with number as a index
  const val = [dayWeek[dayName], dayNum, months[month], year];
  for (let i = 0; i < IDCollection.length; i++) {
    document.getElementById(IDCollection[i]).firstChild.nodeValue = val[i];
  }
}

updateDate();

 </script>
<?php


$servername = "localhost";
$username = "admin";
$password = "123456";
$dbname = "reaction_time";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
?>



