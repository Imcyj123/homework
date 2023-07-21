<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$mysqli = require __DIR__ . "/signup.php";
	if ($_POST['email'] == NULL) {
		echo '<script>alert("please fill email")</script>';
	} else {
		$sql = sprintf("SELECT * FROM user
          WHERE email = '%s'",
			$mysqli->real_escape_string($_POST["email"]));

		$result = $mysqli->query($sql);
		$user = $result->fetch_assoc();

		if ($user) {
			if (password_verify($_POST["password"], $user["password_hash"])) {
				session_start();

				session_regenerate_id();

				$_SESSION["user_id"] = $user["id"];

				header("Location: index.php");
				exit;
			} else {
				echo '<script>alert("password error")</script>';
			}
		} else {
			echo '<script>alert("email error")</script>';
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
  <h1>Login</h1>

  <form method="post">
    <label for="email">Email</label>
    <input type="email" name="email" id="email"
        value="<?=htmlspecialchars($_POST["email"] ?? "")?>">

    <label for="password">Password</label>
    <input type="password" name="password" id="password">
    <button>Login</button>
  </form>
</body>
</html>