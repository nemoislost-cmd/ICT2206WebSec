<?php
$question = $errorMsg = "";
$success = true;
session_start();

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
            // Note that email field is unique, so should only have
            // one row in the result set.
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
        <main class="container">
            <div class="bgimg-1 w3-display-container w3-opacity-min">
  <div class="w3-display-middle" style="white-space:nowrap;">
     <span class="w3-center w3-padding-large w3-xlarge w3-wide">
            <hr>
            <?php
            if ($success)
            {
                echo "<h2>Security Question!</h2>";
                echo "<h4> " . $question . "</h4>";
                echo "<h4>Please key in the security answer.</h4>";
                echo "<br>";
            }
            else
            {
                echo "<h2>Oops!</h2>";
                echo "<h4>The following errors were detected:</h4>";
                echo "<p>" . $errorMsg . "</p>";
                echo "<a href='logout.php' class='btn btn-warning'>Proceed to Logout</a>";
                echo "<br>";
            }
            ?>
            <form action="submit.php" method="post">
                <div class="form-group">
                    <label for="email">Answer:</label>
                    <input class="form-control" type="text" id="ans" required name="ans" placeholder="Enter answer">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
            </span>
            </div>
            </div>
            <hr>
        </main>
        <?php
            include "footer.php";
        ?>
    </body>
</html>
