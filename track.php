<?php
$folder = "uploads/";
$lyricsFolder = "lyrics/";
$titlesFolder = "titles/";

// فایل ورودی
$filename = isset($_GET['file']) ? $_GET['file'] : '';
$filePath = $folder . $filename;

if (!file_exists($filePath)) {
    die("آهنگ مورد نظر پیدا نشد.");
}

$baseName = pathinfo($filename, PATHINFO_FILENAME);
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$audioPath = $filePath;

// کاور
$coverDir = "covers/";
$coverPath = "";
$allowedCoverExts = ['jpg', 'jpeg', 'png', 'webp'];
foreach ($allowedCoverExts as $cExt) {
    $possibleCover = $coverDir . $baseName . "." . $cExt;
    if (file_exists($possibleCover)) {
        $coverPath = $possibleCover;
        break;
    }
}

// ویدیو
$videoDir = "video-links/";
$videoFile = $videoDir . $baseName . ".txt";
$videoEmbed = "";
if (file_exists($videoFile)) {
    $videoEmbed = trim(file_get_contents($videoFile));
}

// عنوان
$title = "بدون عنوان";
$titlePath = $titlesFolder . $baseName . ".txt";
if (file_exists($titlePath)) {
    $raw = file_get_contents($titlePath);
    if (trim($raw) !== '') $title = htmlspecialchars($raw);
}

// متن
$lyrics = "";
$lyricsPath = $lyricsFolder . $baseName . ".txt";
if (file_exists($lyricsPath)) {
    $lyrics = htmlspecialchars(file_get_contents($lyricsPath));
}

// URL تمیز برای سئو
$seoUrl = "https://samaxan.ir/music/" . $baseName;
?>
<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?> - Sama Xan</title>
  <link rel="shortcut icon" href="https://samaxan.ir/img/samaxan-gallery38.jpg"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- SEO -->
  <meta name="description" content="دانلود و پخش آنلاین آهنگ <?= $title ?> از سما خان همراه با متن کامل">
  <meta name="keywords" content="دانلود آهنگ <?= $title ?>, متن آهنگ <?= $title ?>, Sama Xan, سما خان <?= $title ?>">

  <!-- canonical -->
  <link rel="canonical" href="<?= $seoUrl ?>">

  <!-- OG -->
  <meta property="og:title" content="<?= $title ?> - Sama Xan">
  <meta property="og:description" content="دانلود و پخش آنلاین آهنگ <?= $title ?>">
  <meta property="og:type" content="music.song">
  <meta property="og:audio" content="https://samaxan.ir/<?= $audioPath ?>">
  <meta property="og:url" content="<?= $seoUrl ?>">
  <meta property="og:locale" content="fa_IR">
  <meta property="og:site_name" content="Sama Xan">
  <?php if ($coverPath): ?>
  <meta property="og:image" content="https://samaxan.ir/<?= $coverPath ?>">
  <?php endif; ?>

  <!-- Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "MusicRecording",
    "name": "<?= $title ?>",
    "url": "<?= $seoUrl ?>",
    "inLanguage": "fa",
    "byArtist": {
      "@type": "MusicGroup",
      "name": "Sama Xan"
    },
    "datePublished": "<?= date("Y-m-d", filemtime($audioPath)) ?>",
    "description": "دانلود آهنگ <?= $title ?> با متن کامل",
    "audio": {
      "@type": "AudioObject",
      "contentUrl": "https://samaxan.ir/<?= $audioPath ?>",
      "encodingFormat": "audio/<?= $ext ?>"
    }<?php if ($coverPath): ?>,
    "image": "https://samaxan.ir/<?= $coverPath ?>"
    <?php endif; ?>
  }
  </script>

  <!-- استایل با مسیر مطلق -->
  <link href="/style.css?v=20250860" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<header class="header">
  <a href="https://samaxan.ir" class="logo">🎵 Sama Xan</a>
  <div class="menu-icon" onclick="toggleMenu()">
    <i class="bi bi-list"></i>
  </div>
  <nav id="navMenu">
    <a href="https://samaxan.ir/bio.html" class="menulink">بیوگرافی</a>
    <a href="https://samaxan.ir/player.php" class="menulink">آهنگ‌ها</a>
    <a href="https://samaxan.ir/image-gallery.html" class="posts">گالری تصاویر</a>
    <a href="https://samaxan.ir/program-news.html" class="program-news">اخبار و برنامه ها</a>
    <a href="#footer" class="menulink">پروفایل ها</a>
  </nav>
</header>

<main class="track-page">
  <h1 class="track-music-header"><?= $title ?></h1>

  <?php if ($coverPath): ?>
    <div class="cover-container" style="text-align:center; margin-bottom: 1.5rem;">
      <img src="/<?= htmlspecialchars($coverPath) ?>" alt="کاور آهنگ <?= htmlspecialchars($title) ?>" style="max-width: 300px; width: 100%; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);" />
    </div>
  <?php endif; ?>

  <?php if (!empty($videoEmbed)): ?>
  <section class="video-box">
    <h2>🎬 موزیک ویدیو: <?= htmlspecialchars($title) ?></h2>
    <div class="video-embed">
      <?= $videoEmbed ?>
    </div>
  </section>
  <?php endif; ?>

  <div class="meta-box">
    <span>📁 فرمت: <?= strtoupper($ext) ?></span>
    <span>📅 تاریخ انتشار: <?= date("Y/m/d H:i", filemtime($audioPath)) ?></span>
  </div>

  <div class="share-buttons">
  <p>📤 اشتراک‌گذاری:</p>

  <!-- واتساپ -->
  <a href="https://wa.me/?text=<?= urlencode($title . ' - ' . $seoUrl) ?>" target="_blank" style="margin-right:10px; text-decoration:none;">
    <i class="bi bi-whatsapp"></i> واتساپ
  </a>

  <!-- تلگرام -->
  <a href="https://t.me/share/url?url=<?= urlencode($seoUrl) ?>&text=<?= urlencode($title) ?>" target="_blank" style="text-decoration:none;">
    <i class="bi bi-telegram"></i> تلگرام
  </a>
</div>

  <?php if ($lyrics): ?>
  <section class="lyrics-box">
    <h2>🎤 متن آهنگ:</h2>
    <pre><?= $lyrics ?></pre>
  </section>
  <?php endif; ?>

  <div class="player-single">
    <audio controls class="track-page-audio">
      <source src="/<?= $audioPath ?>" type="audio/<?= $ext ?>">
      مرورگر شما پشتیبانی نمی‌کند.
    </audio>
    <a class="btn-download" href="/download.php?file=<?= urlencode($filename) ?>&title=<?= urlencode($title) ?>">دانلود آهنگ <?= $title ?></a>
  </div>
  <?php
// آرایه کل آهنگ‌ها (ترجیحا بر اساس زمان یا لیست موجود در فولدر)
$allTracks = array_values(array_filter(scandir($folder), function($f) use ($folder) {
    return !is_dir($folder.$f) && in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['mp3','m4a','wav']);
}));

$currentIndex = array_search($filename, $allTracks);

$prevTracks = array_slice($allTracks, max(0, $currentIndex-5), min(5,$currentIndex));
$nextTracks = array_slice($allTracks, $currentIndex+1, 5);
?>
<section class="related-tracks-wrapper">
  <div class="related-tracks">
    <h2 class="track-page-h2">آهنگ های مرتبط</h2>
    <div class="track-links">
      <?php foreach($prevTracks as $t): 
          $b = pathinfo($t, PATHINFO_FILENAME);
          $tTitle = file_exists($titlesFolder.$b.'.txt') ? htmlspecialchars(file_get_contents($titlesFolder.$b.'.txt')) : $b;
      ?>
          <a href="/music/<?= $b ?>" class="track-card"><?= $tTitle ?></a>
      <?php endforeach; ?>

      <?php foreach($nextTracks as $t): 
          $b = pathinfo($t, PATHINFO_FILENAME);
          $tTitle = file_exists($titlesFolder.$b.'.txt') ? htmlspecialchars(file_get_contents($titlesFolder.$b.'.txt')) : $b;
      ?>
          <a href="/music/<?= $b ?>" class="track-card"><?= $tTitle ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
</main>

<footer id="footer" class="footer">
  <div class="column">
    <div class="logo">sama xan</div>
    <p>وب سایت رسمی سما خان</p>
  </div>
  <div class="column nav-links">
    <h4 style="color: #f9c74f">صفحات</h4>
    <a href="https://samaxan.ir/bio.html">بیوگرافی</a><br />
    <a href="https://samaxan.ir/player.php">آهنگ ها</a><br />
    <a href="https://samaxan.ir/image-gallery.html">گالری تصاویر</a><br />
  </div>
  <div class="column social-icons">
    <h4 style="color: #f9c74f">پروفایل ها</h4>
    <a href="https://www.instagram.com/samaxaaan"><i class="bi bi-instagram"></i></a>
    <a href="https://t.me/samaxanmusic1"><i class="bi bi-telegram"></i></a>
    <a href="https://www.tiktok.com/@samaxaaan"><i class="bi bi-tiktok"></i></a>
    <a href="https://m.youtube.com/@samaxaaan"><i class="bi bi-youtube"></i></a>
    <a href="mailto:samaxodayi04@gmail.com" aria-label="Send email"
       ><i class="bi bi-envelope"></i
    ></a>
  </div>
  <div class="copyright">© 2025 Sama xan</div>
</footer>
<script src="/script.js?v=20250807"></script>
</body>
</html>