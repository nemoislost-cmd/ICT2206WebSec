<?php
$question = $errorMsg = "";
$success = true;
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

function getSecurityQuestion()
{
    global $question, $errorMsg, $success;
    // Create database connection.
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    // Check connection
    if ($conn->connect_error)
    {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    }
    else
    {
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT question FROM user_accounts WHERE 
        username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $_SESSION["username"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $question = $row["question"];
            $_SESSION["ques"] = $question;
        }
        else
        {
            $errorMsg = "Security question not found";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

getSecurityQuestion();
?>
<html>
    <head>
        <?php
            include "header.php";
        ?>
    </head>
    <body>
        <?php
        include "nav.php";
        ?>
        <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <?php
            if ($success)
            {
                echo "<h2> " . $question . "</h2>";
                echo "<h4>Please key in the security answer.</h4>";?>
                <form action="choice.php" method="post">
              <div class="form-group my-4">
                <label for="answer">Answer:</label>
                <input type="text" class="form-control" id="answer" name="answer" required placeholder="Enter answer">
              </div>
              <div class="text-center mt-4 pt-1 pb-1">
                    <input class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit" value="Submit">
              </div>
            </form>
                <?php
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
        <?php
            include "footer.php";
        ?>
    </body>
</html>
