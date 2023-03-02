<?php
session_start();

// Include the database configuration file
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Get user's security question and answer from database
$username = $_SESSION["username"];
$sql = "SELECT question, answer FROM user_account WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":username", $username, PDO::PARAM_STR);

$stmt->execute();

// Check if username exists
if ($stmt->rowCount() == 1){
    //Fetch the row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $security_question = $row["question"];
    $security_answer = $row["answer"];

    // Check if user has already set a security question and answer
    if (!$security_question || !$security_answer) {
        // Redirect to security question page
        header("Location: security-question.php");
        exit();
    }

}else {
    // Display an error message if username doesn't exist
    $username_err = "No account found with that username.";
    
    // Redirect to home page
    header("location: login.php");
    exit();
}

// If logout button is clicked
if (isset($_POST["logout"])) {
    // Destroy session and redirect to login page
    session_destroy();
    header("Location: login.php");
    exit();
}

unset($stmt); // Close statement
unset($pdo); // Close connection

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    
    <link rel="stylesheet" href="style.css" >

    <!-- Font Awesome -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
      rel="stylesheet"
    />
    <!-- MDB -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css"
      rel="stylesheet"
    />

</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION["username"]; ?>!</h1>
        <p>This is your home page.</p>
        <form method="post">
            <button type="submit" name="logout" class="btn btn-default btn-sm pull-right">Log out</button>
        </form>
    </div>
    
<!-- MDB -->
<script
  type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"
></script>

</body>
</html>
