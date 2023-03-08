<?php
// clear all the session variables and redirect to index
session_start();
session_unset();
session_destroy();
session_write_close();
header("location: login.php");
exit();
?>