<?php
session_start();
$_SESSION["device"]= "trackpad";
header("location: new_color.php");
?>