<?php
$zip_file = 'output.zip';

if (file_exists($zip_file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($zip_file));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($zip_file));
    readfile($zip_file);
    exit;
} else {
    echo "ZIP file not found.";
}
?>
