<?php
session_start();
$num_test_completed = $_SESSION["session_completed_test"];
$images_with_answers = $_SESSION["session_image_answer"];
$selected_image = $_SESSION["session_image_selected"];

//print_r($images_with_answers);
// Get the x and y values for "image/image1.png"
$answer_min_x = $images_with_answers[$selected_image]["min_x"];
$answer_max_x = $images_with_answers[$selected_image]["max_x"];
$answer_min_y = $images_with_answers[$selected_image]["min_y"];
$answer_max_y = $images_with_answers[$selected_image]["max_y"];

if (($_SERVER["REQUEST_METHOD"] == "POST")) {
    $clickedX = $_POST["clickedX"];
    $clickedY = $_POST["clickedY"];
    $time_taken = $_POST["time_taken"];

    if (($clickedX >= $answer_min_x && $clickedX <= $answer_max_x) && ($clickedY >= $answer_min_y && $clickedY <= $answer_max_y)) {
        //correct answer
        unset($images_with_answers[$selected_image]);
//       print_r($images_with_answers);
        array_push($_SESSION["captcha_data"], $time_taken);
        $num_test_completed++;

        // $_SESSION["success"] = true;
        // $_SESSION["session_image_answer"] = $images_with_answers;
        // $_SESSION["session_completed_test"] = $num_test_completed;
        // // Redirect back to index.php
        // header("Location: index.php");
        // exit();
        
        if ($num_test_completed == 5){
            $_SESSION["num_of_tries"] = 0;
            unset($_SESSION["session_image_answer"]);
            // Redirect Link to color test
            $_SESSION["session_completed_test"] = 0;
            header("Location: inform_change_test.php");
            exit();
        }else{
            $_SESSION["success"] = true;
            $_SESSION["session_image_answer"] = $images_with_answers;
            $_SESSION["session_completed_test"] = $num_test_completed;
            // Redirect back to index.php
            header("Location: captcha_challenge.php");
            exit();
        }
    } else {
        //wrong answer
        $_SESSION["failure"] = true;
        $_SESSION["session_completed_test"] = $num_test_completed;
        $_SESSION["session_image_selected"] = $selected_image;
        $_SESSION["num_of_tries"]++;
        // Redirect back to index.php
        header("Location: captcha_challenge.php");
        exit();
    }
}