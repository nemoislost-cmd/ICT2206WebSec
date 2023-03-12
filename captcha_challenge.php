<?php
session_start();

// Creating of varies session when session_completed_test is not created
if (!isset($_SESSION['session_completed_test'])) {
    $_SESSION['session_completed_test'] = 0;
    $_SESSION["device"] = "mouse";
    $_SESSION["num_of_tries"] = 0;
    $_SESSION["captcha_data"] = array();
    $_SESSION["captcha_questions_completed"] = array();
}

if (!isset($_SESSION["session_image_answer"])) {
    // Define the coordinates of the hidden object for the puzzle
    $images_with_answers = array(
        "images/captcha_images/question1.jpg" => array("question" => "Find the wolf", "min_x" => 500, "max_x" => 600, "min_y" => 136, "max_y" => 195),
        "images/captcha_images/question2.jpg" => array("question" => "Find the bear", "min_x" => 183, "max_x" => 251, "min_y" => 210, "max_y" => 258),
        "images/captcha_images/question3.jpg" => array("question" => "Find the cat", "min_x" => 284, "max_x" => 328, "min_y" => 376, "max_y" => 423),
        "images/captcha_images/question4.jpg" => array("question" => "Find the spotless dog", "min_x" => 477, "max_x" => 514, "min_y" => 61, "max_y" => 179),
        "images/captcha_images/question5.jpg" => array("question" => "Find the catus", "min_x" => 479, "max_x" => 524, "min_y" => 56, "max_y" => 128),
        "images/captcha_images/question6.jpg" => array("question" => "Find the ninja", "min_x" => 313, "max_x" => 375, "min_y" => 48, "max_y" => 176),
        "images/captcha_images/question7.jpg" => array("question" => "Find the red hangbag", "min_x" => 373, "max_x" => 405, "min_y" => 332, "max_y" => 375),
        "images/captcha_images/question8.jpg" => array("question" => "Find the pink hairdryer", "min_x" => 371, "max_x" => 415, "min_y" => 263, "max_y" => 284),
        "images/captcha_images/question9.jpg" => array("question" => "Find the yellow pillow", "min_x" => 357, "max_x" => 405, "min_y" => 228, "max_y" => 265),
        "images/captcha_images/question10.jpg" => array("question" => "Find the sliver dustbin", "min_x" => 370, "max_x" => 398, "min_y" => 368, "max_y" => 423),
        "images/captcha_images/question11.jpg" => array("question" => "Find the grape", "min_x" => 167, "max_x" => 233, "min_y" => 239, "max_y" => 265),
        "images/captcha_images/question12.jpg" => array("question" => "Find the pair of red heels", "min_x" => 178, "max_x" => 245, "min_y" => 54, "max_y" => 89),
        "images/captcha_images/question13.jpg" => array("question" => "Find the flowers in a vase", "min_x" => 329, "max_x" => 363, "min_y" => 140, "max_y" => 194),
        "images/captcha_images/question14.jpg" => array("question" => "Find the toilet paper", "min_x" => 569, "max_x" => 597, "min_y" => 326, "max_y" => 367),
        "images/captcha_images/question15.jpg" => array("question" => "Find the red hair brush", "min_x" => 213, "max_x" => 230, "min_y" => 275, "max_y" => 306),
        "images/captcha_images/question16.jpg" => array("question" => "Find the box with a chicken head", "min_x" => 314, "max_x" => 381, "min_y" => 211, "max_y" => 308),
        "images/captcha_images/question17.jpg" => array("question" => "Find the basketball", "min_x" => 483, "max_x" => 530, "min_y" => 37, "max_y" => 81),
        "images/captcha_images/question18.jpg" => array("question" => "Find the teddy bear", "min_x" => 481, "max_x" => 526, "min_y" => 244, "max_y" => 275),
        "images/captcha_images/question19.jpg" => array("question" => "Find Tweety bird", "min_x" => 336, "max_x" => 403, "min_y" => 361, "max_y" => 456),
        "images/captcha_images/question20.jpg" => array("question" => "Find Stitch", "min_x" => 361, "max_x" => 442, "min_y" => 220, "max_y" => 360),
        "images/captcha_images/question21.jpg" => array("question" => "Find Baby Boss", "min_x" => 424, "max_x" => 492, "min_y" => 141, "max_y" => 225),
        "images/captcha_images/question22.jpg" => array("question" => "Find Perry the platypus", "min_x" => 230, "max_x" => 349, "min_y" => 383, "max_y" => 424),
        "images/captcha_images/question23.jpg" => array("question" => "Find Spongebob", "min_x" => 241, "max_x" => 343, "min_y" => 330, "max_y" => 404)
    );
    $_SESSION["session_image_answer"] = $images_with_answers;
    // Select a random image from the list using array_rand() function
    $random_image = array_rand($images_with_answers);

    // To set $_SESSION["session_image_slected"] to $random_image
    // $_SESSION["session_image_selected"] is for storing the value of the image that was selected using the array random function 
    $_SESSION["session_image_selected"] = $random_image;
    
    // $question value is grabbing from the $images_with_answer array using $random_image to the the value in question
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
<html>
    <head>
        <meta charset="UTF-8">
        <link href="css/captcha_style.css" rel="stylesheet" type="text/css"/>
        <script defer src="js/captcha_stuff.js"></script>
        <title>Image Reaction Test</title>
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
                </div>
                <button id="btn_submit"  type="submit" onclick="complete_captcha()">Submit</button>
            </form>
        </div>
    </body>
</html>
