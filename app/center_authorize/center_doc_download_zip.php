<?php
require '../../includes/db-config.php';
session_start();

$id = intval($_GET['id']);
$query = $conn->query("SELECT center_name,center_doc FROM center_authorize WHERE id = $id");
$center = $query->fetch_assoc();
$center_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $center['center_name'] ?? 'center');
$files = [];
if (!empty($center['center_doc'])) {
    $files = json_decode($center['center_doc'], true);
    if (!is_array($files)) {
        $files = [];
    }
}

if (count($files) === 0) {
    die("No files found to download.");
}

$zip = new ZipArchive();
// $zipFileName = "$center_name.''.$id.zip";
$zipFileName = $center_name . "_docs" .".zip";
$zipFilePath = sys_get_temp_dir() . "/" . $zipFileName;

if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Failed to create ZIP file.");
}

foreach ($files as $file) {
    $filePath = __DIR__ . "/../../uploads/center_docs/" . $file;
    if (file_exists($filePath)) {
        // Add file to ZIP (with original filename)
        $zip->addFile($filePath, basename($file));
    }
}

$zip->close();

// ✅ Output file for download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
header('Content-Length: ' . filesize($zipFilePath));
readfile($zipFilePath);

// ✅ Clean up temp file
unlink($zipFilePath);
exit;
