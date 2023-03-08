<?php
session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reaction Time Test</title>
<!--        <script>
		alert("Please complete the following tests on this page using your mouse.");
	</script>-->
        
    </head>

        <style>
#box {
    width: 800px;
    height: 600px;
    background-color: blue;
    margin: auto;
    margin-top: 50px;
}

#text-container {
    position: relative;
    top: 50%;
    transform: translateY(-50%);
    text-align: center;
}

#test-text {
    font-size: 24px;
    color: white;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.counter {
    font-size: 24px;
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
    font-size: 24px;
    text-align: center;
    color: black;
    display: inline-block;
}

.button {
    padding: 15px 30px;
    background-color: #3498db;
    color: #fff;
    border-radius: 5px;
    font-size: 20px;
    cursor: pointer;
    transition: background-color 0.2s ease-in-out;
}

.button:disabled{
    background-color: #ccc;
    cursor: not-allowed;
}

.button:hover {
    background-color: #2980b9;
}

.buttondiv {
    position: fixed;
    bottom: 20px;
    right: 20px;
}

#text1 {
    font-size: 24px;
    color: white;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

#instruction-box {
  background-color: #f5f5f5;
  border-radius: 20px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
  margin: 40px auto;
  padding: 30px;
  text-align: center;
  width: 90%;
}

#instruction-box h2 {
  font-size: 32px;
  margin-bottom: 30px;
  color: #555;
}

#instruction-box ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

#instruction-box li {
  font-size: 20px;
  line-height: 1.8;
  margin-bottom: 15px;
  text-align: left;
  padding-left: 25px;
  position: relative;
  color: #777;
}

#instruction-box li::before {
  content: "";
  display: inline-block;
  height: 12px;
  width: 12px;
  border-radius: 50%;
  background-color: #f1c40f;
  position: absolute;
  left: 0;
  top: 11px;
  transition: all 0.3s ease;
}

#instruction-box li:hover::before {
  transform: scale(1.5);
  background-color: #e67e22;
}

#instruction-box li:first-child::before {
  background-color: #2ecc71;
}

#instruction-box li:last-child::before {
  background-color: #e74c3c;
}

#instruction-box a {
  color: #3498db;
  text-decoration: none;
  transition: all 0.3s ease;
}

#instruction-box a:hover {
  color: #2980b9;
  text-decoration: underline;
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
                    <div class="instruction-box">
  <h2>Instructions</h2>
  <ul>
    <li>Make sure you're in a quiet environment and can concentrate.</li>
    <li>Click anywhere within the rectangle to start the test.</li>
    <li>When the red box turns green, click as quickly as you can within the box.</li>
    <li>Click Finish once counter reaches 5!</li>
  </ul>
</div>
        </div>
        <div class="buttondiv">
            <form id ="color-data" action="loading.php" method ="post" onsubmit ="return sendData(event)">
            <input type ="hidden" name="data" id="data" value="">
            <input type ="hidden" name="timestamp" id="timestamp" value="">
            <button class="button" onclick="sendData(event)" type="submit">Finish</button>
            </form>
        </div>
        
       <?php
       $_SESSION["curr_device"] = "mouse";
       ?>
        <script>
            var counter= 0;
            var data = [];
            
            
            function resetState(counter,reactionTime){
                
                 var box = document.getElementById("box");
                 var textContainer = document.getElementById("text-container");
                 var counterdiv = document.getElementById("counterdiv");
                 var reactiondiv = document.getElementById("reactiondiv");
              
                 box.style.backgroundColor = "blue";
                 textContainer.innerHTML = "<p id='text1'>Good Job! Click to start again!</p>";
                 counterdiv.innerHTML = "<p id = 'counter' >Test Number "+counter+" completed. </p>";
                 //reactiondiv.innerHTML = "<p id ='reactiontime' >"+reactionTime+" ms </p>";
                 box.onclick = function(){
                     startTest();
                 };
             }
             
             function sendData(event){
                 
                 event.preventDefault();
                 var dataInput = document.getElementById("data");
                 if (data.length < 5){
                     
                     alert("Invalid action detected. Please complete all 5 tests before clicking finish!");
                 }else{
                 dataInput.value = JSON.stringify(data);
                 var currTime = Date.now();
                 var timestamp = document.getElementById("timestamp");
                 timestamp.value = currTime;
                 document.getElementById("color-data").submit();      
                 }

                 
             };
             function setFinishState(counter,reactionTime){
                
                 var box = document.getElementById("box");
                 var textContainer = document.getElementById("text-container");
                 var counterdiv = document.getElementById("counterdiv");
                 var reactiondiv = document.getElementById("reactiondiv");
                 box.style.backgroundColor = "black";
                 textContainer.innerHTML = "<p id='text1'>Test has concluded. Click Finish to continue!</p>";
                 counterdiv.innerHTML = "<p id = 'counter' >Test Number "+counter+" completed </p>";
                 //reactiondiv.innerHTML = "<p id ='reactiontime' >"+reactionTime+" ms </p>";
                 console.log(data);
             
             
            };
                 //var button = document.querySelector("#buttondiv button");
                 //button.disabled = false;             
            

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
                         if (counter <= 4){
                             console.log("working");
                             data.push(reactionTime);
                             resetState(counter,reactionTime);
                             //setFinishState(counter,reactionTime);
                         }else if (counter === 5) {
                             data.push(reactionTime);
                             setFinishState(counter,reactionTime);
                         }
                         //resetState(counter,reactionTime);
                         
                     };
                }, time);
            }
        </script>
    </body>
</html>
