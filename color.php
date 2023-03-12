<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reaction Time Test</title>
        <script>
		alert("Please complete the following verification on this page.");
	</script>
        
    </head>
        <link rel="stylesheet" href="css/color.css">
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
                    <div class="instruction-box">
  <h2>Instructions</h2>
  <ul>
    <li>Make sure you're in a quiet environment and can concentrate.</li>
    <li>Click anywhere within the rectangle to start the test.</li>
    <li>When the red box turns green, click as quickly as you can within the box.</li>
    <li>Click Finish once counter reaches 1!</li>
  </ul>
</div>
        </div>
        <div class="buttondiv">
            <form id ="color-data" action="verification.php" method ="post" onsubmit ="return sendData(event)">
            <input type ="hidden" name="data" id="data" value="">
            <input type ="hidden" name="timestamp" id="timestamp" value="">
            <button class="button" onclick="sendData(event)" type="submit">Finish</button>
            </form>
        </div>
        <script>
            var counter= 0;
            var data = 0;
             
             function sendData(event){
                 
                 event.preventDefault();
                 var dataInput = document.getElementById("data");
                 if (data < 1){
                     alert("Invalid action detected. Please complete verification before clicking finish!");
                 }else{
                 dataInput.value = data;
                 var currTime = Date.now();
                 var timestamp = document.getElementById("timestamp");
                 timestamp.value = currTime;
                 document.getElementById("color-data").submit();      
                 }
             };
             function setFinishState(counter){
                
                 var box = document.getElementById("box");
                 var textContainer = document.getElementById("text-container");
                 var counterdiv = document.getElementById("counterdiv");
                 box.style.backgroundColor = "black";
                 textContainer.innerHTML = "<p id='text1'>Test has concluded. Click Finish to continue!</p>";
                 counterdiv.innerHTML = "<p id = 'counter' >Test Number "+counter+" completed </p>";
                 console.log(data);
            };
                 
            function startTest() {
                var box = document.getElementById("box");
                box.style.backgroundColor = "red";
                var textContainer = document.getElementById("text-container");
               textContainer.innerHTML = "<p id='text1'>Wait for it.. Click anywhere in the box when it turns green</p>";
                
                var time = Math.random() * 6000 + 1000;
                setTimeout(function() {
                    box.style.backgroundColor = "green";
                    var startTime = Date.now();
                     box.onclick = function() {
                         var endTime = Date.now();
                         var reactionTime = endTime - startTime;
                         counter++;
                         if (counter === 1){
                             data = reactionTime;
                             setFinishState(counter);
                         }
                     };
                }, time);
            }
        </script>
    </body>
</html>