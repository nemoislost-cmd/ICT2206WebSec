<?php
// Start session
//session_start();

// Include the necessary files
require_once 'db.php';
require_once 'security-validate-sanitise.php';

// Define variables and initialize with empty values
$username = $password = "";

// Checks if the HTTP request method is GET, which is the method used by browsers to request a new page or refresh the current page
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  unset($_POST);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate username
  $username = validate_username($_POST["username"]);

  // Validate password
  $password = validate_password($_POST["password"], "Password", "Login");
  // $password = $_POST["password"];

  // If no issues, proceed with trying to login
  if (!isset($message["Error"])){ 
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
            // $result_verify = password_verify($password, $password_hash);
            // Start output buffering
            ob_start();
            // Password is correct, start a new session
            session_start();

            // Store data in session variables
            $_SESSION["username"] = $username;

            // Redirect to home page
            header("location: index.php");
            // Flush the output buffer and send the header
            ob_end_flush();
            exit();

          } else {
            // Display an error message if password is not valid
            $message["Error"]["Password"] = "The password you entered was not valid.";
          }
        } else {
          // Display an error message if username doesn't exist
          $message["Error"]["Username"]  = "No account found with that username.";
        }
      } else {
        $message["Error"]["General"] = "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      unset($stmt);
    }
  }else{
 
    // Display an error message if password is not valid
    $message["Error"]["Password"] = "The password you entered was not valid.";
  }

  // Close connection
  unset($pdo);

} // End of if ($_SERVER["REQUEST_METHOD"] == "POST")

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="css/style.css" >

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

  <!-- To see how it looks, change it to !isset -->
  <?php if (isset($message["Error"]["General"]) || isset($_SESSION["Info"]["General"])) : ?> 
    <!-- Frame Modal Top -->
    <div class="modal fade top" id="MessageModal" tabindex="-1" role="dialog" aria-labelledby="MessageModal" aria-hidden="true">
      <div class="modal-dialog modal-frame modal-top " role="document" style="max-width: 100%; margin:0;">
        <div class="modal-content">
          <div class="modal-body" style="padding:0;">
            <div class="d-flex justify-content-center align-items-center">

            <?php if (isset($message["Error"]["General"])) :?>
              <!-- To see how it looks, change the content of the message -->
              <!-- <p class="pt-3 mx-4" id="GeneralMessage">Error</p> -->
              <p class="pt-3 mx-4" id="GeneralMessage"><?php echo $message["Error"]["General"]; ?></p>
            <?php elseif (isset($_SESSION["Info"]["General"])) :?>
              <!-- <p class="pt-3 mx-4" id="GeneralMessage">Info</p> -->
              <p class="pt-3 mx-4" id="GeneralMessage"><?php echo $_SESSION["Info"]["General"]; unset($_SESSION["Info"]["General"]); ?></p>
            <?php endif; ?>
              <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

<section class="h-100 gradient-form" style="background-color: #eee;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center mb-4">
                  <img src="images/AuthReact_Logo.png"
                    style="width: 185px;" alt=''>
                  
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <p>Please login to your account</p>
                  <div class="form-outline">
                    <input type="text" name="username" class="form-control"
                      placeholder="Username from your email" />
                    <label class="form-label" for="username">Username</label>
                  </div>

                  <?php if (isset($message["Error"]["Username"])) : ?>
                    <div class="error text-danger mb-4"><?php echo $message["Error"]["Username"]; ?></div>
                  <?php endif; ?>

                  <div class="form-outline mt-4">
                    <input type="password" name="password" class="form-control" />
                    <label class="form-label" for="password">Password</label>
                  </div>

                  <?php if (isset($message["Error"]["Password"])) : ?>
                    <div class="error text-danger mb-4"><?php echo $message["Error"]["Password"]; ?></div>
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
                <h4 class="mb-4">What is AuthReact?</h4>
                <p class="small mb-0" style="text-align: justify;">Auth-React is an application developed by the team trying_not_to_die for the purpose of ICT 2206 Web Security. This application aims to be a POC for an experimental concept conceived by the team. Our team is trying to investigate the hypothesis of using human reaction time as a newer form of 2FA authentication to authenticate users into the system. We have developed a series of tests for the purpose of this experiment that will utilise human reaction time. We are also taking into account several factors that we are trying to investigate that will affect the human reaction time in general. </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MDB -->
<!-- Required JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>

<script>
  $(document).ready(function(){
    $('#MessageModal').modal('show');
  });
</script>

</body>
</html>
