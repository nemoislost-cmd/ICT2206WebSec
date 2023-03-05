<?php
$username = $name = $pwd_hashed = $email = $errorMsg = "";
$success = true;

if (empty($_POST["email"]))
{
    $errorMsg .= "Email is required.<br>";
    $success = false;
}
else
{
    $email = sanitize_input($_POST["email"]);
    // Additional check to make sure e-mail address is well-formed.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $errorMsg .= "Invalid email format.<br>";
        $success = false;
    }
}

if (empty($_POST["pwd"]))
{
    $errorMsg .= "Password is required.<br>";
    $success = false;
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/*
* Helper function to authenticate the login.
*/
function authenticateUser()
{
    global $username, $name, $email, $pwd_hashed, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT * FROM user_accounts WHERE 
        email=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
        {
            // Note that email field is unique, so should only have
            // one row in the result set.
            $row = $result->fetch_assoc();
            $username = $row["username"];
            $name = $row["name"];
            $pwd_hashed = $row["password"];
            // Check if the password matches:
            if (!password_verify($_POST["pwd"], $pwd_hashed))
            {
                // Don't be too specific with the error message - hackers don't
                // need to know which one they got right or wrong. :)
                $errorMsg = "Email not found or password doesn't match...";
                $success = false;
            }
            else
            {
                session_start();
                $_SESSION["username"]= $username;
                $_SESSION["name"]= $name;
            }
        }
        else
        {
            $errorMsg = "Email not found or password doesn't match...";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkExistingData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1)
        {
            $errorMsg = "MultiAuth data not found!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkDayData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.test_period='day'
            AND captcha_data.test_period='day'
            AND color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1)
        {
            $errorMsg = "Day data not found!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkNightData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.test_period='night'
            AND captcha_data.test_period='night'
            AND color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1)
        {
            $errorMsg = "Night data not found!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkTrackpadData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.device='trackpad'
            AND captcha_data.device='trackpad'
            AND color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 2)
        {
            $errorMsg = "Trackpad data incomplete!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function checkMouseData()
{
    global $username, $errorMsg, $success;
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
        $stmt = $conn->prepare("SELECT captcha_data.username, color_data.username 
            FROM captcha_data 
            INNER JOIN color_data
            ON captcha_data.username = color_data.username
            WHERE color_data.device='mouse'
            AND captcha_data.device='mouse'
            AND color_data.username=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 2)
        {
            $errorMsg = "Mouse data incomplete!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

authenticateUser();
checkExistingData();
checkDayData();
checkNightData();
checkTrackpadData();
checkMouseData();
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
                echo "<h2>Login successful!</h2>";
                echo "<h4>Welcome back, " . $name . ".</h4>";
                echo "<h4>Choose either trackpad or mouse for your next step.</h4>";
                echo "<a href='trackpad.php' class='btn btn-success'>Trackpad</a>";
                echo "<br>";
                echo "<a href='mouse.php' class='btn btn-success'>Mouse</a>";
                echo "<br>";
            }
            else
            {
                echo "<h2>Oops!</h2>";
                echo "<h4>The following errors were detected:</h4>";
                echo "<p>" . $errorMsg . "</p>";
                echo "<a href='logout.php' class='btn btn-warning'>Return to Home</a>";
                echo "<br>";
            }
            ?>
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
