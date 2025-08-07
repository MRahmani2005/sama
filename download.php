<?php
$filename = isset($_GET['file']) ? basename($_GET['file']) : '';
$titleParam = isset($_GET['title']) ? trim($_GET['title']) : '';
$filepath = 'uploads/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    echo "فایل پیدا نشد!";
    exit;
}

$ext = pathinfo($filename, PATHINFO_EXTENSION);
$downloadName = $filename;

if ($titleParam !== '') {
    // تمیز کردن عنوان
    $safeTitle = preg_replace('/[^\\p{L}0-9_\\s-]/u', '', $titleParam); // اجازه دادن به حروف فارسی
    $safeTitle = trim($safeTitle);
    $downloadName = $safeTitle . '.' . $ext;
}

// ارسال فایل برای دانلود با نام فارسی صحیح
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($downloadName)); // پشتیبانی از نام فارسی
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit;
