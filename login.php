<?php
$username = $password = $errorMsg = $username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  unset($_POST);
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function authenticateUser(){
    global $username, $password, $pwd_hashed, $errorMsg, $success;
    $config = parse_ini_file('../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
    $config['password'], $config['dbname']);
    if ($conn->connect_error){
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else{
        $stmt = $conn->prepare("SELECT password FROM user_accounts WHERE 
        username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $pwd_hashed = $row["password"];
            if (password_verify($password, $pwd_hashed)) {
                session_start();
                $_SESSION["username"] = $username;
                header("location: security.php");
                exit();
            } else {
                $errorMsg = "No account found.";
            }
        }
        else {
            $errorMsg = "No account found.";
        }
        $stmt->close();
    }
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $username_err = "Username is required.";
    } else {
        $username = sanitize_input($_POST["username"]);
    }

    if (empty($_POST["password"])){
        $password_err = "Password is required.<br>";
    } else {
        $password = sanitize_input($_POST["password"]);
    }

    authenticateUser();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include "header.php";
    ?>
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
                    
                    <?php if (!empty($errorMsg)) : ?>
                    <div class="error text-danger mb-4"><?php echo $errorMsg; ?></div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4 pt-1 mb-5 pb-1">
                    <input class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit" value="Login">
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
    <?php
        include "footer.php";
    ?>
</body>
</html>