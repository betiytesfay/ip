<!DOCTYPE html>
<html>
<head>
	<title>Email Verification</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-OgVRvuATP1z7JjHLkuOU6Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0pq" crossorigin="anonymous">
</head>
<body>
	<div class="container mt-5">
		<h1>Email Verification</h1>
		<form method="post" action="register.php">
			<div class="form-group">
				<label>Email</label>
				<input type="email" name="email" class="form-control" required>
			</div>
			<div class="form-group">
				<label>Password</label>
				<input type="password" name="password" class="form-control" required>
			</div>
			<button type="submit" class="btn btn-primary">Register</button>
		</form>
	</div>
</body>
</html>
