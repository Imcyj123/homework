<?php
session_start();

if (isset($_SESSION["user_id"])) {
	$mysqli = require __DIR__ . "/signup.php";
	$sql = "SELECT * from user
			WHERE id = {$_SESSION["user_id"]}";
	$result = $mysqli->query($sql);
	$user = $result->fetch_assoc();
	$inactive = 900;
	if (!isset($_SESSION['timeout'])) {
		$_SESSION['timeout'] = time() + $inactive;
	}
	$session_life = time() - $_SESSION['timeout'];
	if ($session_life >= $inactive) {
		session_destroy();
		header("Location:login.php");
	}
	$_SESSION['timeout'] = time();
}
// print_r($_SESSION);
?>


<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>Message Board</title>
</head>
<body>
	<h1>Home</h1>
	<?php if (isset($_SESSION["user_id"])): ?>
		<p>Hello <?=htmlspecialchars($user["name"])?> </p>
		<p><a href="logout.php">Log out</a></p>
		<hr>
		<?php else: ?>
		<a href="login.php"><button>login</button></a>
		<a href="signup.html"><button>signup</button></a>
		<?php endif;?>
    <h1>Message Board</h1>
    <style type="text/css">
			.top{display: flex;}
			.first{font-size: 20px;}
			.id{width: 5%;}
			.user{width: 10%;}
			.title{width: 10%;}
			.content{width: 20%;}
			.cur_time{width: 20%;}
			.action{margin-left:10%;width:30%;}
		</style>
<?php

$mysqli = new mysqli("localhost", "root", "", "message_boards");

if ($mysqli->connect_errno) {
	echo "連接資料庫失敗：" . $mysqli->connect_error;
	exit();
}

// 檢查是否有新增留言的請求

if (isset($_POST['submit'])) {
	$title = $_POST['title'];
	$message = $_POST['message'];
	$guid = bin2hex(openssl_random_pseudo_bytes(16));

	// 檢查是否有檔案上傳
	if (isset($_FILES['file']) && isset($_FILES['file']['name'])) {
		$fileCount = count($_FILES['file']['name']);
		for ($i = 0; $i < $fileCount; $i++) {
			$file_name = $_FILES['file']['name'][$i];
			$fileType = $_FILES['file']['type'][$i];
			$file_tmp = $_FILES['file']['tmp_name'][$i];
			$fileError = $_FILES['file']['error'][$i];
			$fileSize = $_FILES['file']['size'][$i];
			$file_path = 'uploads/' . $guid . $file_name;
			move_uploaded_file($file_tmp, $file_path);
			// Process each file here (e.g., move to desired location, validate, etc.)
		}
	}

	// if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {

	// 	if ($_FILES['file']["size"] > 102400) {
	// 		exit("File too large (max 1MB)");
	// 	} else {
	// 		print_r($_FILES);
	// 		print_r(count($_FILES['file']['name']));
	// 		$file_name = $_FILES['file']['name'];
	// 		$file_tmp = $_FILES['file']['tmp_name'];
	// 		$guid = bin2hex(openssl_random_pseudo_bytes(16));
	// 		// $post_man = $user['name'];
	// 		// $post_time = time();
	// 		$file_path = 'uploads/' . $guid . $file_name;

	// 		// 移動上傳的檔案到目標資料夾
	// 		move_uploaded_file($file_tmp, $file_path);
	// 	}
	// } else {
	// 	$file_path = null;
	// }

	// 插入留言到資料庫
	// $sql = "INSERT INTO messages (username, title, message, file_path, timestamp) VALUES (?	, ?, ?, ?, NOW())";
	// $stmt = $mysqli->prepare($sql);
	// $stmt->bind_param("sss", $username, $message, $file_path);
	// $stmt->execute();
	// $stmt->close();

	$sql = "INSERT INTO messages (username, title, message, guid, file_path, timestamp) VALUES (?, ?, ?, ?,?, NOW())";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("sssss", $user["name"], $title, $message, $guid, $file_path);
	$stmt->execute();
	$stmt->close();

}
if (isset($_POST['delete'])) {
	if ($_POST["username"] === $user["name"]) {
		$message_id = $_POST['message_id'];

		// 刪除檔案
		$sql = "SELECT file_path, guid FROM messages WHERE id = ?";
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param("i", $message_id);
		$stmt->execute();
		$stmt->bind_result($file_path, $guid);
		$stmt->fetch();
		$stmt->close();

		// if ($row['file_path'] !== null) {
		// 	$pattern = "{$row['guid']}*";
		// 	$files = glob($pattern);
		// 	if ($files) {
		// 		foreach ($files as $file) {
		// 			if (file_exists($file)) {
		// 				unlink("uploads/" . $file);
		// 			}
		// 		}
		// 	}
		// }
		if ($file_path !== null) {
			$pattern = "uploads/{$guid}*";
			$files = glob($pattern);
			if ($files) {
				foreach ($files as $file) {
					if (file_exists($file)) {
						unlink($file);
					}
				}
			}
		}

		// 刪除留言
		$sql = "DELETE FROM messages WHERE id = ?";
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param("i", $message_id);
		$stmt->execute();
		$stmt->close();
	} else {
		exit("this is not your comment");
	}
}

// 顯示留言列表
$sql = "SELECT * FROM messages ORDER BY timestamp DESC";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
	echo "<div class='top first'>
				<div class='id'><strong>id</strong></div>
				<div class='user'><strong>user</strong></div>
				<div class='title'><strong>title</strong></div>
				<div class='content'><strong>message</strong></div>
				<div class='cur_time'><strong>time</strong></div>
				<div class='action'><strong>action</strong></div>
			</div>";
	while ($row = $result->fetch_assoc()) {
		echo "<div class='top'>
          <div class='id'>" . $row['id'] . "</div>
          <div class='user'>" . $row['username'] . "</div>
          <div class='title'>" . $row['title'] . "</div>
          <div class='content'>" . $row['message'] . "</div>
          <div class='cur_time'>" . $row['timestamp'] . "</div>";

// 		if ($row['file_path'] !== null) {
// 			// echo "<div class='file'><p><button><a href='" . $row['file_path'] . "' target='_blank'>查看</a></button></p></div>";
// 			// echo "<div>";
// 			// $patternn = $row['file_path'] . "*";
// 			// $files = glob("uploads/" . $patternn);
// 			// foreach ($files as $file) {
// 			// 	echo "file:" . $file . "<br>";
// 			// }
// 			// echo "</div>";
// 			echo "<div class='file'>
//             <p>
//                 <button onclick=\"showImage('" . $row['file_path'] . "')\">查看</button>
//             </p>
//             $pattern = "b827615d1ac1b5b06a1551a3d4f25962*";
// $files = glob("path/to/directory/" . $pattern);
// foreach ($files as $file) {
//     // Process each file here
//     echo "File: " . $file . "<br>";
// }
//         </div>
//         <div id=\"imageContainer\"></div>";
// 		}

		if (isset($_SESSION["user_id"]) && $row["username"] === $user["name"]) {
			echo "<form method='POST' action=''>
                    <input type='hidden' name='username' value='" . $user['name'] . "'>
                    <input type='hidden' name='message_id' value='" . $row['id'] . "'>
                    <input type='submit' name='delete' value='刪除'>
                    </form>";
		}

		echo "</div>";

		if ($row['file_path'] !== null) {
			$patternn = "uploads/{$row['guid']}*";
			$files = glob($patternn);
			echo "<div>";
			if ($files) {
				foreach ($files as $file) {
					$fex = explode(".", $file);
					if ($fex[1] === "jpg") {
						echo "<div><img src='{$file}' loading='lazy'></div><br>";
					}
					if ($fex[1] === 'docx' || $fex[1] === 'doc' || $fex[1] === 'pdf') {
						echo "<a href='{$file}'> $file </a>";
					}
				}
			}
			echo "</div>";
			// Store the file names in a JSON format to pass it to JavaScript
			// $fileList = json_encode($files);
			echo "<div class='file'>
            <p>";
		}

// 檢查是否有更新留言的請求
		if (isset($_POST['update'])) {
			$message_id = $_POST['message_id'];
			$new_title = $_POST['new_title'];
			$new_message = $_POST['new_message'];

			// 更新留言
			$sql = "UPDATE messages SET title = ?, message = ? WHERE id = ?";
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param("ssi", $new_title, $new_message, $message_id);
			$stmt->execute();
			$stmt->close();
		}

		if (isset($_SESSION["user_id"]) && $row["username"] === $user["name"]) {
			echo "<form method='POST' action=''>
                    <input type='hidden' name='username' value='" . $user['name'] . "'>
                    <input type='hidden' name='message_id' value='" . $row['id'] . "'>
                    <textarea name='new_title' rows='4' placeholder='title' required></textarea>
                    <textarea name='new_message' rows='4' placeholder='message' required></textarea>
                    <input type='submit' name='update' value='更新'>
                    </form>";
		}

		echo "<hr>";
	}
} else {
	echo "<p>No messages yet.</p>";
}

$mysqli->close();
?>

    <h2>Add a Message</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="title">title:</label>
        <input type="text" name="title" required><br>

        <label for="message">Message:</label>
        <textarea name="message" rows="4" required></textarea><br>

        <label for="file">File:</label>
        <!-- <input type="file" name="file"><br> -->

        <input type="file" name="file[]" multiple="multiple"><br>

        <input type="submit" name="submit" value="Post Message">
    </form>


</body>
</html>
