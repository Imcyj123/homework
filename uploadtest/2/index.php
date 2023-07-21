<!DOCTYPE html>
<html>
<head>
    <title>File Upload and Download</title>
</head>
<body>
    <h1>File Upload and Download</h1>

    <h2>Upload Files</h2>
    <form method="POST" action="upload.php" enctype="multipart/form-data">
        <label for="file1">Select File 1:</label>
        <input type="file" name="files[]" id="file1"><br>

        <label for="file2">Select File 2:</label>
        <input type="file" name="files[]" id="file2"><br>

        <input type="submit" name="submit" value="Upload">
    </form>

    <hr>

    <h2>Download ZIP</h2>
    <?php
    if (file_exists('output.zip')) {
        echo "<p><a href='download.php'>Download ZIP</a></p>";
    } else {
        echo "<p>No ZIP file available.</p>";
    }
    ?>
</body>
</html>
