<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_SESSION['reminderMail'])) {
    echo "Mail has already been sent";
} else {
        $servername = "localhost";
        $username = "admin";
        $password = "123456";
        $dbname = "reaction_time";

    // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        $query = "SELECT email,name FROM user_accounts WHERE username='" . $_SESSION["username"] . "'";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];
        $name = $row['name'];
        
        } else {
       // Handle the case where the countdownDate value was not found
        }   //$countdownDate = time();
        
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
        $mail->Subject = 'Auth-React Reminder : Come back to input remaining data!';
        $mail->Body = 'Hello '.$name.',<br><br>12hrs have passed since you last calibrated your data. Please come back to finish the remaining tests for day or night respectively.<br><br>Thank you.';
        
        if($mail->send()){
            // Redirect to the login page
            $_SESSION['reminderMail']=1;
            $_SESSION['startCountdown']=0;
            exit();
        } else {
            $email_err = "Error: " . $mail->ErrorInfo;
            echo $email_err;
        }
 
}
}


     
