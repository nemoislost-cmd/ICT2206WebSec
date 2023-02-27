<!DOCTYPE html>
<?php
session_start();
?>
<html>
    <head>
        <?php
            include "header.php";
        ?>
    </head>
    <body>
        <main class="container">
            <div class="bgimg-1 w3-display-container w3-opacity-min">
  <div class="w3-display-middle" style="white-space:nowrap;">
     <span class="w3-center w3-padding-large w3-xlarge w3-wide">

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input class="form-control" type="email" id="email" required name="email" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="pwd">Password:</label>
                    <input class="form-control" type="password" id="pwd" required name="pwd" placeholder="Enter password">
                </div>
                <div class="form-group">
                    <button onClick="login()" class="btn btn-primary" type="submit">Login</button>
                </div>
            </form>
  </span>
            </div>
            </div>
        </main>
        <?php
            include "footer.php";
        ?>
    </body>
</html>
