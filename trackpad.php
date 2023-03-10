<?php
session_start();
$_SESSION["device"]= "trackpad";
header("location: captcha.php");
?>