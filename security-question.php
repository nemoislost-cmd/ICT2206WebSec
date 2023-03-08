<?php
session_start();

// Include the database configuration file
require_once 'db.php';

$selected_question = $user_answer = "";


// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Get user's security question and answer from database
$username = $_SESSION["username"];

// Prepare a select statement
$sql = "SELECT question, answer FROM user_accounts WHERE username = :username";
$stmt = $pdo->prepare($sql);

// Bind the value of username to the prepared statement 
$stmt->bindValue(":username", $username, PDO::PARAM_STR);

// Execute and Fetch the result
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$security_question = $row['question'];
$security_answer = $row['answer'];

// Close statement
unset($stmt);

// Check if user has already set a security question and answer
if ($security_question && $security_answer) {
    // Redirect to index page
    header("Location: index.php");
    exit();
}


// Function to check for XSS and SQL injection attacks
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Retrieve questions from API if not already stored in session variable
if (!isset($_SESSION["List of Questions"])) {
    
  // Retrieve the questions from the database
  $sql = "SELECT question FROM security_questions";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $questions = $stmt->fetchAll(PDO::FETCH_COLUMN);

  // Shuffle the questions
  shuffle($questions);

    // Add 3 question into the list stored inside the session
    $_SESSION["List of Questions"] = $selected_questions = array_slice($questions, 0, 3);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get selected question and user's answer
    $selected_question =$_POST["question"];
    $user_answer = test_input($_POST["answer"]);
	
	/*
	print_r(test_input($selected_question));
	print_r(array_map('test_input',$_SESSION["List of Questions"]));
	print_r($_SESSION["List of Questions"]);
	echo (!in_array($selected_question, array_map('test_input',$_SESSION["List of Questions"])));
    */

	// Check if selected question matches one of the three listed questions
    if (!in_array( test_input($selected_question) ,$_SESSION["List of Questions"])) {
        //$error_message = "Questions are already fixed, do not change it.";
    } elseif (empty($user_answer)){
		$error_message = "Please enter an answer";
	}else {
        // Prepare an update statement
		$sql = "UPDATE user_accounts SET question = :question, answer = :answer WHERE username = :username";

		if ($stmt = $pdo->prepare($sql)) {
  			// Bind parameters to the prepared statement
  			$stmt->bindValue(":username", $username, PDO::PARAM_STR);
  			$stmt->bindValue(":question", $selected_question, PDO::PARAM_STR);
  			$stmt->bindValue(":answer", $user_answer, PDO::PARAM_STR);

			// Attempt to execute the prepared statement
			if ($stmt->execute()) {
				// Update successful
				echo "User's security question and answer updated successfully.";

				// Redirect to index.php
				header("Location: index.php");
				exit();
				
			} else {
				// Update failed
				$error_message = "Error: Failed to update user's security question and answer.";
			}
		}
    }
}

if (isset($error_message)){
	// Set the question back instead of retrieve a new one
	$selected_questions = $_SESSION["List of Questions"];
}


  // Close connection
  unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set Security Question</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css">
  <style>
    body {
      background-color: #eee;
    }
    .card {
      margin-top: 50px;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
		<img src="images/AuthReact_Logo.PNG"
                    style="width: auto; max-height: 50px;" alt=''>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="#">Home</a>
          </li>
          <li class="nav-item">
		  <a class="nav-link" href="logout.php">Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header">
            <h4 class="mb-0">Set Security Question</h4>
          </div>
          <div class="card-body">
            <?php if (isset($error_message)) { ?>
              <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
              </div>
            <?php } ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
               <div class="form-group my-4">
                <label for="question">Select a Security Question:</label>
                <select class="form-select" id="question" name="question">
                  <?php foreach ($selected_questions as $question) { ?>
                    <option value="<?php echo $question; ?>"><?php echo $question; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group my-4">
                <label for="answer">Answer:</label>
                <input type="text" class="form-control" id="answer" name="answer">
              </div>
              <div class="text-center mt-4 pt-1 pb-1">
                    <input class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit" value="Submit">
                    
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
</body>
</html>
