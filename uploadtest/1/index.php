<!DOCTYPE html>
<html>
<head>
    <title>File Upload and Download</title>
</head>
<body>
    <h1>File Upload and Download</h1>

    <h2>Upload Files</h2>
    <form method="POST" action="upload.php" enctype="multipart/form-data">
        <label for="file">Select Files:</label>
        <input type="file" name="files[]" multiple><br>
        <input type="submit" name="submit" value="Upload">
    </form>

    <hr>

    <h2>Download Files</h2>
    <?php
    // 讀取上傳的檔案列表
    $files = scandir('uploads');
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<p><a href='download.php?file=$file'>$file</a></p>";
        }
    }
    ?>
</body>
</html>
