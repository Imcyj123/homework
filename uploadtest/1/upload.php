<?php
if (isset($_FILES['files'])) {
    $uploads_dir = 'uploads/';

    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        $file_name = $_FILES['files']['name'][$key];
        $file_tmp = $_FILES['files']['tmp_name'][$key];

        move_uploaded_file($file_tmp, $uploads_dir . $file_name);
    }

    header("Location: index.php");
}
?>
