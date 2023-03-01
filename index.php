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
