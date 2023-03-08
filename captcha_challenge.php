<?php
session_start();

if (!isset($_SESSION['session_completed_test'])) {
    $_SESSION['session_completed_test'] = 0;
    $_SESSION["device"] = "mouse";
    $_SESSION["num_of_tries"] = 0;
    $_SESSION["captcha_data"] = array();
}

if (!isset($_SESSION["session_image_answer"])) {
    // Define the coordinates of the hidden object for the puzzle
    $images_with_answers = array(
        "images/captcha_images/question1.jpg" => array("question" => "Find the wolf", "min_x" => 500, "max_x" => 600, "min_y" => 136, "max_y" => 195),
        "images/captcha_images/question2.jpg" => array("question" => "Find the bear", "min_x" => 183, "max_x" => 251, "min_y" => 210, "max_y" => 258),
        "images/captcha_images/question3.jpg" => array("question" => "Find the cat", "min_x" => 284, "max_x" => 328, "min_y" => 376, "max_y" => 423),
        "images/captcha_images/question4.jpg" => array("question" => "Find the spotless dog", "min_x" => 477, "max_x" => 514, "min_y" => 61, "max_y" => 179),
        "images/captcha_images/question5.jpg" => array("question" => "Find the spotless pig", "min_x" => 477, "max_x" => 514, "min_y" => 61, "max_y" => 179)
    );
    $_SESSION["session_image_answer"] = $images_with_answers;
    // Select a random image from the list
    $random_image = array_rand($images_with_answers);
    $_SESSION["session_image_selected"] = $random_image;
    $question = $images_with_answers[$random_image]["question"];
} else if (isset($_SESSION['success'])) {
    echo "<script>alert('Congratulations! You got the correct answer!');</script>";
    // Unset the session variable
    unset($_SESSION['success']);
    $_SESSION["num_of_tries"] = 0;
    $images_with_answers = $_SESSION["session_image_answer"];
    $random_image = array_rand($images_with_answers);
    $question = $_SESSION["session_image_answer"][$random_image]["question"];
    $_SESSION["session_image_selected"] = $random_image;
}

// Check if failure session variable is set
else if (isset($_SESSION['failure'])) {
    // Unset the session variable
    unset($_SESSION['failure']);
    $random_image = $_SESSION["session_image_selected"];
    if ($_SESSION["num_of_tries"] == 3) {
        unset($_SESSION['session_image_answer'][$_SESSION["session_image_selected"]]);
        echo "<script>alert('You failed this puzzle!');</script>";
        $images_with_answers = $_SESSION["session_image_answer"];
        $random_image = array_rand($images_with_answers);
        $question = $_SESSION["session_image_answer"][$random_image]["question"];
        $_SESSION["session_image_selected"] = $random_image;
        $_SESSION["num_of_tries"] = 0;
    } else if ($_SESSION["num_of_tries"] < 3) {
        $remaining_tries = 3 - $_SESSION["num_of_tries"];
        $question = $_SESSION["session_image_answer"][$random_image]["question"];
        echo "<script>alert('$remaining_tries tries left');</script>";
    }
} else {
    $images_with_answers = $_SESSION["session_image_answer"];
    $random_image = array_rand($images_with_answers);
    $current_question = $_SESSION["session_image_selected"];
    while ($random_image == $current_question) {
        $random_image = array_rand($images_with_answers);
    }
    $question = $_SESSION["session_image_answer"][$random_image]["question"];
    $_SESSION["session_image_selected"] = $random_image;
}
?>

<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->

<html>
    <head>
        <meta charset="UTF-8">
        <link href="css/captcha_style.css" rel="stylesheet" type="text/css"/>
        <script defer src="js/captcha_stuff.js"></script>

        <title></title>
    </head>
    <body>
        <h1> 
            <?php
            echo $_SESSION["session_completed_test"] + 1;
            ?> Out Of 5
        </h1>
        <h3>
            Test: 
            <?php
            echo $question;
            ?> 
        </h3>
        <div>
            <form id="main-container" method="POST"  onsubmit="return validateForm()" action="validation.php">
                <div id="captcha-container">
                    <img id="captcha-image" hidden="hidden" src="<?php echo $random_image ?>"  alt="Find the hidden object"  />
                    <button id="btn_show_image"  onclick="show_image()">Click Me To Reveal Puzzle!</button> 
                    <input type="hidden" name="clickedX" id="clickedX" value="">
                    <input type="hidden" name="clickedY" id="clickedY" value="">
                    <input type="hidden" name="time_taken" id="time_taken" value="">
                    <!--                    <div id="click_area"><div class="circle"></div></div>
                    <div class="circle"></div>-->
                </div>
                <button id="btn_submit"  type="submit" onclick="complete_captcha()">Submit</button>
            </form>
        </div>
    </body>
</html>