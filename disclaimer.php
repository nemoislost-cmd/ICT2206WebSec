<!DOCTYPE html>
<html>
<head>
	
    <title>Disclaimer Page</title>
    <meta charset="UTF-8">
     <link href="css/disclaimer.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	<div class="container">
		<h1>Disclaimer</h1>
		<div>
  <p style="text-align: justify;">
    This website was developed for a project for ICT 2206 Web Security. While we have taken basic security measures to protect the website, the main intention is to collect data for our experimental setup to test our hypothesis. Please refrain from performing any penetration testing on the website as it was not developed for this purpose. We operate based on the trust that you would do these tests in an authentic manner by following the instructions and prompts given. If you do manage to break something not intended to be broken, please inform the developers of the website.</p>
  <p style="text-align: justify;">
    By clicking the "I agree to the terms" button below, you acknowledge that you have read, understood, and agreed to the terms of this disclaimer. Do not engage in disruptive behavior, including, but not limited to, the following: launching denial of service attacks, launching cyber attacks, illegal, immoral or unethical conduct. As mentioned before, while this website was developed for Web Security, it is not intended for penetration testing.</p>
</div>
		<button id="agree-btn">I agree to the terms</button>
	</div>
	<script>
		const agreeBtn = document.getElementById("agree-btn");
		agreeBtn.addEventListener("click", function() {
			alert("Thank you for agreeing to the terms! Please click Ok to proceed to the Login page.");
			window.location.href = "login.php";
		});
	</script>
</body>
</html>