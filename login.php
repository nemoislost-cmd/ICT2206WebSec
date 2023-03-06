<?php
// Start session
session_start();

// Include the database configuration file
require_once 'db.php';

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Checks if the HTTP request method is GET, which is the method used by browsers to request a new page or refresh the current page
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  unset($_POST);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate username
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter your username.";
  } else {
    $username = trim($_POST["username"]);
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter your password.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Check input errors before attempting to login
  if (empty($username_err) && empty($password_err)) {

    // Prepare a select statement
    $sql = "SELECT username, password FROM user_accounts WHERE username = :username";

    if ($stmt = $pdo->prepare($sql)) {
      // Bind the value of username to the prepared statement 
      $stmt->bindValue(":username", $username, PDO::PARAM_STR);

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Check if username exists
        if ($stmt->rowCount() == 1) {
          // Fetch the result
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $username = $row["username"];
          $password_hash = $row["password"];

          // Verify password
          if (password_verify($password, $password_hash)) {
            // Password is correct, start a new session
            session_start();

            // Store data in session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;

            // Redirect to home page
            header("location: index.php");
            exit();

          } else {
            // Display an error message if password is not valid
            $password_err = "The password you entered was not valid.";
          }
        } else {
          // Display an error message if username doesn't exist
          $username_err = "No account found with that username.";
        }
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      unset($stmt);
    }
  }

  // Close connection
  unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

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

<section class="h-100 gradient-form" style="background-color: #eee;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center mb-4">
                  <img src="images/AuthReact_Logo.PNG"
                    style="width: 185px;" alt=''>
                  
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <p>Please login to your account</p>
                  <div class="form-outline">
                    <input type="text" name="username" class="form-control"
                      placeholder="Username from your email" />
                    <label class="form-label" for="username">Username</label>
                  </div>

                  <?php if (!empty($username_err)) : ?>
                    <div class="error text-danger mb-4"><?php echo $username_err; ?></div>
                  <?php endif; ?>

                  <div class="form-outline mt-4">
                    <input type="password" name="password" class="form-control" />
                    <label class="form-label" for="password">Password</label>
                  </div>

                  <?php if (!empty($password_err)) : ?>
                    <div class="error text-danger mb-4"><?php echo $password_err; ?></div>
                  <?php endif; ?>

                  <div class="text-center mt-4 pt-1 mb-5 pb-1">
                    <input class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit" value="Login">
                    <a class="text-muted" href="forget-password.php">Forgot password?</a>
                  </div>

                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Don't have an account?</p>
                    <a role="button" class="btn btn-outline-danger" href="register.php">Create new</a>
                  </div>

                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h4 class="mb-4">We are AuthReact</h4>
                <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                  tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                  exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MDB -->
<script
  type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"
></script>


</body>
</html>
