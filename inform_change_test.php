<?php
session_start();
    
    if (($_SESSION["device"] == "mouse") && (!isset($_SESSION["curr_device"]))){
//        $_SESSION["device"] = "trackpad";
        $link = "reactiontime.php";
        $device = $_SESSION["device"];
        $current_test = "Captcha";
        $next_test = "Color";
    } else if (($_SESSION["curr_device"] == "mouse")&&($_SESSION["device"] == "mouse")){
        $_SESSION["device"] = "trackpad";
        $_SESSION["captcha_data"] = array();
        $device = $_SESSION["device"];
        $link = "captcha_challenge.php";
        $current_test = "Color";
        $next_test = "Captcha";
    }else{
        $device = $_SESSION["device"];
        $link = "reactiontimetrackpad.php";
        $current_test = "Captcha";
        $next_test = "Color";
    }
?>

 <html>
    <head>
        <meta charset="UTF-8">
        <link href="css/captcha_style.css" rel="stylesheet" type="text/css"/>
        <script defer src="js/captcha_stuff.js"></script>

        <title></title>
    </head>
    <body>
        <form id="content-container" action="<?php echo $link;?>">
            <h1 id="take_note">TAKE NOTE!!!</h1>
            <p id="test_disclaimer">You have completed the <?php echo $current_test;?> test. The upcoming testing is on <?php echo $next_test;?> Test.<br>
                Click the button below, if you are ready to take the test.</p>
            <p id="device_to_use">Please use the <?php echo $device;?> to complete the next test!</p>
            <button id="start_test">Start Test</button>
        </form>
    </body>
</html>