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
<title>Hej</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body, h1,h2,h3,h4,h5,h6 {font-family: "Montserrat", sans-serif}
.w3-row-padding img {margin-bottom: 12px;}
/* Remove margins from "page content" on small screens */
@media only screen and (max-width: 600px) {#main {margin-left: 0}}
</style>
</head>
<body class="w3-black">


<!-- Page Content -->
<div class="w3-padding-large" id="main">
  <!-- Header/Home -->
  <header class="w3-container w3-padding-32 w3-center w3-black" id="home">
    <h1 class="w3-jumbo"><span class="w3-hide-small">I'm</span> Ice Bear.</h1>
    <p>The Strongest, The Cleverest</p>
    <img src="images/Ice_bear_slay.jpg" alt="boy" class="w3-image" width="992" height="1108">
  </header>

  <!-- About Section -->
  <div class="w3-content w3-justify w3-text-grey w3-padding-64" id="about">
    <h2 class="w3-text-light-grey">Ice Bear</h2>
    <hr style="width:200px" class="w3-opacity">
    <p>Ice Bear is the youngest of the trio, but is, undoubtedly, the strongest, the cleverest, and, in some respects, the most mature of them. He was able to rescue his older brothers from certain death without too much hassle and is quick to jump into action if he finds a threat arising. He cares for both of his brothers dearly. He tends to do most of the chores of the house, though he doesn't seem to mind this. Despite his willingness to pitch in where the others don't, he still takes days off to relax and unwind.

Not only is he a smart and strong bear, but he's also essential to the Bears' brotherhood.
    </p>
</div>
  
</body>
</html>