<html>
    
    <body>

<div class ="display">
    <p id ="text">Calibration in progress.... Please do not exit out of the system...</p>
</div>

<?php
<<<<<<< Updated upstream

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data = json_decode($_REQUEST["data"], true);
  $timestamp = json_decode($_REQUEST["timestamp"], true);
  $curr_device = json_decode($_REQUEST["device"], true);
  $color_data = $data;
  $color_data_timestamp = $timestamp;
  print_r($color_data);
  print_r($color_data_timestamp);
  print_r($curr_device);
}

=======
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data = json_decode($_REQUEST["data"], true);
  $timestamp = json_decode($_REQUEST["timestamp"], true);
  $curr_device = $_SESSION["curr_device"];
  $color_data = $data;
  $color_data_timestamp = $timestamp;
  print_r($color_data_timestamp);
}

insertColorData($color_data,$color_data_timestamp,$curr_device);
>>>>>>> Stashed changes
$mean = calculate_mean($color_data);
$max = calculate_max($color_data);
$min = calculate_min($color_data);
$median = calculate_median($color_data);

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
  $mid = floor(($count-1)/2);
  if($count % 2 == 0) {
    return ($arr[$mid] + $arr[$mid+1])/2;
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
    
    if (abs($skew) < 0.8) { // Normal distribution
        $standardDeviation = sqrt($variance);
        $zScore = getZScore($confidenceLevel);
        $marginOfError = $zScore * ($standardDeviation / sqrt($sampleSize));
    } else { // T distribution
        $degreesOfFreedom = $sampleSize - 1;
        $tScore = getTScore($confidenceLevel, $degreesOfFreedom);
        $standardError = sqrt($variance / $sampleSize);
        $marginOfError = $tScore * $standardError;
    }
    $marginOfErrorPercent = ($marginOfError / $mean) * 100;
<<<<<<< Updated upstream
    echo "Margin of error: " . round($marginOfErrorPercent, 2) . "%";
=======
     echo "Margin of error: " . round($marginOfErrorPercent, 2) . "%";
    return $marginOfErrorPercent;
   
>>>>>>> Stashed changes
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



function getTScore($confidenceLevel, $degreesOfFreedom) {
    $alpha = 1 - $confidenceLevel;
<<<<<<< Updated upstream
    $tDistribution = new TDistribution($degreesOfFreedom);
    return $tDistribution->getInverseCDF(1 - ($alpha / 2));
}

function insertColorData($color_data){
=======
    $tcrit = stats_inv_t($alpha/2, $degreesOfFreedom);
    return abs($tcrit);
}

function calculate_sd($arr) {
    // calculate the mean of the array
    $mean = array_sum($arr) / count($arr);
    
    // calculate the difference between each element and the mean
    $diffs = array_map(function($x) use ($mean) {
        return $x - $mean;
    }, $arr);
    
    // square each difference
    $squared_diffs = array_map(function($x) {
        return pow($x, 2);
    }, $diffs);
    
    // calculate the mean of the squared differences
    $mean_squared_diffs = array_sum($squared_diffs) / count($squared_diffs);
    
    // take the square root of the mean squared differences to obtain the standard deviation
    $sd = sqrt($mean_squared_diffs);
    
    return $sd;
}

function insertColorData($color_data,$color_data_timestamp,$curr_device){
>>>>>>> Stashed changes
    $mean = calculate_mean($color_data);
    $max = calculate_max($color_data);
    $min = calculate_min($color_data);
    $median = calculate_median($color_data);
<<<<<<< Updated upstream
    calculateMarginOfError($color_data, 0.95);
=======
    $margin = calculateMarginOfError($color_data, 0.95);
    $sd = calculate_sd($color_data);
    $value1 = $_SESSION["username"];
    $value2 = $color_data[0];
    $value3 = $color_data[1];
    $value4 = $color_data[2];
    $value5 = $color_data[3];
    $value6 = $color_data[4];
    $value7 = date('Y-m-d H:i:s',$color_data_timestamp/1000);
    echo "<br>";
    echo $value7;
    $value8 = $_SESSION["time_period"];
    $value9 = $curr_device ;
>>>>>>> Stashed changes
    
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
    echo "Connected successfully <br>";


    
   $stmt1 = $conn->prepare("INSERT INTO color_testcase (username,T1,T2,T3,T4,T5,timestamp,test_period,device) VALUES (?,?,?,?,?,?,?,?,?)");
   $stmt1->bind_param("siiiiisss", $value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8, $value9);
    // Set parameters and execute
    $stmt1->execute();
    echo "New record created successfully in color_testcase";
    $stmt2 = $conn->prepare("INSERT INTO color_data (username,mean,median,test_period,sd,lower,upper,margin,device) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt2->bind_param("siisiiiis", $value1, $mean, $median, $value8, $sd, $min, $max, $margin, $value9);
    $stmt2->execute();
    echo "New record created successfully in color_data";
    mysqli_close($conn);
    

    
}

echo "<br>";
echo "Mean: " . $mean . "<br>";
echo "Max: " . $max . "<br>";
echo "Min: " . $min . "<br>";
echo "Median: ". $median . "<br>";
calculateMarginOfError($color_data, 0.95);
    
?>

   </body>
</html>
