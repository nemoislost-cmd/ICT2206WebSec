<?php
session_start();
$_SESSION["device"]= "mouse";
header("location: captcha.php");
?>