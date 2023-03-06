<!DOCTYPE html>
<html>
<head>
	<title>Disclaimer Page</title>
	<style>
body {
    font-family: "Roboto", sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f7f7f7;
}

.container {
    width: 80%;
    margin: 0 auto;
    padding: 40px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    text-align: center;
}

h1 {
    font-size: 3rem;
    margin-bottom: 20px;
    font-weight: 500;
}

p {
    font-size: 1.5rem;
    margin-bottom: 30px;
    line-height: 1.5;
}

button {
    padding: 10px 20px;
    font-size: 1.5rem;
    background-color: #4CAF50;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #3e8e41;
}

	</style>
</head>
<body>
	<div class="container">
		<h1>Disclaimer</h1>
		<p>This website was developed for a project for ICT 2206 Web Security . While we have taken basic security measures to protect the website, the main intention is to collect data for our experimental setup to test our hypothesis. Please refrain from performing any penetration testing on the website as it was not developed for this purpose. We operate based on the trust that you would do these tests in an authentic manner by following the instructions and prompts given.</p>
		<p>By clicking the "I agree to the terms" button below, you acknowledge that you have read, understood, and agreed to the terms of this disclaimer.</p>
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