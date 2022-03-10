<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
	<title>Queried Batch Payment Alert</title>

	<style>
		* {
			margin: 0;
			padding: 0;
			font-family: 'Lato', sans-serif;
		}

		.container {
			width:  100%;
			background: rgba(0, 0, 0, 0.1);
		}

		.row {
			margin: 0 auto;
			max-width: 95%;
			background: rgba(255, 255, 255, 1);
			height:  100vh;
		}

		.top-header {
			margin: 8px auto;
			overflow: hidden;
			width: 50%;
			max-width: 75%;
			min-width: 35%;
		}

		.top-header img {
			width: 100%;
		}

		.mid-section {
			padding: 30px;
			background-color: rgba(39, 174, 96,1.0);
			background-image: url("{{ asset('images/ncdmb_building.jpeg') }}");
			background-origin: center;
			background-size: cover;
			background-blend-mode: soft-light;
		}

		.mid-section h2 {
			text-align: center;
			color: #fff;
			padding: 10px 0;
			font-weight: 900;
			text-shadow: 2px 2px #000000;
		}

		.messageBody {
			padding: 30px 50px;
		}

		.salutation h4 {
			color: #7f8c8d;
		}

		.body {
			margin: 20px 0;
			text-align: justify;
			line-height: 1.5;
			font-size: 14px;
			color: #7f8c8d;
		}

		.footer {
			margin-top: 40px;
		}

		.footer p {
			font-size: 14px;
			color: #7f8c8d;
		}
	</style>

</head>
<body>
	<div class="container">
		<div class="row">
			<h1 class="top-header">
				<img src="{{ asset('images/logo.png') }}">
			</h1>

			<div class="mid-section">
				<h2>BUDGET PORTAL</h2>
			</div>

			<div class="messageBody">
				<div class="salutation">
					<h4 style="margin-top: 20px;">Dear {{ $batch->initiator->name }}</h4>
					<h2>QUERIED BATCH PAYMENT ALERT!!</h2>
				</div>

				<div class="body">
					<p>THE BATCH PAYMENT WITH CODE {{ $batch->batchCode }} HAS BEEN QUERIED BY AUDIT {{ now() }} WITH MESSAGE: <br> {{ $message }}</p>
				</div>

				<div class="footer">
					<p>Best Regards</p>
					<h4 style="margin-top: 6px;">Budget Portal Admin</h4>
				</div>
			</div>
		</div>
	</div>


</body>
</html>