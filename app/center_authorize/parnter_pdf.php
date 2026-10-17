<?php

require '../../includes/db-config.php';

use setasign\Fpdi\Fpdi;

require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

$color_pdf  = isset($_GET['colortype']) ? strtolower(trim($_GET['colortype'])) : '';
$outputMode = isset($_GET['output']) ? strtolower(trim($_GET['output'])) : 'zip'; // zip | merge

/** ---------------- Helpers ---------------- */
if (!function_exists('sanitize_filename')) {
    function sanitize_filename($name)
    {
        $safe = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $name);
        return trim($safe, '_');
    }
}

if (!function_exists('wrapTextByChars')) {
    function wrapTextByChars($text, $maxChars)
    {
        if ($maxChars <= 0) return [$text];
        $out = [];
        $len = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
        $buf = '';
        for ($i = 0; $i < $len; $i++) {
            $ch = function_exists('mb_substr') ? mb_substr($text, $i, 1, 'UTF-8') : substr($text, $i, 1);
            $buf .= $ch;
            $cur = function_exists('mb_strlen') ? mb_strlen($buf, 'UTF-8') : strlen($buf);
            if ($cur >= $maxChars) {
                $out[] = $buf;
                $buf = '';
            }
        }
        if ($buf !== '') $out[] = $buf;
        return $out;
    }
}
if (!function_exists('wrapTextByWords')) {
    function wrapTextByWords(string $text, int $maxWords): array
    {
        $words = preg_split('/\s+/', trim($text));
        $lines = [];
        $line = [];

        foreach ($words as $word) {
            $line[] = $word;
            if (count($line) >= $maxWords) {
                $lines[] = implode(' ', $line);
                $line = [];
            }
        }

        if (!empty($line)) {
            $lines[] = implode(' ', $line);
        }

        return $lines;
    }
}


if (!function_exists('generate_certificate_pdf')) {
    function generate_certificate_pdf(array $data, string $templatePath, string $dest, string $filename, string $color_pdf, ?FPDI $mergePdf = null)
    {
        $pdf = $mergePdf ?? new FPDI();

        if ($color_pdf == "color") {
            $pdf->setSourceFile($templatePath);
            $tplIdx = $pdf->importPage(1);
        }

        $pdf->AddPage();
        if ($color_pdf == "color") {
            $pdf->useTemplate($tplIdx);
        }
        $pdf->SetFont('helvetica', '', 11);

        // -------- Center Name --------
        // $centerName = ucwords($data['center_name'] ?? '');
        // $centerNameLines = wrapTextByWords($centerName, ($data['type_id'] == 47) ? 8 : 8);

        // $cy = ($data['type_id'] == 47) ? 93.3 : 92.3;
        // $cx = 45;
        // foreach ($centerNameLines as $line) {
        //     $pdf->SetXY($cx, $cy);
        //     $pdf->MultCell(110, 10, $line, 0, 1, 'L');
        //     $cy += 11.7;
        //     $cx -= 1;
        // }
        $centerName = ucwords($data['center_name'] ?? '');
        $center_name = str_replace("’", "'", $centerName);
        $cy = ($data['type_id'] == 47) ? 93.3 : 92.3;
        $cx = 45;
        $pdf->SetXY($cx, $cy);
        $pdf->MultiCell(140,11, $center_name, 0,  'L' );

        // -------- Address --------
        // $address_len = ($data['type_id'] == 47) ? 54 : 52;
        // $addressLines = wrapTextByChars($data['address'] ?? '', $address_len);
        // $ay = ($data['type_id'] == 47) ? 114.2 : 114.4;
        // $ax = 45.8;

        // $lineIndex = 0;
        // foreach ($addressLines as $line) {
        //     $pdf->SetXY($ax, $ay);
        //     $pdf->Cell(120, 10, $line, 0, 1, 'L');
        //     $ay += 10.6;
        //     if ($lineIndex === 0) {
        //         $ax -= 20;
        //     }
        //     $lineIndex++;
        // }
        // $address = ucwords($data['address'] ?? '');
        // $ay = ($data['type_id'] == 47) ? 114.2 : 114.4;
        // $ax = 45;
        // $pdf->SetXY($ax, $ay);
        // $pdf->MultiCell(145,11, $address, 0,  'L' );
        $Address = ucwords(($data['address']) ?? '');
         $address = str_replace("’", "'", $Address);
$maxWidth = 145; // same as your MultiCell width
$ay = ($data['type_id'] == 47) ? 114.2 : 114.4;
$ax = 45;

// Break into lines without splitting words
$words = explode(' ', $address);
$lines = [];
$current = '';

foreach ($words as $word) {
    $test = ($current === '') ? $word : $current . ' ' . $word;
    if ($pdf->GetStringWidth($test) <= $maxWidth) {
        $current = $test;
    } else {
        $lines[] = $current;
        $current = $word;
    }
}
if ($current !== '') {
    $lines[] = $current;
}

// Print lines, shifting only from 2nd line
foreach ($lines as $i => $line) {
    $curX = ($i === 0) ? $ax : $ax - 18; // shift -20 from second line
    $pdf->SetXY($curX, $ay);
    $pdf->Cell($maxWidth, 11, $line, 0, 1, 'L');
    $ay += 10.5;
}

        // -------- Programs (max 3) --------
        $programsRaw = $data['programs'] ?? '';
        $programArray = array_map('trim', explode(',', $programsRaw));
        $programArray = array_slice($programArray, 0, 3);

        $y = 191.7;
        $x = 41.5;
        foreach ($programArray as $prog) {
            $pdf->SetXY($x, $y);
            $pdf->Cell(120, 10, $prog, 0, 1, 'L');
            $y += 9;
        }

        // -------- If merging --------
        if ($mergePdf) {
            return $pdf;
        }

        // -------- Normal output --------
        if ($dest === 'F') {
            $pdf->Output($filename, 'F');
        } elseif ($dest === 'D') {
            $pdf->Output($filename, 'D');
        } else {
            $pdf->Output($filename, 'I');
        }
    }
}

if (!function_exists('select_template_for_row')) {
    function select_template_for_row(array $row, string $color_pdf): string
    {
        $typeId = isset($row['type_id']) ? (int)$row['type_id'] : 0;
        if ($color_pdf === "color") {
            $path = ($typeId === 47)
                ? __DIR__ . '/bvoc_certificate.pdf'
                : __DIR__ . '/skill_certificate.pdf';
        } else {
            $path = __DIR__ . '/dummy.pdf';
        }
        if (!file_exists($path)) {
            die('Template not found: ' . htmlspecialchars($path));
        }
        return $path;
    }
}

/** ---------------- Routing: single or batch ---------------- */

$batch = isset($_GET['batch']) ? trim($_GET['batch']) : '';
$programs_type = isset($_GET['programs_type']) ? trim($_GET['programs_type']) : '';

if ($batch === '' || $batch === '0' || $batch == 0) {
    /** ---------- Single mode ---------- */
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) die('Invalid ID');
    $sql = "SELECT * FROM center_authorize WHERE id = $id LIMIT 1";
    $res = $conn->query($sql);
    if (!$res || $res->num_rows === 0) die('No records found!');
    $row = $res->fetch_assoc();
    $templatePath = select_template_for_row($row, $color_pdf);
    $safeName = sanitize_filename(($row['center_name'] ?? 'certificate') . '_' . $id);
    $downloadName = "authorize_center_certificate_{$safeName}.pdf";
    if (ob_get_length()) while (ob_get_level()) ob_end_clean();
    generate_certificate_pdf($row, $templatePath, 'I', $downloadName, $color_pdf);
    exit;
} else {
    /** ---------- Batch mode ---------- */
    if ($batch === 'all' && ($programs_type === '' || $programs_type === '0' || is_null($programs_type))) {
        $sql = "SELECT * FROM center_authorize ORDER BY id ASC";
        $zipLabel = 'all';
    } elseif ($batch === 'all' && in_array($programs_type, ['47', '48'])) {
        $programs_type_int = (int)$programs_type;
        $sql = "SELECT * FROM center_authorize WHERE type_id = $programs_type_int ORDER BY id ASC";
        $zipLabel = 'all_program_' . $programs_type_int;
    } elseif ($batch !== 'all' && ($programs_type === '' || $programs_type === '0' || is_null($programs_type))) {
        $batchInt = (int)$batch;
        $sql = "SELECT * FROM center_authorize WHERE batch = $batchInt ORDER BY id ASC";
        $zipLabel = $batchInt;
    } else {
        $batchInt = (int)$batch;
        $programs_type_int = (int)$programs_type;
        $sql = "SELECT * FROM center_authorize WHERE batch = $batchInt AND type_id = $programs_type_int ORDER BY id ASC";
        $zipLabel = $batchInt . '_program_' . $programs_type_int;
    }

    $res = $conn->query($sql);
    if (!$res || $res->num_rows === 0) die('No records found for batch ' . htmlspecialchars((string)$batch));

    if ($outputMode === 'merge') {
        // --------- MERGED PDF ---------
        $pdf = new FPDI();
        while ($row = $res->fetch_assoc()) {
            $templatePath = select_template_for_row($row, $color_pdf);
            generate_certificate_pdf($row, $templatePath, '', '', $color_pdf, $pdf);
        }
        $downloadName = "authorize_center_batch_{$zipLabel}_merged.pdf";
        if (ob_get_length()) while (ob_get_level()) ob_end_clean();
        $pdf->Output($downloadName, 'I');
        exit;
    } else {
        // --------- ZIP of PDFs ---------
        if (!class_exists('ZipArchive')) die('ZipArchive extension is not enabled on this server.');
        $tempDir = __DIR__ . '/temp_pdfs_' . time();
        if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
        while ($row = $res->fetch_assoc()) {
            $templatePath = select_template_for_row($row, $color_pdf);
            $idForName = isset($row['id']) ? (int)$row['id'] : time();
            $safeName  = sanitize_filename(($row['center_name'] ?? 'certificate') . '_' . $idForName);
            $pdfPath   = $tempDir . "/authorize_center_certificate_{$safeName}.pdf";
            generate_certificate_pdf($row, $templatePath, 'F', $pdfPath, $color_pdf);
        }
        $zipFilename = "authorize_center_batch_{$zipLabel}.zip";
        $zipPath     = __DIR__ . "/{$zipFilename}";
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            foreach (glob($tempDir . '/*.pdf') as $f) @unlink($f);
            @rmdir($tempDir);
            die('Could not create zip file.');
        }
        foreach (glob($tempDir . '/*.pdf') as $pdfFile) {
            $zip->addFile($pdfFile, basename($pdfFile));
        }
        $zip->close();
        if (ob_get_length()) while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zipFilename) . '"');
        header('Content-Length: ' . filesize($zipPath));
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: public');
        readfile($zipPath);
        foreach (glob($tempDir . '/*.pdf') as $f) @unlink($f);
        @rmdir($tempDir);
        @unlink($zipPath);
        exit;
    }
}
