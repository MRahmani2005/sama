<?php
// ====== تنظیمات پایه ======
$base = 'https://https://samaxan.ir'; // ← دامنه نهایی سایتت
$root = __DIR__;

// دایرکتوری‌ها و فایل‌هایی که نباید وارد سایت‌مپ شوند
$excludeDirs = ['assets','css','js','img','images','admin','includes','uploads','fonts','.git','covers','lyrics','titles','video-links'];
$excludeFiles = ['404.php','403.php','500.php','sitemap.xml','sitemap_index.xml','generate-sitemap.php','robots.txt','error_log'];

// اگر می‌خوای صفحاتی را اجباراً حذف کنی (مثلاً thank-you)، اینجا بذار:
$excludeByPathContains = ['/login', '/search', '/thank-you'];

// اگر فایل‌ها meta robots noindex داشته باشند، حذف شوند؟
$skipNoindexMeta = true;

// ====== پیمایش فایل‌ها ======
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$urls = []; // url => lastmod (timestamp)

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) continue;

    $ext = strtolower($fileInfo->getExtension());
    if (!in_array($ext, ['html','htm','php'])) continue;

    $absPath = $fileInfo->getPathname();
    $relPath = str_replace($root, '', $absPath);
    $relPath = str_replace('\\', '/', $relPath);
    if ($relPath === '' || $relPath[0] !== '/') $relPath = '/' . ltrim($relPath, '/');

    // رد کردن دایرکتوری‌های ممنوع
    $topDir = explode('/', trim($relPath, '/'))[0] ?? '';
    if (in_array($topDir, $excludeDirs, true)) continue;

    // رد کردن برخی فایل‌ها
    $filename = basename($relPath);
    if (in_array($filename, $excludeFiles, true)) continue;
    if (substr($filename, 0, 1) === '_') continue; // پارتشیال‌ها مثل _header.php

    // حذف مسیرهای خاص
    foreach ($excludeByPathContains as $needle) {
        if (stripos($relPath, $needle) !== false) continue 2;
    }

    // تبدیل index.* به مسیر دایرکتوری
    if (preg_match('~/(index)\.(html?|php)$~i', $relPath)) {
        $path = preg_replace('~/(index)\.(html?|php)$~i', '/', $relPath);
    } else {
        $path = $relPath;
    }

    // اگر فایل noindex داشت، ردش کن
    if ($skipNoindexMeta && in_array($ext, ['html','htm','php'])) {
        $sample = @file_get_contents($absPath, false, null, 0, 20000); // فقط 20KB اول
        if ($sample && preg_match('~<meta\s+name=["\']robots["\']\s+content=["\'][^"\']*noindex~i', $sample)) {
            continue;
        }
    }

    $url = rtrim($base, '/') . $path;
    // URL باید بدون querystring وارد سایت‌مپ شود
    $url = preg_replace('~\?.*$~', '', $url);

    $urls[$url] = filemtime($absPath);
}

// اطمینان از وجود صفحه اصلی
$homeUrl = rtrim($base, '/') . '/';
if (!isset($urls[$homeUrl])) {
    $homeIndex = $root . '/index.html';
    $urls[$homeUrl] = file_exists($homeIndex) ? filemtime($homeIndex) : time();
}

// ====== ساخت XML ======
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;

$urlset = $dom->createElement('urlset');
$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$dom->appendChild($urlset);

// مرتب‌سازی اختیاری بر اساس URL برای ثبات خروجی
ksort($urls);

foreach ($urls as $url => $mtime) {
    $urlEl = $dom->createElement('url');

    $loc = $dom->createElement('loc');
    $loc->appendChild($dom->createTextNode($url));
    $urlEl->appendChild($loc);

    // ISO8601
    $last = $dom->createElement('lastmod', gmdate('c', $mtime));
    $urlEl->appendChild($last);

    // اولویت تقریبی بر اساس عمق مسیر
    $pathOnly = parse_url($url, PHP_URL_PATH) ?: '/';
    $depth = $pathOnly === '/' ? 0 : substr_count(trim($pathOnly,'/'), '/');
    $priority = ($pathOnly === '/') ? '1.0' : (($depth <= 0) ? '0.8' : '0.5');
    $urlEl->appendChild($dom->createElement('priority', $priority));

    // فرکانس تغییر پیشنهادی (اختیاری)
    $urlEl->appendChild($dom->createElement('changefreq', 'weekly'));

    $urlset->appendChild($urlEl);
}

// ====== خروجی ======
file_put_contents($root . '/sitemap.xml', $dom->saveXML());

// نمایش نتیجه در مرورگر/CLI
header('Content-Type: text/plain; charset=UTF-8');
echo "Wrote " . count($urls) . " URLs to sitemap.xml\n";