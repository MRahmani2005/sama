<?php
$folder = "uploads/";
$lyricsFolder = "lyrics/";
$titlesFolder = "titles/";

// سازگار با همه نسخه‌ها
$filename = isset($_GET['file']) ? $_GET['file'] : '';
$filePath = $folder . $filename;

if (!file_exists($filePath)) {
    die("آهنگ مورد نظر پیدا نشد.");
}

$baseName = pathinfo($filename, PATHINFO_FILENAME);
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$audioPath = $filePath;
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
$videoDir = "video-links/";
$videoFile = $videoDir . pathinfo($filename, PATHINFO_FILENAME) . ".txt";
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
?>
<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?> - Sama Xan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="دانلود و پخش آنلاین آهنگ <?= $title ?> از سما خان">

  <!-- OG -->
  <meta property="og:title" content="<?= $title ?> - Sama Xan">
  <meta property="og:description" content="دانلود و پخش آنلاین آهنگ <?= $title ?>">
  <meta property="og:type" content="music.song">
  <meta property="og:audio" content="https://samaxan.ir/<?= $audioPath ?>">
  <meta property="og:url" content="https://samaxan.ir/track.php?file=<?= urlencode($filename) ?>">
  <meta property="og:locale" content="fa_IR">
  <meta property="og:site_name" content="Sama Xan">

  <!-- Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "MusicRecording",
    "name": "<?= $title ?>",
    "url": "https://samaxan.ir/track.php?file=<?= urlencode($filename) ?>",
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
    }
  }
  </script>

  <!-- استایل -->
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css?v=<?= filemtime('style.css') ?>">
  <style>
  /* پایه و فونت */
  body {
    font-family: 'Vazirmatn', sans-serif;
    background-color: #f9f9f9;
    color: #333;
    margin: 0;
    padding: 0;
    line-height: 1.6;
    direction: rtl;
  }

  /* هدر */
  .header {
    background: #121212;
    color: #f9c74f;
    padding: 15px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 6px rgba(0,0,0,0.5);
  }

  .header .logo {
    font-weight: 800;
    font-size: 1.8rem;
    text-decoration: none;
    color: #f9c74f;
  }

  .header .menu-icon {
    font-size: 1.6rem;
    cursor: pointer;
    color: #f9c74f;
  }

  #navMenu a {
    color: #f9c74f;
    margin-left: 20px;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
  }

  #navMenu a:hover {
    color: #fff;
  }

  /* بخش اصلی صفحه */
  main.track-page {
    max-width: 700px;
    background: #fff;
    margin: 2rem auto 4rem auto;
    padding: 2rem 2.5rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }

  /* عنوان آهنگ */
  .music-header {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    color: #121212;
    text-align: center;
    border-bottom: 2px solid #f9c74f;
    padding-bottom: 10px;
  }

  /* پلیر صوتی */
  .player-single {
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .player-single audio {
    width: 100%;
    border-radius: 10px;
    outline: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  }

  .btn-download {
    display: inline-block;
    margin-top: 10px;
    background-color: #f9c74f;
    color: #121212;
    padding: 10px 20px;
    border-radius: 30px;
    font-weight: 700;
    text-decoration: none;
    transition: background-color 0.3s ease;
  }

  .btn-download:hover {
    background-color: #e0b644;
  }

  /* بخش ویدیو */
  .video-box {
    margin: 2rem 0;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    background: #000;
    padding: 1rem;
    text-align: center;
  }

  .video-box h2 {
    color: #f9c74f;
    margin-bottom: 1rem;
    font-weight: 700;
  }

  .video-embed iframe,
  .video-embed video {
    width: 100%;
    max-height: 400px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
  }

  /* متا دیتا */
  .meta-box {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    text-align: center;
    font-weight: 600;
  }

  /* دکمه‌های اشتراک گذاری */
  .share-buttons {
    text-align: center;
    margin-bottom: 2rem;
  }

  .share-buttons p {
    font-weight: 700;
    margin-bottom: 10px;
    color: #121212;
  }

  .share-buttons a {
    margin: 0 12px;
    text-decoration: none;
    font-size: 1.3rem;
    color: #444;
    transition: color 0.3s ease;
    display: inline-block;
  }

  .share-buttons a:hover {
    color: #f9c74f;
  }

  /* متن آهنگ */
  .lyrics-box {
    background: #fff7d6;
    border: 1px solid #f9c74f;
    padding: 1.5rem;
    border-radius: 12px;
    font-size: 1rem;
    white-space: pre-wrap;
    line-height: 1.5;
    color: #444;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  /* فوتر */
  .footer {
    background: #121212;
    color: #f9c74f;
    padding: 1.5rem 2rem;
    text-align: center;
    font-size: 0.9rem;
  }

  .footer .logo {
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  /* ستون‌های فوتر */
  .footer .column {
    margin-bottom: 1rem;
  }

  .footer .nav-links a,
  .footer .social-icons a {
    color: #f9c74f;
    margin: 0 5px;
    text-decoration: none;
    font-size: 1.3rem;
  }

  .footer .nav-links a:hover,
  .footer .social-icons a:hover {
    color: #fff;
  }

  /* آیکون‌ها */
  .footer .social-icons a {
    margin-left: 10px;
  }

  /* ریسپانسیو */
  @media (max-width: 768px) {
    main.track-page {
      margin: 1rem 1rem 4rem 1rem;
      padding: 1.2rem 1.5rem;
    }

    .music-header {
      font-size: 1.6rem;
    }

    .video-embed iframe,
    .video-embed video {
      max-height: 250px;
    }
    .cover-container img {
  max-width: 300px;
  width: 100%;
  border-radius: 15px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  display: inline-block;
}
  }
</style>
</head>
<body>

<header class="header">
  <a href="https://samaxan.ir" class="logo">🎵 Sama Xan</a>
  <div class="menu-icon" onclick="toggleMenu()">
    <i class="bi bi-list"></i>
  </div>
  <nav id="navMenu">
    <a href="https://samaxan.ir/bio.html" class="menulink">بیوگرافی</a>
    <a href="https://samaxan.ir/music/player.php" class="menulink">آهنگ‌ها</a>
    <a href="#posts" class="menulink">پست‌ها</a>
    <a href="#footer" class="menulink">پروفایل ها</a>
  </nav>
</header>
<main class="track-page">
  <h1 class="music-header"><?= $title ?></h1>
  <?php if ($coverPath): ?>
    <div class="cover-container" style="text-align:center; margin-bottom: 1.5rem;">
      <img src="<?= htmlspecialchars($coverPath) ?>" alt="کاور <?= htmlspecialchars($title) ?>" style="max-width: 300px; width: 100%; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);" />
    </div>
  <?php endif; ?>
  <div class="player-single">
    <audio controls>
      <source src="<?= $audioPath ?>" type="audio/<?= $ext ?>">
      مرورگر شما پشتیبانی نمی‌کند.
    </audio>
    <a class="btn-download" href="download.php?file=<?= urlencode($filename) ?>&title=<?= urlencode($title) ?>">⬇️ دانلود</a>
  </div>
<?php if (!empty($videoEmbed)): ?>
  <section class="video-box">
    <h2>🎬 موزیک ویدیو: <?= htmlspecialchars($title) ?></h2>
    <div class="video-embed">
      <?= $videoEmbed ?>
    </div>
  </section>
<?php endif; ?>
  <div class="meta-box">
    <span>📁 فرمت: <?= strtoupper($ext) ?></span> | 
    <span>📅 تاریخ انتشار: <?= date("Y/m/d H:i", filemtime($audioPath)) ?></span>
  </div>

  <div class="share-buttons">
    <p>📤 اشتراک‌گذاری:</p>
    <a href="https://t.me/share/url?url=<?= urlencode('https://samaxan.ir/track.php?file=' . $filename) ?>&text=<?= urlencode($title) ?>" target="_blank" style="text-decoration:none;">
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 240 240" style="vertical-align:middle;">
    <circle cx="120" cy="120" r="120" fill="#0088cc"/>
    <path fill="#fff" d="M173.1 80.5L152 172.6c-1.5 6.2-5.6 7.7-11.2 4.8l-31-22.8-14.9 14.4c-1.6 1.6-3 3-6.2 3l2.2-31.5 57.4-51.8c2.5-2.2-.5-3.5-3.8-1.3l-71 44.7-30.6-9.6c-6.6-2.1-6.7-6.6 1.4-9.8l119.5-46.1c5.5-2 10.3 1.3 8.5 9.4z"/>
  </svg>
  <span style="margin-right:8px;">تلگرام</span>
<a href="https://wa.me/?text=<?= urlencode($title . ' - https://samaxan.ir/track.php?file=' . $filename) ?>" target="_blank" style="text-decoration:none;">
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#25D366" viewBox="0 0 24 24" style="vertical-align:middle;">
    <path d="M20.52 3.48A11.9 11.9 0 0 0 12.07.08C6.08.08.98 5.14.98 11.09c0 1.95.52 3.85 1.5 5.52L.08 24l7.62-2.36a11.91 11.91 0 0 0 4.45.85h.02c6 0 11.08-5.06 11.08-11.01 0-2.94-1.16-5.7-3.13-7.65zM12.15 21.8a9.83 9.83 0 0 1-4.24-.99l-.3-.14-4.52 1.4 1.47-4.4-.16-.32a9.9 9.9 0 0 1-1.44-5.15c0-5.39 4.41-9.78 9.83-9.78a9.76 9.76 0 0 1 6.97 2.9 9.7 9.7 0 0 1 2.9 6.93c-.01 5.4-4.42 9.78-9.91 9.78zm5.46-7.35c-.3-.15-1.78-.88-2.06-.98-.28-.1-.49-.15-.7.15s-.8.98-.98 1.18c-.18.2-.36.22-.66.07-.3-.15-1.26-.46-2.4-1.48-.89-.8-1.49-1.79-1.66-2.09-.17-.3-.02-.46.13-.6.13-.13.3-.34.45-.5.15-.17.2-.28.3-.47.1-.18.05-.35-.02-.5-.07-.15-.7-1.69-.96-2.31-.25-.6-.51-.5-.7-.51l-.6-.01c-.2 0-.5.07-.76.35s-1 1-1 2.42 1.02 2.8 1.16 2.99c.15.2 2 3.05 4.88 4.28.68.29 1.2.46 1.6.59.67.21 1.28.18 1.76.11.54-.08 1.78-.73 2.03-1.43.25-.7.25-1.3.18-1.43-.07-.13-.26-.2-.54-.35z"/>
  </svg>
  <span style="margin-right:8px;">واتساپ</span>
</a>
  </div>

  <?php if ($lyrics): ?>
  <section class="lyrics-box">
    <h2>🎤 متن آهنگ:</h2>
    <pre><?= $lyrics ?></pre>
  </section>
  <?php endif; ?>
</main>

<!-- فوتر -->
<footer id="footer" class="footer">
  <div class="column">
    <div class="logo">sama xan</div>
    <p>وب سایت رسمی سما خان</p>
  </div>
  <div class="column nav-links">
    <h4 style="color: #f9c74f">صفحات</h4>
    <a href="https://pedramfaizi.ir/sama/home.htm">تست</a><br />
    <a href="https://pedramfaizi.ir/sama/index.ph">تست</a><br />
    <a href="https://pedramfaizi.ir/sama/posts.ph">تست</a><br />
  </div>
  <div class="column social-icons">
    <h4 style="color: #f9c74f">پروفایل ها</h4>
    <a href="https://www.instagram.com/samaxaaan"><i class="bi bi-instagram"></i></a>
    <a href="https://t.me/samaxanmusic1"><i class="bi bi-telegram"></i></a>
    <a href="https://www.tiktok.com/@samaxaaan"><i class="bi bi-tiktok"></i></a>
    <a href="https://m.youtube.com/@samaxaaan"><i class="bi bi-youtube"></i></a>
  </div>
  <div class="copyright">© 2025 Sama xan</div>
</footer>

</body>
</html>