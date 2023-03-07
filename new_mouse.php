<?php
session_start();
$_SESSION["device"]= "mouse";
header("location: new_color.php");
?>