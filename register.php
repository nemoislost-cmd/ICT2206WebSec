<?php
// Include the necessary files
require_once 'db.php';
require_once 'security-validate-sanitise.php';

// Define variables and initialize with empty values
$name = $email = $password = $confirm_password = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate name
  $name = validate_name($_POST["name"]);

  // Validate email
  $email = validate_email($_POST["email"]);

  // Validate password
  $password = validate_password($_POST["password"]);

  // Validate confirm password
  $confirm_password = validate_password($_POST["confirm_password"], "ConfirmPassword");

  // Check for duplicate email
  if (!isset($message["Error"]["Email"])){

    // Prepare a select statement to check if the email already exists
    $sql = "SELECT username FROM user_accounts WHERE email = :email";

    if ($stmt = $pdo->prepare($sql)) {
      // Bind the value of email to the prepared statement
      $stmt->bindValue(":email", $email, PDO::PARAM_STR);

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {

        // Check if email already exists
        if ($stmt->rowCount() == 1) {
          $message["Error"]["Email"] = "This email address has already been registered..";
        } 

      } else {
        $message["Error"]["General"] = "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      unset($stmt);
    }
  }

  /*
  if (!isset($message["Error"]["Password"]) && !isset($message["Error"]["ConfirmPassword"])){
      if ($password !== $confirm_password){
        $message["Error"]["ConfirmPassword"] = "Passwords do not match.";
      }
  }*/

  // Check for password matches
  if ($password !== $confirm_password){
    $message["Error"]["ConfirmPassword"] = "Passwords do not match.";
  }

  // If no issues, proceed with making changes to database
  if (!isset($message["Error"])){
    // Generate a unique username
    $username = substr($name, 0, 4) ."_". uniqid();

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an insert statement
    $sql = "INSERT INTO user_accounts (username, name, email, password) VALUES (:username, :name, :email, :password_hash)";

    if ($stmt = $pdo->prepare($sql)) {
      // Bind the values to the prepared statement
      $stmt->bindValue(":username", $username, PDO::PARAM_STR);
      $stmt->bindValue(":name", $name, PDO::PARAM_STR);
      $stmt->bindValue(":email", $email, PDO::PARAM_STR);
      $stmt->bindValue(":password_hash", $password_hash, PDO::PARAM_STR);

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Send email to user with their generated username
        require_once "PHPMailer/src/PHPMailer.php";
        require_once "PHPMailer/src/SMTP.php";
        require_once "PHPMailer/src/Exception.php";
        
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'authreactmail@gmail.com'; // Enter your email here
        $mail->Password = 'qrzqpcmgrbaxtotb'; // Enter your email password here
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('authreactmail@gmail.com', 'AuthReact Mail'); // Enter your name here
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Auth-React Application : Username Credentials for the website ';
        $mail->Body = 'Hello '.$name.',<br><br>Please use the following username to login into the system: ' . $username . '<br><br>Thank you.';
        
        if($mail->send()){

          // Start output buffering
          ob_start();
          // Redirect to the login page
          session_start();
          $_SESSION["Info"]["General"] = "Registration successful. Do check your email for the username to login.";
          header("location: login.php");
          // Flush the output buffer and send the header
          ob_end_flush();
          exit();

        } else {
          $message["Error"]["General"] = "Mailing Error: " . $mail->ErrorInfo;
        }

      } else {
        $message["Error"]["General"] = "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      unset($stmt);
    }
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
    <title>Register</title>

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

<section class="h-100 gradient-form" style="background-color: #eee;">
  
  <!-- To see how it looks, change it to !isset -->
  <?php if (isset($message["Error"]["General"])) : ?> 
    <!-- Frame Modal Top -->
    <div class="modal fade top show" id="MessageModal" tabindex="-1" role="dialog" aria-labelledby="MessageModal" aria-hidden="true">
      <div class="modal-dialog modal-frame modal-top " role="document" style="max-width: 100%; margin:0;">
        <div class="modal-content">
          <div class="modal-body" style="padding:0;">
            <div class="d-flex justify-content-center align-items-center">
              <!-- To see how it looks, change the content of the message -->
              <!-- <p class="pt-3 mx-4" id="GeneralMessage">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Impedit nisi quo provident fugiat reprehenderit nostrum quos..</p> -->
              <p class="pt-3 mx-4" id="GeneralMessage"><?php echo $message["Error"]["General"]; ?></p>
              <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
          <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h4 class="mb-4">Important Notes : Creating Account</h4>
                <p class="small mb-0" style="text-align: justify;">1) There will be an unique ID generated by the system for each user. This will be sent to your emails and you would need to use this unique ID as the username when logging into the Auth-React application.

2) Do note that the Password requirements are as follows
 
8 characters minimum
1 Uppercase letter 
1 Lowercase letter
A number.
No special characters allowed.</p>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center mb-4">
                  <img src="images/AuthReact_Logo.PNG"
                    style="width: 185px;" alt=''>
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <p>Please fill this form to create an account.</p>
                  <div class="form-outline">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" />
                    <label class="form-label" for="name">Name</label>
                  </div>

                  <?php if (isset($message["Error"]["Name"])) : ?>
                    <div class="error text-danger mb-4"><?php echo $message["Error"]["Name"]; ?></div>
                  <?php endif; ?>
                  
                  <div class="form-outline mt-4">
                    <input type="email" name="email" class="form-control" placeholder="Your Email">
                    <label class="form-label" for="email">Email</label>
                  </div>

                  <?php if (isset($message["Error"]["Email"])) : ?>
                    <div class="error text-danger mb-4"><?php echo $message["Error"]["Email"]; ?></div>
                  <?php endif; ?>

                  <div class="form-outline mt-4">
                    <input type="password" name="password" class="form-control" placeholder="Your Password" />
                    <label class="form-label" for="password">Password</label>
                  </div>

                  <?php if (isset($message["Error"]["Password"])) : ?>
                    <div class="error text-danger mb-4"><?php echo $message["Error"]["Password"]; ?></div>
                  <?php endif; ?>

                  <div class="form-outline mt-4">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" />
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                  </div>

                  <?php if (isset($message["Error"]["ConfirmPassword"])) : ?>
                    <div class="error text-danger mb-4"><?php echo $message["Error"]["ConfirmPassword"]; ?></div>
                  <?php endif; ?>

                  <div class="text-center mt-4 pt-1 mb-4 pb-1">
                    <input class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit" value="Create account">
                  </div>

                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Already have an account?</p>
                    <a role="button" class="btn btn-outline-danger" href="login.php">Login here</a>
                  </div>

                </form>

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