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
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h1><span>I'm</span> Ice Bear.</h1>
			<p>The Strongest, The Cleverest</p>
			<img src="images/Ice_bear_slay.jpg" alt="boy" width="992" height="1108">
        </div>
      </div>
    </div>
  </div>
        <?php
            include "footer.php";
        ?>
    </body>
</html>