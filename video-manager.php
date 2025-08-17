<?php
session_start();

// رمز ورود دلخواه
$username = 'admin';
$password = '12345';

if (!isset($_SESSION['logged_in'])) {
  if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] === $username && $_POST['password'] === $password) {
      $_SESSION['logged_in'] = true;
      header("Location: " . $_SERVER['PHP_SELF']);
      exit;
    } else {
      $error = "❌ نام کاربری یا رمز اشتباه است.";
    }
  }

  // فرم ورود را نمایش بده و بقیه کدها را متوقف کن
  ?>
  <!DOCTYPE html>
  <html lang="fa">

  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css?v=20250825">
    <title>ورود به مدیریت</title>
  </head>

  <body class="video-manage-page" dir="rtl">
    <div class="form-container">
      <form method="post">
        <h2>🔐 ورود به مدیریت</h2>
        <?php if (isset($error))
          echo "<div class='error'>$error</div>"; ?>
        <input type="text" name="username" placeholder="نام کاربری" required>
        <input type="password" name="password" placeholder="رمز عبور" required>
        <button type="submit">ورود</button>
      </form>
    </div>
    <div id="particles-js"></div>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
      particlesJS("particles-js", { "particles": { "number": { "value": 10, "density": { "enable": true, "value_area": 2288.528160733591 } }, "color": { "value": "#ffffff" }, "shape": { "type": "circle", "stroke": { "width": 0, "color": "#000" }, "polygon": { "nb_sides": 12 }, "image": { "src": "img/github.svg", "width": 80, "height": 80 } }, "opacity": { "value": 0.9379960556063308, "random": true, "anim": { "enable": false, "speed": 1, "opacity_min": 0.1, "sync": false } }, "size": { "value": 212.45209806468176, "random": true, "anim": { "enable": true, "speed": 38.97970619046457, "size_min": 40, "sync": false } }, "line_linked": { "enable": false, "distance": 200, "color": "#ffffff", "opacity": 1, "width": 2 }, "move": { "enable": true, "speed": 8, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false, "attract": { "enable": false, "rotateX": 600, "rotateY": 1200 } } }, "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": false, "mode": "grab" }, "onclick": { "enable": false, "mode": "remove" }, "resize": true }, "modes": { "grab": { "distance": 400, "line_linked": { "opacity": 1 } }, "bubble": { "distance": 400, "size": 40, "duration": 2, "opacity": 8, "speed": 3 }, "repulse": { "distance": 200, "duration": 0.4 }, "push": { "particles_nb": 4 }, "remove": { "particles_nb": 2 } } }, "retina_detect": true }); var count_particles, stats, update; stats = new Stats; stats.setMode(0); stats.domElement.style.position = 'absolute'; stats.domElement.style.left = '0px'; stats.domElement.style.top = '0px'; document.body.appendChild(stats.domElement); count_particles = document.querySelector('.js-count-particles'); update = function () { stats.begin(); stats.end(); if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) { count_particles.innerText = window.pJSDom[0].pJS.particles.array.length; } requestAnimationFrame(update); }; requestAnimationFrame(update);; 
    </script>

  </body>

  </html>
  <?php
  exit;

}
$videoDir = "video-links/";
$audioDir = "uploads/";
$msg = "";

// اضافه یا ویرایش لینک
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
  $filename = isset($_POST['file']) ? $_POST['file'] : '';
  $videoLink = isset($_POST['video']) ? trim($_POST['video']) : '';
  if ($filename && $videoLink) {
    $targetFile = $videoDir . pathinfo($filename, PATHINFO_FILENAME) . ".txt";
    file_put_contents($targetFile, $videoLink);
    $msg = "✅ لینک ویدیو برای '$filename' ذخیره شد.";
  }
}

// حذف لینک ویدیو
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
  $filename = isset($_POST['file']) ? $_POST['file'] : '';
  $targetFile = $videoDir . pathinfo($filename, PATHINFO_FILENAME) . ".txt";
  if (file_exists($targetFile)) {
    unlink($targetFile);
    $msg = "🗑️ لینک ویدیو برای '$filename' حذف شد.";
  }
}

// گرفتن لیست فایل‌های صوتی
$audioFiles = array_filter(scandir($audioDir), function ($file) {
  return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['mp3', 'wav', 'ogg', 'm4a', 'flac']);
});
sort($audioFiles);
?>

<!DOCTYPE html>
<html lang="fa">

<head>
  <meta charset="UTF-8">
  <title>مدیریت ویدیو ترک‌ها</title>
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Vazirmatn', sans-serif;
      background: #f7f7f7;
      padding: 2rem;
      direction: rtl;
    }

    h1 {
      color: #333;
    }

    .msg {
      padding: 1rem;
      background: #d4edda;
      border: 1px solid #c3e6cb;
      color: #155724;
      border-radius: 8px;
      margin-bottom: 1rem;
    }

    form {
      background: #fff;
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 2rem;
    }

    label {
      display: block;
      margin-top: 1rem;
    }

    input,
    select,
    textarea {
      width: 100%;
      padding: 0.5rem;
      margin-top: 0.5rem;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      margin-top: 1rem;
      padding: 0.7rem 1.5rem;
      border: none;
      background: #28a745;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn-del {
      background: #dc3545;
      margin-right: 1rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 2rem;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
    }

    th,
    td {
      padding: 1rem;
      border-bottom: 1px solid #eee;
      text-align: right;
    }

    th {
      background: #f0f0f0;
    }

    .has-link {
      color: green;
      font-weight: bold;
    }

    .no-link {
      color: gray;
    }
  </style>
</head>

<body>

  <h1>🎬 مدیریت لینک ویدیو آهنگ‌ها</h1>

  <?php if (!empty($msg)): ?>
    <div class="msg"><?= $msg ?></div>
  <?php endif; ?>

  <!-- فرم افزودن / ویرایش -->
  <form method="post">
    <input type="hidden" name="action" value="save">
    <label for="file">🎵 انتخاب فایل آهنگ:</label>
    <select name="file" id="file" required>
      <option value="">-- انتخاب کنید --</option>
      <?php foreach ($audioFiles as $file): ?>
        <option value="<?= htmlspecialchars($file) ?>"><?= htmlspecialchars($file) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="video">🎥 لینک ویدیو یا iframe:</label>
    <textarea name="video" id="video" rows="5" placeholder="https://cdn.example.com/video.mp4 یا کد IFRAME"
      required></textarea>

    <button type="submit">💾 ذخیره یا ویرایش</button>
  </form>

  <!-- جدول لیست فایل‌ها -->
  <h2>📁 لیست فایل‌های آهنگ + وضعیت ویدیو</h2>
  <table>
    <tr>
      <th>نام فایل</th>
      <th>وضعیت ویدیو</th>
      <th>اقدامات</th>
    </tr>
    <?php foreach ($audioFiles as $file):
      $key = pathinfo($file, PATHINFO_FILENAME);
      $videoFile = $videoDir . $key . ".txt";
      $hasLink = file_exists($videoFile);
      ?>
      <tr>
        <td><?= htmlspecialchars($file) ?></td>
        <td class="<?= $hasLink ? 'has-link' : 'no-link' ?>">
          <?= $hasLink ? '✅ دارد' : '— ندارد' ?>
        </td>
        <td>
          <?php if ($hasLink): ?>
            <form method="post" style="display:inline;">
              <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>">
              <input type="hidden" name="action" value="delete">
              <button class="btn-del" type="submit" onclick="return confirm('آیا مطمئنی که می‌خواهی حذف شود؟')">🗑️
                حذف</button>
            </form>
          <?php else: ?>
            <em>—</em>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

</body>

</html>