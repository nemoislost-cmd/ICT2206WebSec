<?php
// Initialize the session
// session_start();
 
// Include the necessary files
require_once 'db.php';
require_once 'security-validate-sanitise.php';

// If form submitted, process it
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    $username = validate_username($_POST["username"]);

    // If no issues, proceed with database queries
    if (!isset($message["Error"])){
        
        // Check if the username exists in the database
        $sql = "SELECT email,name FROM user_accounts WHERE username = :username";

        if($stmt = $pdo->prepare($sql)){

            // Bind the value of username to the prepared statement 
            $stmt->bindValue(":username", $username, PDO::PARAM_STR);

            // Attempt to execute the prepared statement
            if($stmt->execute()){

                // Check if username exists
                if($stmt->rowCount() == 1){

                    // Fetch the result
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $email = $row["email"];
                    $name = $row["name"];
                    
                    // Generate a random temporary password
                    $temp_password = bin2hex(random_bytes(6));
                    
                    // Hash the temporary password
                    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
                    // Update the user's password in the database
                    $sql = "UPDATE user_accounts SET password =(:password) WHERE username = :username";

                    if($stmt2 = $pdo->prepare($sql)){

                        // Bind the value of username to the prepared statement 
                        $stmt2->bindValue(":username", $username, PDO::PARAM_STR);
                        $stmt2->bindValue(":password", $hashed_password, PDO::PARAM_STR);

                        // Attempt to execute the prepared statement
                        if($stmt2->execute()){
                            // Send the temporary password to the user's email
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
                            $mail->Subject = 'Auth-React Application: New Password';
                            $mail->Body = 'Hello '.$name.'<br><br>Your new system generated password is: ' . $temp_password . '<br><br>Please login in with this password from now onwards for Auth-React Application.<br><br>Thank you.';
                            
                            if($mail->send()){

                                // Start output buffering
                                ob_start();
                                // Redirect to the login page
                                session_start();
                                $_SESSION["Info"]["General"] = "Do check your email for the new password to login.";
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
                        unset($stmt2);
                    }
                } else {
                  $message["Error"]["Username"] = "This username does not exist.";
                }
            } else {
              $message["Error"]["General"] = "Something went wrong. Please try again later.";
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
    <title>Forget Password</title>

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
          <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center mb-4">
                  <img src="images/AuthReact_Logo.PNG"
                    style="width: 185px;" alt=''>
                  
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <p>Enter your username to receive a new password.</p>
                    <div class="form-outline">
                        <input type="text" name="username" class="form-control"
                                placeholder="Username from your email" />
                        <label class="form-label" for="username">Username</label>
                    </div>

                    <?php if (isset($message["Error"]["Username"])) : ?>
                        <div class="error text-danger mb-4"><?php echo $message["Error"]["Username"]; ?></div>
                    <?php endif; ?>

                    <div class="text-center mt-4 pt-1 mb-4 pb-1">
                        <input class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit" value="Reset Password">
                    </div>
                    
                    <div class="d-flex justify-content-evenly mt-4">
                        <a class="text-muted" href="login.php">Login</a>
                        <a class="text-muted" href="register.php">Register</a>
                    </div>
                    
                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h4 class="mb-4">Important Notes : Forget Password</h4>
                <p class="small mb-0" style="text-align: justify;">In the event that you forgot your password, the system will generate a random password and send it to the email that you have registered with in Auth-React.</p>
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
