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
  <link rel="shortcut icon"  href="https://samaxan.ir/img/samaxan-gallery38.jpg"/>
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="style.css?v=20250824" />

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
        <a href="https://samaxan.ir/image-gallery.html">گالری تصاویر</a>
        <a href="#footer" class="menulink">پروفایل ها</a>
      </nav>
    </header>

<h1 class="video-page-h1">گالری موزیک ویدیوها</h1>

<main id="video-gallery" class="video-gallery">

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
      <div class="column">
        <div class="logo">sama xan</div>
        <p>وب سایت رسمی سما خان</p>
      </div>
      <div class="column nav-links">
        <h4 style="color: #f9c74f">صفحات</h4>
        <a href="https://samaxan.ir/bio.html">بیوگرافی</a><br />
        <a href="">تست</a><br />
        <a href="">تست</a><br />
      </div>
      <div class="column social-icons">
        <h4 style="color: #f9c74f">پروفایل ها</h4>
        <a href="https://www.instagram.com/samaxaaan"
          ><i class="bi bi-instagram"></i
        ></a>
        <a href="https://t.me/samaxanmusic1"><i class="bi bi-telegram"></i></a>
        <a href="https://www.tiktok.com/@samaxaaan"
          ><i class="bi bi-tiktok"></i
        ></a>
        <a href="https://m.youtube.com/@samaxaaan"
          ><i class="bi bi-youtube"></i
        ></a>
      </div>
      <div class="copyright">© 2025 Sama xan</div>
    </footer>
    <script src="script.js?v=20250806"></script>

</body>
</html>
