<?php
// sitemap builder for samaxan.ir — only static + track pages (clean URLs)

$outputFile = __DIR__ . "/sitemap.xml";

$staticPages = [
    ["loc" => "https://samaxan.ir/", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/bio.html", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/bio-en.html", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/bio-ku.html", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/player.php", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/image-gallery.html", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/program-news.html", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/index-en.html", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/index-ku.html", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/videos.php", "lastmod" => date("Y-m-d")],
    ["loc" => "https://samaxan.ir/download.php", "lastmod" => date("Y-m-d")]
];

$folders = [
    "uploads" => __DIR__ . "/uploads/"
];

// helper for XML escaping
function xml($v) { return htmlspecialchars($v, ENT_XML1 | ENT_COMPAT, 'UTF-8'); }

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// static pages
foreach ($staticPages as $p) {
    $xml .= "  <url>\n";
    $xml .= "    <loc>" . xml($p['loc']) . "</loc>\n";
    $xml .= "    <lastmod>{$p['lastmod']}</lastmod>\n";
    $xml .= "    <changefreq>weekly</changefreq>\n";
    $xml .= "    <priority>0.8</priority>\n";
    $xml .= "  </url>\n";
}

// tracks (clean URLs)
$audioFiles = glob($folders['uploads'] . "*.{mp3,wav,ogg,m4a,flac}", GLOB_BRACE);
foreach ($audioFiles as $file) {
    $lastmod  = gmdate("Y-m-d", filemtime($file));
    $baseName = pathinfo($file, PATHINFO_FILENAME);
    $trackUrl = "https://samaxan.ir/music/" . rawurlencode($baseName);

    $xml .= "  <url>\n";
    $xml .= "    <loc>" . xml($trackUrl) . "</loc>\n";
    $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
    $xml .= "    <changefreq>weekly</changefreq>\n";
    $xml .= "    <priority>0.9</priority>\n";
    $xml .= "  </url>\n";
}

$xml .= "</urlset>";

file_put_contents($outputFile, $xml);
echo "✅ سایت‌مپ ساخته شد: sitemap.xml (فقط استاتیک + ترک‌ها با URL تمیز)";