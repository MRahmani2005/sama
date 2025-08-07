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
        <title>ورود به مدیریت</title>
        <style>
            body { font-family: 'Vazirmatn', sans-serif; background: #f5f5f5; padding: 3rem; text-align: center; direction: rtl; }
            form { background: #fff; padding: 2rem; display: inline-block; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            input { padding: 0.5rem; margin: 0.5rem 0; width: 100%; border-radius: 5px; border: 1px solid #ccc; }
            button { padding: 0.6rem 1.5rem; background: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
            .error { color: red; margin-bottom: 1rem; }
        </style>
    </head>
    <body>
        <form method="post">
            <h2>🔐 ورود به مدیریت</h2>
            <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
            <input type="text" name="username" placeholder="نام کاربری" required>
            <input type="password" name="password" placeholder="رمز عبور" required>
            <button type="submit">ورود</button>
        </form>
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
$audioFiles = array_filter(scandir($audioDir), function($file) {
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
    body { font-family: 'Vazirmatn', sans-serif; background: #f7f7f7; padding: 2rem; direction: rtl; }
    h1 { color: #333; }
    .msg { padding: 1rem; background: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 8px; margin-bottom: 1rem; }
    form { background: #fff; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; }
    label { display: block; margin-top: 1rem; }
    input, select, textarea { width: 100%; padding: 0.5rem; margin-top: 0.5rem; border-radius: 5px; border: 1px solid #ccc; }
    button { margin-top: 1rem; padding: 0.7rem 1.5rem; border: none; background: #28a745; color: #fff; border-radius: 5px; cursor: pointer; }
    .btn-del { background: #dc3545; margin-right: 1rem; }
    table { width: 100%; border-collapse: collapse; margin-top: 2rem; background: #fff; border-radius: 10px; overflow: hidden; }
    th, td { padding: 1rem; border-bottom: 1px solid #eee; text-align: right; }
    th { background: #f0f0f0; }
    .has-link { color: green; font-weight: bold; }
    .no-link { color: gray; }
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
    <textarea name="video" id="video" rows="5" placeholder="https://cdn.example.com/video.mp4 یا کد IFRAME" required></textarea>

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
              <button class="btn-del" type="submit" onclick="return confirm('آیا مطمئنی که می‌خواهی حذف شود؟')">🗑️ حذف</button>
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