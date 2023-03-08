<?php
session_start();
$_SESSION["device"]= "trackpad";
header("location: color.php");
?>