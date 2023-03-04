<!DOCTYPE html>
<html>
<head>
	<title>Disclaimer Page</title>
	<style>
		body {
			font-family: Arial, sans-serif;
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
			font-size: 2.5rem;
			margin-bottom: 20px;
		}
		p {
			font-size: 1.2rem;
			margin-bottom: 30px;
		}
		button {
			padding: 10px 20px;
			font-size: 1.2rem;
			background-color: #4CAF50;
			color: #fff;
			border: none;
			border-radius: 4px;
			cursor: pointer;
		}
		button:hover {
			background-color: #3e8e41;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>Disclaimer</h1>
		<p>Do not engage in disruptive behaviour during the competition. This includes, but is not limited to, the following:
                   launching          denial of service attacks;
                    launching          cyber attacks on other teams;
                   delete, modify, replace, or break the flags, services, and challenges (aside from the intended ones);
                      illegal, immoral or unethical conduct.</p>
		<p>By clicking the "I agree to the terms" button below, you acknowledge that you have read, understood, and agreed to the terms of this disclaimer.</p>
		<button id="agree-btn">I agree to the terms</button>
	</div>
	<script>
		const agreeBtn = document.getElementById("agree-btn");
		agreeBtn.addEventListener("click", function() {
			alert("Thank you for agreeing to the terms!");
		});
	</script>
</body>
</html>
