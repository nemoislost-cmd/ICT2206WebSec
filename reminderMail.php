<?php
session_start();
    
if (isset($_SESSION['reminderMail'])) {
    header('Content-Type: text/plain');
    echo "Mail has already been sent";
    
} else {
        require_once('db_connect.php');
        $query = "SELECT email,name FROM user_accounts WHERE username='" . $_SESSION["username"] . "'";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];
        $name = $row['name'];
        
        } else {
       // Handle the case where the countdownDate value was not found
        }   //$countdownDate = time();
        if($_SESSION['countdownover'] =='1'){
        require_once "PHPMailer/src/PHPMailer.php";
        require_once "PHPMailer/src/SMTP.php";
        require_once "PHPMailer/src/Exception.php";
        
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'authreactmail@gmail.com'; // Enter your email here
        $mail->Password = 'eqluyqelncswqtuj'; // Enter your email password here
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('authreactmail@gmail.com', 'AuthReact Mail'); // Enter your name here
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Auth-React Reminder : Come back to input remaining data!';
        $mail->Body = 'Hello '.$name.',<br><br>12hrs have passed since you last did the tests for Day/Night. Please come back to finish the remaining tests for Day/Night respectively.<br><br>Thank you. Visit the site at http://nice-kirch.cloud';
        
        if($mail->send()){
            $_SESSION['reminderMail']=1;
            //$_SESSION['startCountdown']=0;
           header('Content-Type: text/plain');
           echo "Mail sent";
        } else {
            $email_err = "Error: " . $mail->ErrorInfo;
            header('Content-Type: text/plain');
            echo "Mail Error";
        }
       }else{
             echo "Countdown not over clown..."  ;
           }
}
 




