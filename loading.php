<html>
    
    <body>

<div class ="display">
    <p id ="text">Calibration in progress.... Please do not exit out of the system...</p>
</div>

<?php

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
    echo "Margin of error: " . round($marginOfErrorPercent, 2) . "%";
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
    $tDistribution = new TDistribution($degreesOfFreedom);
    return $tDistribution->getInverseCDF(1 - ($alpha / 2));
}

function insertColorData($color_data){
    $mean = calculate_mean($color_data);
    $max = calculate_max($color_data);
    $min = calculate_min($color_data);
    $median = calculate_median($color_data);
    calculateMarginOfError($color_data, 0.95);
    
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
