<?php
// Include the database configuration file
require_once 'db.php';

// Define variables and initialize with empty values
$name = $email = $password = "";
$name_err = $email_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate name
  if (empty(trim($_POST["name"]))) {
    $name_err = "Please enter your name.";
  } else {
    $name = trim($_POST["name"]);
  }

  // Validate email
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter your email address.";
  } else {
    $email = trim($_POST["email"]);

    // Prepare a select statement to check if the email already exists
    $sql = "SELECT username FROM user_accounts WHERE email = :email";

    if ($stmt = $pdo->prepare($sql)) {
      // Bind the value of email to the prepared statement
      $stmt->bindValue(":email", $email, PDO::PARAM_STR);

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Check if email already exists
        if ($stmt->rowCount() == 1) {
          $email_err = "This email address is already taken.";
        } 

      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      unset($stmt);
    }
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST["password"])) < 8) {
    $password_err = "Password must have at least 8 characters.";
  } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', trim($_POST["password"]))) {
    // Password does not meet complexity requirements
    $password_err = "Password must have at least a uppercase letter, a lowercase letter and a number. No special characters is allowed";
  } else {
    $password = trim($_POST["password"]);
  }

  // Validate confirm password
  if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', trim($_POST["confirm_password"]))) {
    // Password does not meet complexity requirements
    $confirm_password_err = "Password doesn't match";
  } elseif (trim($_POST["password"]) !== trim($_POST["confirm_password"]) ) {
    // Password does not meet complexity requirements
    $confirm_password_err = "Password doesn't match";
  }


  // Check input errors before inserting in database
  if (empty($name_err) && empty($email_err) && empty($password_err) && empty($$confirm_password_err)) {

    // Generate a unique username
    $username = substr($name, 0, 4) ."_". uniqid();

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an insert statement
    $sql = "INSERT INTO user_accounts (username, name, email, password) VALUES (:username, :name, :email, :password)";

    if ($stmt = $pdo->prepare($sql)) {
      // Bind the values to the prepared statement
      $stmt->bindValue(":username", $username, PDO::PARAM_STR);
      $stmt->bindValue(":name", $name, PDO::PARAM_STR);
      $stmt->bindValue(":email", $email, PDO::PARAM_STR);
      $stmt->bindValue(":password", $password_hash, PDO::PARAM_STR);

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
        $mail->Subject = 'Username used to login';
        $mail->Body = 'Hello '.$name.',<br><br>Please use the following username to login: ' . $username . '<br><br>Thank you.';
        
        if($mail->send()){
            // Redirect to the login page
            header("location: login.php");
            exit();
        } else {
            $email_err = "Error: " . $mail->ErrorInfo;
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
    <title>Register</title>

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
          <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h4 class="mb-4">We are AuthReact</h4>
                <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                  tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                  exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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

                  <?php if (!empty($name_err)) : ?>
                    <div class="error text-danger mb-4"><?php echo $name_err; ?></div>
                  <?php endif; ?>
                  
                  <div class="form-outline mt-4">
                    <input type="email" name="email" class="form-control" placeholder="Your Email">
                    <label class="form-label" for="email">Email</label>
                  </div>

                  <?php if (!empty($email_err)) : ?>
                    <div class="error text-danger mb-4"><?php echo $email_err; ?></div>
                  <?php endif; ?>

                  <div class="form-outline mt-4">
                    <input type="password" name="password" class="form-control" placeholder="Your Password" />
                    <label class="form-label" for="password">Password</label>
                  </div>

                  <?php if (!empty($password_err)) : ?>
                    <div class="error text-danger mb-4"><?php echo $password_err; ?></div>
                  <?php endif; ?>

                  <div class="form-outline mt-4">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" />
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                  </div>

                  <?php if (!empty($confirm_password_err)) : ?>
                    <div class="error text-danger mb-4"><?php echo $confirm_password_err; ?></div>
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
<script
  type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"
></script>

</body>
</html>