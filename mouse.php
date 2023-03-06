<?php
session_start();
$_SESSION["device"]= "mouse";
header("location: color.php");
//header("location: security.php");
?>