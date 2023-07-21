<?php
if (isset($_FILES['files'])) {
    $uploads_dir = 'uploads/';
    $zip_file = 'output.zip';
    $zip = new ZipArchive();

    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['files']['name'][$key];
            $file_tmp = $_FILES['files']['tmp_name'][$key];
            $zip->addFile($file_tmp, $file_name);
        }

        $zip->close();
    } else {
        echo "Failed to create ZIP file.";
        exit;
    }

    header("Location: index.php");
}
?>
