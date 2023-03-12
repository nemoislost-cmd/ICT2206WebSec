<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}
?>
<html>
    <head>
        <?php
            include "header.php";
        ?>
    </head>
    <body>
        <?php
        include "nav.php";
        ?>
        <div class="container">
    
            <h1><span>I'm</span> Ice Bear.</h1>
			<p>The Strongest, The Cleverest</p>
			<img src="images/Ice_bear_slay.jpg" alt="boy" width="992" height="1108">

        </div>
        <?php
            include "footer.php";
        ?>
    </body>
</html>