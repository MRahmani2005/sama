<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8" />
  <title>دانلود تمام آهنگ های سما خان</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- فونت و آیکون -->
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- فایل‌های css و js با جلوگیری از کش -->
  <link rel="stylesheet" href="style.css?v=20250823">
</head>

<body>
<header class="header music-header unique">
  <div class="menu-icon" onclick="toggleMenu()">
    <i class="bi bi-list"></i>
  </div>
  <a href="https://samaxan.ir" class="logo">🎵 Sama Xan</a>

  <nav id="navMenu">
    <a href="https://samaxan.ir/bio.html" class="menulink">بیوگرافی</a>
    <a href="https://samaxan.ir/music/player.php" class="menulink">آهنگ‌ها</a>
    <a href="#posts" class="menulink">پست‌ها</a>
    <a href="#footer" class="menulink">پروفایل ها</a>
  </nav>
  <div class="contactme">
    <a href="https://www.instagram.com/samaxaaan" class="instagram-box"><i class="bi bi-instagram"></i>اینستاگرام</a>
  </div>
</header>
<!-- پلیر بالا -->
<div class="player">
  <div class="title" id="track-title">در حال پخش: </div>
  <audio id="audio" controls></audio>
  <div class="controls">
    <button onclick="prevTrack()">قبلی</button>
    <button onclick="nextTrack()">بعدی</button>
  </div>
</div>


<div class="music-search-container">
  <h1 class="music-heading">دانلود تمام آهنگ های سما خان</h1>
  <div class="search-box">
      <input type="text" id="searchInput" placeholder="جستجو بر اساس عنوان...">
  </div>
</div>

<script>
  const audio = document.getElementById("audio");
  const title = document.getElementById("track-title");
  let tracks = [];
  let current = 0;

  window.onload = () => {
    document.querySelectorAll('a.track').forEach(link => {
      tracks.push({
        title: link.dataset.title || link.innerText,
        file: link.href
      });
    });
    if (tracks.length > 0) loadTrack(current);
  };

  function loadTrack(index) {
    audio.src = tracks[index].file;
    title.innerText = "در حال پخش: " + tracks[index].title;
    audio.play().catch(e => console.error("خطا در پخش:", e));
  }

  function nextTrack() {
    current = (current + 1) % tracks.length;
    loadTrack(current);
  }

  function prevTrack() {
    current = (current - 1 + tracks.length) % tracks.length;
    loadTrack(current);
  }

  audio.addEventListener("ended", nextTrack);
</script>

<!-- لیست آهنگ‌ها -->
<div class="music-list" id="musicList">
<?php
$folder = "uploads/";
$lyricsFolder = "lyrics/";
$titlesFolder = "titles/";

if (is_dir($folder)) {
    $files = array_filter(scandir($folder), function($file) {
        return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['mp3', 'wav', 'ogg', 'm4a', 'flac']);
    });

    usort($files, function($a, $b) use ($folder) {
        return filemtime($folder . $b) - filemtime($folder . $a);
    });

    foreach ($files as $file) {
        $baseName = pathinfo($file, PATHINFO_FILENAME);
        $audioPath = $folder . $file;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        // عنوان آهنگ
        $title = "بدون عنوان";
        $titlePath = $titlesFolder . $baseName . ".txt";
        if (file_exists($titlePath)) {
            $raw = file_get_contents($titlePath);
            if (trim($raw) !== '') $title = htmlspecialchars($raw);
        }

        // متن آهنگ
        $lyrics = "";
        $lyricsPath = $lyricsFolder . $baseName . ".txt";
        if (file_exists($lyricsPath)) {
            $lyrics = htmlspecialchars(file_get_contents($lyricsPath));
        }

        // زمان بارگذاری
        $time = date("Y/m/d H:i", filemtime($audioPath));

        echo "<div class='music-item' data-title='$title'>";
        echo "<div class='music-title'>🎵 $title</div>";
        echo "<div class='music-meta'>🗓️ $time | 📁 $ext</div>";
        echo "<audio controls preload='none'><source src='$audioPath' type='audio/$ext'>مرورگر شما پشتیبانی نمی‌کند.</audio>";
        echo "<a class='track' data-title='$title' href='$audioPath' style='display:none'></a>"; 
        if ($lyrics) echo "<div class='lyrics'>$lyrics</div>"; echo "<a href='track.php?file=" . urlencode($file) . "' class='btn-more'>صفحه اختصاصی</a>";
        echo "<a class='lyrics-btn-download' href='download.php?file=" . urlencode($file) . "&title=" . urlencode($title) . "'>دانلود</a>";
            echo "</div>"; // پایان music-item 
        }
        } else {
            echo "<p>پوشه آهنگ‌ها پیدا نشد.</p>";
        }
?>
</div>

<!-- جستجوی سریع -->
<script>
document.getElementById("searchInput").addEventListener("input", function() {
  const val = this.value.trim().toLowerCase();
  document.querySelectorAll('.music-item').forEach(item => {
    const title = item.dataset.title.toLowerCase();
    item.style.display = title.includes(val) ? 'block' : 'none';
  });
});
</script>

<!-- فوتر -->
<footer id="footer" class="footer player-footer">
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
    <script src="script.js?v=20250821"></script>
</body>
</html>