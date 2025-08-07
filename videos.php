<?php
$audioDir = "uploads/";
$titleDir = "titles/";
$videoDir = "video-links/";

$audioFiles = array_filter(scandir($audioDir), function($file) {
    return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['mp3', 'wav', 'ogg', 'm4a', 'flac']);
});
sort($audioFiles);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>گالری موزیک ویدیوها | Sama Xan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="گالری موزیک ویدیوهای رسمی سما خان. مشاهده، دانلود و پخش آنلاین موزیک ویدیوها از خواننده محبوب کردی، سما خان." />
  <meta name="keywords" content="موزیک ویدیو سما خان, Sama Xan, موسیقی کردی, دانلود موزیک ویدیو, Sama Xan videos" />
  <meta name="author" content="Sama Xan" />

  <!-- Open Graph -->
  <meta property="og:title" content="گالری موزیک ویدیوها | Sama Xan" />
  <meta property="og:description" content="گالری موزیک ویدیوهای رسمی سما خان. مشاهده، دانلود و پخش آنلاین موزیک ویدیوها از خواننده محبوب کردی، سما خان." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://samaxan.ir/gallery-video.php" />
  <meta property="og:image" content="https://samaxan.ir/img/samaxan-profile.jpg" />
  <meta property="og:locale" content="fa_IR" />
  <meta property="og:site_name" content="Sama Xan Official" />

  <!-- Schema.org -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "گالری موزیک ویدیوها | Sama Xan",
    "url": "https://samaxan.ir/gallery-video.php",
    "description": "گالری موزیک ویدیوهای رسمی سما خان. مشاهده، دانلود و پخش آنلاین موزیک ویدیوها از خواننده محبوب کردی، سما خان.",
    "inLanguage": "fa"
  }
  </script>

  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="style.css?v=<?= filemtime('style.css') ?>" />
  <style>
    body {
      font-family: 'Vazirmatn', sans-serif;
      background: #f9f9f9;
      padding: 20px;
      direction: rtl;
      color: #333;
      margin: 0;
    }
    h1 {
      text-align: center;
      color: #f9c74f;
      margin-bottom: 30px;
      font-weight: 800;
      font-size: 2.4rem;
      text-shadow: 0 0 3px #b8921a;
    }
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 25px;
      max-width: 1100px;
      margin: 0 auto 40px auto;
    }
    .video-item {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 15px 20px 25px 20px;
      text-align: center;
      transition: box-shadow 0.3s ease;
    }
    .video-item:hover {
      box-shadow: 0 6px 25px rgba(0,0,0,0.2);
    }
    .video-title {
      font-weight: 700;
      margin-bottom: 15px;
      color: #121212;
      font-size: 1.25rem;
      min-height: 52px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 5px;
      line-height: 1.2;
    }
    .video-embed iframe,
    .video-embed video {
      width: 100%;
      max-height: 230px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    /* Header */
    .header {
      background: #121212;
      color: #f9c74f;
      padding: 15px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 6px rgba(0,0,0,0.5);
      margin-bottom: 25px;
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
      user-select: none;
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
    /* Footer */
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
      font-size: 1.2rem;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    .footer .nav-links a,
    .footer .social-icons a {
      color: #f9c74f;
      margin: 0 8px;
      text-decoration: none;
      font-size: 1.3rem;
      transition: color 0.3s ease;
    }
    .footer .nav-links a:hover,
    .footer .social-icons a:hover {
      color: #fff;
    }
    .footer .social-icons a {
      margin-left: 12px;
    }
    /* Responsive */
    @media (max-width: 768px) {
      .gallery {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      }
      .video-embed iframe,
      .video-embed video {
        max-height: 180px;
      }
      .header {
        padding: 12px 15px;
      }
      .header .logo {
        font-size: 1.5rem;
      }
      #navMenu a {
        margin-left: 12px;
        font-size: 0.95rem;
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
    <a href="https://samaxan.ir/player.php" class="menulink">آهنگ‌ها</a>
    <a href="#gallery" class="menulink">گالری ویدیو</a>
    <a href="#footer" class="menulink">پروفایل ها</a>
  </nav>
</header>

<main id="gallery" class="gallery">
  <h1>گالری موزیک ویدیوها</h1>

  <?php foreach ($audioFiles as $file):
    $baseName = pathinfo($file, PATHINFO_FILENAME);

    // خواندن عنوان
    $titlePath = $titleDir . $baseName . ".txt";
    $title = file_exists($titlePath) ? trim(file_get_contents($titlePath)) : $baseName;
    if ($title === '') $title = $baseName;

    // خواندن لینک ویدیو (iframe یا url)
    $videoPath = $videoDir . $baseName . ".txt";
    if (!file_exists($videoPath)) continue; // اگر لینک ویدیو نیست، رد کن

    $videoEmbed = trim(file_get_contents($videoPath));
  ?>
    <article class="video-item">
      <div class="video-title"><?= htmlspecialchars($title) ?></div>
      <div class="video-embed">
        <?= $videoEmbed ?>
      </div>
    </article>
  <?php endforeach; ?>

</main>

<footer id="footer" class="footer">
  <div class="logo">sama xan</div>
  <p>© 2025 Sama xan | وب سایت رسمی سما خان</p>
  <div class="nav-links">
    <a href="https://samaxan.ir/bio.html">بیوگرافی</a>
    <a href="https://samaxan.ir/player.php">آهنگ‌ها</a>
    <a href="#gallery">گالری ویدیو</a>
  </div>
  <div class="social-icons">
    <a href="https://www.instagram.com/samaxaaan"><i class="bi bi-instagram"></i></a>
    <a href="https://t.me/samaxanmusic1"><i class="bi bi-telegram"></i></a>
    <a href="https://www.tiktok.com/@samaxaaan"><i class="bi bi-tiktok"></i></a>
    <a href="https://m.youtube.com/@samaxaaan"><i class="bi bi-youtube"></i></a>
  </div>
</footer>

<script>
function toggleMenu() {
  const navMenu = document.getElementById('navMenu');
  if (navMenu.style.display === 'flex') {
    navMenu.style.display = 'none';
  } else {
    navMenu.style.display = 'flex';
  }
}
</script>

</body>
</html>
