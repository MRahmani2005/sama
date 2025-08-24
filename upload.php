<?php
// محافظت با رمز ساده (Basic Auth)
$valid_username = 'admin';
$valid_password = '12345';

if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] !== $valid_username || 
    $_SERVER['PHP_AUTH_PW'] !== $valid_password) {
    header('WWW-Authenticate: Basic realm="Private Upload Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo "⛔ دسترسی غیرمجاز!";
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$targetDir = "uploads/";
$lyricsDir = "lyrics/";
$titlesDir = "titles/";
$coverDir = "covers/";

if (!file_exists($coverDir)) mkdir($coverDir, 0777, true);
if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
if (!file_exists($lyricsDir)) mkdir($lyricsDir, 0777, true);
if (!file_exists($titlesDir)) mkdir($titlesDir, 0777, true);

function sanitizeFileName($string) {
    $string = preg_replace('/[^A-Za-z0-9\-آ-یء ]/', '', $string);
    $string = preg_replace('/\s+/', '_', $string);
    return trim($string, '_');
}

// حذف فایل‌ها
if (isset($_GET['delete'])) {
    $file = basename($_GET['delete']);
    $baseName = pathinfo($file, PATHINFO_FILENAME);

    $musicFile = $targetDir . $file;
    $lyricsFile = $lyricsDir . $baseName . ".txt";
    $titleFile = $titlesDir . $baseName . ".txt";

    // حذف کاورهای احتمالی با پسوندهای مجاز
    $allowedCoverExts = ['jpg', 'jpeg', 'png', 'webp'];
    foreach ($allowedCoverExts as $ext) {
        $coverFile = $coverDir . $baseName . "." . $ext;
        if (file_exists($coverFile)) unlink($coverFile);
    }

    if (file_exists($musicFile)) unlink($musicFile);
    if (file_exists($lyricsFile)) unlink($lyricsFile);
    if (file_exists($titleFile)) unlink($titleFile);

    header("Location: upload.php?deleted=1");
    exit;
}

// ویرایش عنوان و متن و آپلود کاور
if (isset($_POST['edit']) && isset($_POST['file'])) {
    $file = basename($_POST['file']);
    $baseName = pathinfo($file, PATHINFO_FILENAME);

    $newTitle = trim($_POST['title_edit']);
    $newLyrics = trim($_POST['lyrics_edit']);

    if ($newTitle === '') {
        $error = "عنوان نمی‌تواند خالی باشد.";
    } else {
        file_put_contents($titlesDir . $baseName . ".txt", $newTitle);
        file_put_contents($lyricsDir . $baseName . ".txt", $newLyrics);

        // آپلود کاور جدید در ویرایش
        if (isset($_FILES['cover_edit']) && $_FILES['cover_edit']['error'] === UPLOAD_ERR_OK) {
            $coverExt = pathinfo($_FILES['cover_edit']['name'], PATHINFO_EXTENSION);
            $allowedCoverExts = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array(strtolower($coverExt), $allowedCoverExts)) {
                $coverName = $baseName . "." . $coverExt;
                $coverPath = $coverDir . $coverName;

                // حذف کاورهای قبلی با پسوندهای متفاوت
                foreach ($allowedCoverExts as $ext) {
                    $oldCover = $coverDir . $baseName . "." . $ext;
                    if (file_exists($oldCover) && $oldCover !== $coverPath) {
                        unlink($oldCover);
                    }
                }

                move_uploaded_file($_FILES['cover_edit']['tmp_name'], $coverPath);
            }
        }

        header("Location: upload.php?edited=1");
        exit;
    }
}

// آپلود فایل جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit'])) {
    $titleRaw = isset($_POST['title']) ? trim($_POST['title']) : '';

    if (empty($titleRaw)) {
        $error = "عنوان آهنگ نمی‌تواند خالی باشد.";
    }

    $time = date("Ymd_His");
    $originalName = basename($_FILES["music"]["name"]);
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    $newName = "music_" . $time . "." . $ext;
    $targetFile = $targetDir . $newName;

    $allowed = ['mp3', 'wav', 'ogg', 'm4a', 'flac'];

    if (!isset($error)) {
        if (!in_array(strtolower($ext), $allowed)) {
            $error = "فرمت فایل مجاز نیست.";
        } elseif (!move_uploaded_file($_FILES["music"]["tmp_name"], $targetFile)) {
            $error = "خطا در آپلود فایل.";
        } else {
            // ذخیره کاور اگر فرستاده شده باشد
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
                $coverExt = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                $allowedCoverExts = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array(strtolower($coverExt), $allowedCoverExts)) {
                    $coverName = pathinfo($newName, PATHINFO_FILENAME) . "." . $coverExt;
                    move_uploaded_file($_FILES['cover']['tmp_name'], $coverDir . $coverName);
                }
            }

            $lyricsText = isset($_POST['lyrics']) ? trim($_POST['lyrics']) : '';
            file_put_contents($lyricsDir . pathinfo($newName, PATHINFO_FILENAME) . ".txt", $lyricsText);
            file_put_contents($titlesDir . pathinfo($newName, PATHINFO_FILENAME) . ".txt", $titleRaw);

            header("Location: upload.php?success=1");
            exit();
        }
    }
}

$files = array_diff(scandir($targetDir), ['.', '..']);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<link rel="stylesheet" href="style.css?v=20250835">
<meta charset="UTF-8" />
<title>آپلود و مدیریت آهنگ‌ها</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />

</head>
<body>

<h1 style="text-align:center;" class="upload-h1">آپلود و مدیریت آهنگ‌ها</h1>

<?php if (!empty($error)) : ?>
  <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php elseif (isset($_GET['success'])): ?>
  <div class="message success">✅ آپلود با موفقیت انجام شد.</div>
<?php elseif (isset($_GET['deleted'])): ?>
  <div class="message success">✅ آهنگ حذف شد.</div>
<?php elseif (isset($_GET['edited'])): ?>
  <div class="message success">✅ ویرایش با موفقیت انجام شد.</div>
<?php endif; ?>

<!-- فرم آپلود جدید -->
<form method="post" enctype="multipart/form-data" class="upload-form">
  <label>عنوان آهنگ (الزامی):</label><br />
  <input type="text" name="title" placeholder="عنوان آهنگ را وارد کنید" required /><br />

  <label>فایل آهنگ (فرمت‌های مجاز: mp3, wav, ogg, m4a, flac):</label><br />
  <input type="file" name="music" accept="audio/*" required /><br />
  
  <label>کاور آهنگ (اختیاری، فقط jpg/png/webp):</label><br />
  <input type="file" name="cover" accept="image/*"><br />

  <label>متن آهنگ (اختیاری):</label><br />
  <textarea name="lyrics" placeholder="متن آهنگ را اینجا بنویسید..."></textarea><br />

  <button type="submit">آپلود</button>
</form>

<?php
if (isset($_GET['edit'])):
    $fileToEdit = basename($_GET['edit']);
    $baseName = pathinfo($fileToEdit, PATHINFO_FILENAME);

    $titleFile = $titlesDir . $baseName . ".txt";
    $lyricsFile = $lyricsDir . $baseName . ".txt";

    $currentTitle = file_exists($titleFile) ? file_get_contents($titleFile) : "";
    $currentLyrics = file_exists($lyricsFile) ? file_get_contents($lyricsFile) : "";
?>
  <form method="post" enctype="multipart/form-data" class="edit-form">
    <h2>ویرایش آهنگ: <?= htmlspecialchars($fileToEdit) ?></h2>
    <input type="hidden" name="file" value="<?= htmlspecialchars($fileToEdit) ?>" />
    <div class="form-song-name">
      <label>عنوان آهنگ:</label><br />
      <input type="text" name="title_edit" value="<?= htmlspecialchars($currentTitle) ?>" required /><br />
    </div>
    <label>متن آهنگ:</label><br />
    <textarea name="lyrics_edit"><?= htmlspecialchars($currentLyrics) ?></textarea><br />
    <label>کاور آهنگ (اختیاری، فقط jpg/png/webp):</label><br />
    <input class="input-upload" type="file" name="cover_edit" accept="image/*"><br />
    <button class="btn-submit" type="submit" name="edit">ثبت تغییرات</button>
    <a href="upload.php" style="margin-left: 10px; color: #555;" class="button-form">انصراف</a>
  </form>
<?php endif; ?>

<!-- جدول لیست آهنگ‌ها -->
<table>
  <thead>
    <tr>
      <th>نام فایل</th>
      <th>کاور</th>
      <th>متن آهنگ</th>
      <th>عملیات</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($files as $file):
      $baseName = pathinfo($file, PATHINFO_FILENAME);
      $titleFile = $titlesDir . $baseName .$titleFile = $titlesDir . $baseName . ".txt";
      $lyricsFile = $lyricsDir . $baseName . ".txt";

      $title = file_exists($titleFile) ? file_get_contents($titleFile) : '-';
      $lyrics = file_exists($lyricsFile) ? file_get_contents($lyricsFile) : '-';

      // پیدا کردن کاور
      $coverPath = "";
      $allowedCoverExts = ['jpg', 'jpeg', 'png', 'webp'];
      foreach ($allowedCoverExts as $ext) {
          if (file_exists($coverDir . $baseName . "." . $ext)) {
              $coverPath = $coverDir . $baseName . "." . $ext;
              break;
          }
      }
    ?>
    <tr>
      <td><?= htmlspecialchars($file) ?></td>
      <td>
        <?php if ($coverPath): ?>
          <img src="<?= htmlspecialchars($coverPath) ?>" alt="کاور" style="width: 200px; height: 200px; object-fit: cover; border-radius: 8px;" />
        <?php endif; ?>
      </td>

      <td class="upload-lyrics"><?= nl2br(htmlspecialchars($lyrics)) ?></td>
      <td>
        <a href="?edit=<?= urlencode($file) ?>" class="button-link">ویرایش</a>
        <a href="?delete=<?= urlencode($file) ?>" onclick="return confirm('آیا از حذف این آهنگ مطمئن هستید؟');" class="button-link delete">حذف</a>
      </td>
            <td class="audio-cell">
        <audio controls>
          <source src="<?= htmlspecialchars($targetDir . $file) ?>" type="audio/<?= htmlspecialchars(pathinfo($file, PATHINFO_EXTENSION)) ?>">
          مرورگر شما از پخش صوت پشتیبانی نمی‌کند.
        </audio>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>