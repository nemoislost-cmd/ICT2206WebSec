<?php

// Define an array to store the error messages
$message = array();

// Define a function to validate and sanitize the username field
function validate_username($username) {
    global $message;

    if (empty($username)) {
        $message["Error"]["Username"] = "Please enter your name.";
    } else {
        $username = filter_var(trim($username), FILTER_SANITIZE_STRING);
        if (!preg_match('/^[a-zA-Z ]{0,4}_[\da-f]{13}$/i', $username)) {
            $message["Error"]["Username"] = "Please use the username provided in your email.";
        }
    }

    return $username;
}

// Define a function to validate and sanitize the name field
function validate_name($name) {
    global $message;

    if (empty($name)) {
        $message["Error"]["Name"] = "Name is required";
    } else {
        $name = filter_var(trim($name), FILTER_SANITIZE_STRING);
        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $message["Error"]["Name"] = "Name can only contain letters and whitespace";
        }
    }

    return $name;
}

// Define a function to validate and sanitize the email field
function validate_email($email) {
    global $message;

    if (empty($email)) {
        $message["Error"]["Email"] = "Please enter your email address.";
    } else {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message["Error"]["Email"] = "Invalid email format";
        }
    }

    return $email;
}

// Define a function to validate and sanitize the password field
function validate_password($password, $str = "Password", $site = "Normal") {
    global $message;

  if (empty($password)) {
    $message["Error"][$str] = "Please enter a password.";
  } else {
    $password = filter_var(trim($password), FILTER_SANITIZE_STRING);
    if (strlen($password) < 8) {
      $message["Error"][$str] = "Password must have at least 8 characters.";
    }elseif ( $site != "Normal"){
      
      //Need to cater to the newly generated password at Login Page
      if (!preg_match('/^[0-9a-f]{12}$/i', $password) && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)){
        $message["Error"]["Password"] = "The password you entered was not valid.";
      }

    }elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) {
      $message["Error"][$str] = "Password must have at least a uppercase letter, a lowercase letter and a number. No special characters is allowed";
    }
  }

    return $password;
}

// Define a function to validate and sanitize the Questions and Answers Field
function validate_question_answer($input_string, $type = "Question") {
    global $message;

    if (empty($input_string)) {
        $message["Error"][$type] = $type . " is required";
    } else {

//        $input_string = filter_var(trim($input_string), FILTER_SANITIZE_STRING);
//        if (!preg_match("/^[a-zA-Z0-9\s',?-]+$/', $input_string)) {
//            if ($type != "Question") {
//                $message["Error"][$type] = $type . " can only contain letters and whitespace, numbers and single quotes.";
//            } else {
//                $message["Error"][$type] = $type . " can't be modified.";
//            }
//        }

        $answer = trim($input_string); // remove leading/trailing whitespace
        $answer = preg_replace('/\s+/', ' ', $answer); // replace multiple whitespace with single space
//        echo $answer;
        if (strpos($answer, "'") !== false && substr_count($answer, "'") > 1) {
//            echo "have single quote";
            // answer contains more than one single quote
            // handle the error here
        }
        else if (!ctype_alnum(str_replace(' ', '', $answer))) {
//            echo "answer with special characters";
            // answer contains non-alphanumeric characters
            // handle the error here
        }else {
//            echo $input_string;
            return $input_string;
        }
        
    }

//    return $input_string;
}

?>