<?php
session_start();
?>

<html>
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <body>
        <style>
            body {
                background-color: white;
                font-family: "Roboto", sans-serif;
            }
            h1 {
                text-align: center;
                font-size: 2em;
                margin-top: 2em;
            }
            .progress {
                position: relative;
                height: 10px;
                width: 80%;
                margin: 0 auto;
                border: 2px solid #ccc;
                border-radius: 15px;
                background-color: #f5f5f5;
            }
            .progress .color {
                position: absolute;
                background-color: #2a9d8f;
                width: 0px;
                height: 10px;
                border-radius: 15px;
                animation: progress 4s infinite linear;
            }
            @keyframes progress {
                0% {
                    width: 0%;
                }
                25% {
                    width: 50%;
                }
                50% {
                    width: 75%;
                }
                75% {
                    width: 85%;
                }
                100% {
                    width: 100%;
                }
            }
            button[type="submit"] {
                display: block;
                margin: 2em auto;
                background-color: blue;
                color: white;
                font-size: 1.5em;
                padding: 1em;
                border-radius: 10px;
            }
            img {
                display: block;
                margin: 0 auto 2em;
            }



        </style>
        <h1>Calibration in progress... Click the button to proceed!</h1>
        <div class="progress">
            <div class="color"></div>
        </div>
        <img src="images/ice-bear.png" alt="placeholder" width="400px" >
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = json_decode($_REQUEST["data"], true);
            $timestamp = json_decode($_REQUEST["timestamp"], true);
            $curr_device = $_SESSION["curr_device"];
            $color_data = $data;
            $color_data_timestamp = $timestamp;
            // Preparing data set for result for image verification
            $captcha_data = $_SESSION["captcha_data"];
            $captcha_timestap = $timestamp;
            $captcha_questions = $_SESSION["captcha_questions_completed"];
            $total_captcha_qustions = count($captcha_questions);
        }
        
        if ($total_captcha_qustions == 10){
            insert_aptcha_questions($captcha_questions);
        }

        require_once('db_connect.php');

// Calculates the maximum value in an array
        function calculate_max($arr) {
            return max($arr);
        }

// Calculates the minimum value in an array
        function calculate_min($arr) {
            return min($arr);
        }

        function calculate_mean($arr) {
            $sum = array_sum($arr);
            $count = count($arr);
            if ($count > 0) {
                return $sum / $count;
            } else {
                return null;
            }
        }

        function calculate_median($arr) {
            sort($arr);
            $count = count($arr);
            $mid = floor(($count - 1) / 2);
            if ($count % 2 == 0) {
                return ($arr[$mid] + $arr[$mid + 1]) / 2;
            } else {
                return $arr[$mid];
            }
        }

        function calculateMarginOfError($data, $confidenceLevel) {
            $sampleSize = count($data);
            $mean = array_sum($data) / $sampleSize;
            $variance = 0;
            $skew = 0;

            foreach ($data as $value) {
                $variance += pow($value - $mean, 2);
                $skew += pow($value - $mean, 3);
            }

            $variance /= ($sampleSize - 1);
            $skew /= ($sampleSize * pow(sqrt($variance), 3));

            $standardDeviation = sqrt($variance);
            $zScore = getZScore($confidenceLevel);
            $marginOfError = $zScore * ($standardDeviation / sqrt($sampleSize));
            $marginOfErrorPercent = ($marginOfError / $mean) * 100;
            return $marginOfErrorPercent;
        }

        function getZScore($confidenceLevel) {
            switch ($confidenceLevel) {
                case 0.8:
                    return 1.28;
                case 0.9:
                    return 1.645;
                case 0.95:
                    return 1.96;
                case 0.99:
                    return 2.576;
                default:
                    return 0;
            }
        }

        function calculateLowerMargin($min, $margin) {
            $lower_margin = $min - ($margin / 100 * $min);
            $lower_margin_rounded = round($lower_margin, 0);
            return $lower_margin_rounded;
        }

        function calculateUpperMargin($max, $margin) {
            $upper_margin = $max + ($margin / 100 * $max);
            $upper_margin_rounded = round($upper_margin, 0);
            return $upper_margin_rounded;
        }

        function calculate_sd($arr) {
            // calculate the mean of the array
            $mean = array_sum($arr) / count($arr);

            // calculate the difference between each element and the mean
            $diffs = array_map(function ($x) use ($mean) {
                return $x - $mean;
            }, $arr);

            // square each difference
            $squared_diffs = array_map(function ($x) {
                return pow($x, 2);
            }, $diffs);

            // calculate the mean of the squared differences
            $mean_squared_diffs = array_sum($squared_diffs) / count($squared_diffs);

            // take the square root of the mean squared differences to obtain the standard deviation
            $sd = sqrt($mean_squared_diffs);

            return $sd;
        }

        function insertColorData($color_data, $color_data_timestamp, $curr_device) {
            $mean = calculate_mean($color_data);
            $max = calculate_max($color_data);
            $min = calculate_min($color_data);
            $median = calculate_median($color_data);
            $margin = calculateMarginOfError($color_data, 0.80);
            $lower_margin = calculateLowerMargin($min, $margin);
            $upper_margin = calculateUpperMargin($max, $margin);

            $sd = calculate_sd($color_data);
            $value1 = $_SESSION["username"];
            $value2 = $color_data[0];
            $value3 = $color_data[1];
            $value4 = $color_data[2];
            $value5 = $color_data[3];
            $value6 = $color_data[4];
            $value7 = date('Y-m-d H:i:s', $color_data_timestamp / 1000);
            $tempdate = strtotime($value7);
            $tempdate += 12 * 60 * 60;
            $targetdate = date('Y-m-d H:i:s', $tempdate);
            $value8 = $_SESSION["time_period"];
            $value9 = $curr_device;
            $servername = "localhost";
            $username = "mel";
            $password = "password";
            $dbname = "reaction_time";

            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);

            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $stmt1 = $conn->prepare("INSERT INTO color_testcase (username,T1,T2,T3,T4,T5,timestamp,test_period,device,futuretimestamp) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt1->bind_param("siiiiissss", $value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8, $value9, $targetdate);
            // Set parameters and execute
            $stmt1->execute();
            $stmt2 = $conn->prepare("INSERT INTO color_data (username,mean,median,test_period,sd,lower,upper,margin,device,lower_margin,upper_margin) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt2->bind_param("siisiiiisii", $value1, $mean, $median, $value8, $sd, $min, $max, $margin, $value9, $lower_margin, $upper_margin);
            $stmt2->execute();
//    if (isset($_SESSION["curr_device"])){
//        if ($_SESSION["curr_device"] == "mouse"){
//             echo '<form action="reactiontimetrackpad.php"><button type="submit" style="background-color: blue; color: white; padding: 10px;">Next</button></form>';
//            
//        }else{
//            $_SESSION['startCountdown']=1;
//            echo '<form action="index.php" onsubmit="location.reload()"><button type="submit" style="background-color: blue; color: white; padding: 10px;">Finish</button></form>';
//            
//            
//        }
//    }
            //echo '<form action="reactiontimetrackpad.php"><button type="submit" style="background-color: blue; color: white; padding: 10px;">Next</button></form>';


            mysqli_close($conn);
        }

        function insertCaptchaData($data, $data_timestamp, $curr_device) {
            $mean = calculate_mean($data);
            $max = calculate_max($data);
            $min = calculate_min($data);
            $median = calculate_median($data);
            $margin = calculateMarginOfError($data, 0.80);
            $lower_margin = calculateLowerMargin($min, $margin);
            $upper_margin = calculateUpperMargin($max, $margin);
            $sd = calculate_sd($data);
            $value1 = $_SESSION["username"];
            $value2 = $data[0];
            $value3 = $data[1];
            $value4 = $data[2];
            $value5 = $data[3];
            $value6 = $data[4];
            $value7 = date('Y-m-d H:i:s', $data_timestamp / 1000);
            $tempdate = strtotime($value7);
            $tempdate += 12 * 60 * 60;
            $targetdate = date('Y-m-d H:i:s', $tempdate);
            $value8 = $_SESSION["time_period"];
            $value9 = $curr_device;
            $servername = "localhost";
            $username = "mel";
            $password = "password";
            $dbname = "reaction_time";

            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);

            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $stmt1 = $conn->prepare("INSERT INTO captcha_testcase (username,T1,T2,T3,T4,T5,timestamp,test_period,device,futuretimestamp) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt1->bind_param("siiiiissss", $value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8, $value9, $targetdate);
            // Set parameters and execute
            $stmt1->execute();
            $stmt2 = $conn->prepare("INSERT INTO captcha_data (username,mean,median,test_period,sd,lower,upper,margin,device,lower_margin,upper_margin) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt2->bind_param("siisiiiisii", $value1, $mean, $median, $value8, $sd, $min, $max, $margin, $value9, $lower_margin, $upper_margin);
            $stmt2->execute();
            if (isset($_SESSION["curr_device"])) {
                if ($_SESSION["curr_device"] == "mouse") {
                    echo '<form action="inform_change_test.php"><button type="submit" style="background-color: blue; color: white; padding: 10px;">Next</button></form>';
                } else {
                    $_SESSION['startCountdown'] = 1;
                    echo '<form action="index.php" onsubmit="location.reload()"><button type="submit" style="background-color: blue; color: white; padding: 10px;">Finish</button></form>';
                }
            }
            mysqli_close($conn);
        }

        function insert_aptcha_questions($captcha_questions) {
            $value1 = $_SESSION["username"];
            $value2 = $captcha_questions[0];
            $value3 = $captcha_questions[1];
            $value4 = $captcha_questions[2];
            $value5 = $captcha_questions[3];
            $value6 = $captcha_questions[4];
            $value7 = $captcha_questions[5];
            $value8 = $captcha_questions[6];
            $value9 = $captcha_questions[7];
            $value10 = $captcha_questions[8];
            $value11 = $captcha_questions[9];

            $servername = "localhost";
            $username = "mel";
            $password = "password";
            $dbname = "reaction_time";

            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $stmt1 = $conn->prepare("INSERT INTO captcha_completed_questions (username,Q1,Q2,Q3,Q4,Q5,Q6,Q7,Q8,Q9,Q10) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt1->bind_param("ssssss", $value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8, $value9, $value10, $value11);
            $stmt1->execute();
        }

//calculateMarginOfError($color_data, 0.95);
        insertColorData($color_data, $color_data_timestamp, $curr_device);
        insertCaptchaData($captcha_data, $captcha_timestap, $curr_device);
        ?>
        
    </body>
</html>
