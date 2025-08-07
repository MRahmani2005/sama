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
<meta charset="UTF-8" />
<title>آپلود و مدیریت آهنگ‌ها</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  /* Reset اولیه برای ثبات در همه مرورگرها */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

/* بدنه صفحه */
body {
  font-family: 'Tahoma', sans-serif;
  background: #f9fafb;
  color: #333;
  direction: rtl;
  padding: 20px 15px;
  min-height: 100vh;
}

/* تیتر صفحه */
h1 {
  text-align: center;
  font-size: 2.2rem;
  margin-bottom: 30px;
  color: #2c3e50;
}

/* فرم آپلود */
form {
  max-width: 700px;
  background: #ffffff;
  margin: 0 auto 40px auto;
  padding: 25px 30px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.07);
}

/* برچسب‌ها */
form label {
  display: block;
  font-weight: 600;
  font-size: 1.1rem;
  color: #34495e;
  margin-bottom: 10px;
  user-select: none;
}

/* ورودی‌ها و textarea */
input[type="text"], textarea, input[type="file"] {
  width: 100%;
  padding: 14px 18px;
  border: 1.8px solid #bdc3c7;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  font-family: 'Tahoma', sans-serif;
  resize: vertical;
  color: #2c3e50;
  margin-bottom: 22px;
}

input[type="text"]:focus, textarea:focus, input[type="file"]:focus {
  border-color: #27ae60;
  box-shadow: 0 0 8px #27ae6050;
  outline: none;
}

/* دکمه */
button {
  background-color: #27ae60;
  color: white;
  border: none;
  padding: 14px 30px;
  font-size: 1.1rem;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease;
  width: 100%;
  user-select: none;
}
button:hover {
  background-color: #219150;
}

/* پیام‌ها */
.message {
  max-width: 700px;
  margin: 15px auto 40px auto;
  font-weight: 700;
  font-size: 1.1rem;
  text-align: center;
  word-break: break-word;
  padding: 15px 20px;
  border-radius: 10px;
}

.message.error {
  background-color: #fcebea;
  color: #e74c3c;
  border: 1.5px solid #e74c3c;
}

.message.success {
  background-color: #e8f6e8;
  color: #27ae60;
  border: 1.5px solid #27ae60;
}

/* جدول */
table {
  width: 100%;
  max-width: 900px;
  margin: 0 auto 50px auto;
  border-collapse: collapse;
  box-shadow: 0 6px 18px rgba(0,0,0,0.1);
  border-radius: 12px;
  overflow: hidden;
  font-size: 0.95rem;
  background-color: white;
}

/* ردیف‌های جدول */
thead tr {
  background-color: #27ae60;
  color: white;
  font-weight: 700;
  font-size: 1rem;
}
thead th, tbody td {
  padding: 14px 12px;
  border-bottom: 1px solid #ddd;
  text-align: center;
  vertical-align: middle;
}

/* ردیف‌های زوج */
tbody tr:nth-child(even) {
  background-color: #f4f9f4;
}

/* ستون متن آهنگ */
td.lyrics {
  max-width: 300px;
  white-space: pre-wrap;
  text-align: right;
  direction: rtl;
  font-family: Tahoma, sans-serif;
  font-size: 0.95rem;
  line-height: 1.4;
  word-break: break-word;
}

/* ستون پلیر */
td.audio-cell {
  width: 220px;
  padding: 8px 12px;
}

/* پلیر */
audio {
  width: 100%;
  max-width: 220px;
  height: 32px;
  outline: none;
  border-radius: 6px;
  background-color: #eee;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
  display: block;
  margin: 0 auto;
}

/* لینک‌های عملیات */
a.button-link {
  display: inline-block;
  background-color: #27ae60;
  color: white;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 600;
  margin: 2px 5px;
  text-decoration: none;
  user-select: none;
  transition: background-color 0.3s ease;
}
a.button-link:hover {
  background-color: #219150;
}
a.button-link.delete {
  background-color: #e74c3c;
}
a.button-link.delete:hover {
  background-color: #c0392b;
}

/* فرم ویرایش */
.edit-form {
  max-width: 700px;
  margin: 30px auto 40px auto;
  background: #fff3cd;
  border: 1.5px solid #ffeeba;
  padding: 25px 30px;
  border-radius: 12px;
  box-sizing: border-box;
}
.edit-form h2 {
  margin-top: 0;
  margin-bottom: 20px;
  font-weight: 700;
  color: #856404;
  font-size: 1.4rem;
}
.edit-form a {
  font-size: 0.9rem;
  color: #555;
  text-decoration: underline;
  margin-right: 15px;
  word-break: break-word;
}

/* ریسپانسیو */

/* موبایل تا 480px */
@media (max-width: 480px) {
  body {
    padding: 15px 10px;
  }
  h1 {
    font-size: 1.6rem;
    margin-bottom: 20px;
  }
  form, .edit-form {
    padding: 20px 20px;
  }
  input[type="text"], textarea, input[type="file"], button, a.button-link {
    font-size: 1rem;
    padding: 12px 15px;
  }
  button {
    width: 100%;
  }
  table {
    font-size: 0.85rem;
    margin-bottom: 30px;
    max-width: 100%;
    overflow-x: auto;
    display: block;
  }
  thead tr {
    display: table-row;
  }
  tbody tr {
    display: table-row;
  }
  td.audio-cell {
    width: 100%;
    padding: 10px 5px;
  }
  td.lyrics {
    max-width: 100%;
    font-size: 0.9rem;
  }
  audio {
    max-width: 100%;
    height: 35px;
  }
  a.button-link {
    font-size: 0.95rem;
    padding: 8px 14px;
  }
  .message {
    font-size: 1rem;
    padding: 12px 15px;
  }
}

/* تبلت تا 768px */
@media (max-width: 768px) {
  form, .edit-form {
    padding: 22px 24px;
  }
  table {
    max-width: 100%;
    font-size: 0.9rem;
  }
  td.audio-cell {
    width: 180px;
  }
  audio {
    max-width: 180px;
    height: 32px;
  }
}

</style>
</head>
<body>

<h1 style="text-align:center;">آپلود و مدیریت آهنگ‌ها</h1>

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
<form method="post" enctype="multipart/form-data">
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
    <label>عنوان آهنگ:</label><br />
    <input type="text" name="title_edit" value="<?= htmlspecialchars($currentTitle) ?>" required /><br />
    <label>متن آهنگ:</label><br />
    <textarea name="lyrics_edit"><?= htmlspecialchars($currentLyrics) ?></textarea><br />
    <label>کاور آهنگ (اختیاری، فقط jpg/png/webp):</label><br />
    <input type="file" name="cover_edit" accept="image/*"><br />
    <button type="submit" name="edit">ثبت تغییرات</button>
    <a href="upload.php" style="margin-left: 10px; color: #555;">انصراف</a>
  </form>
<?php endif; ?>

<!-- جدول لیست آهنگ‌ها -->
<table>
  <thead>
    <tr>
      <th>نام فایل</th>
      <th>عنوان آهنگ</th>
      <th>کاور</th>
      <th>پخش</th>
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
      <td><?= htmlspecialchars($title) ?></td>
      <td>
        <?php if ($coverPath): ?>
          <img src="<?= htmlspecialchars($coverPath) ?>" alt="کاور" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" />
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
      <td class="audio-cell">
        <audio controls>
          <source src="<?= htmlspecialchars($targetDir . $file) ?>" type="audio/<?= htmlspecialchars(pathinfo($file, PATHINFO_EXTENSION)) ?>">
          مرورگر شما از پخش صوت پشتیبانی نمی‌کند.
        </audio>
      </td>
      <td class="lyrics"><?= nl2br(htmlspecialchars($lyrics)) ?></td>
      <td>
        <a href="?edit=<?= urlencode($file) ?>" class="button-link">ویرایش</a>
        <a href="?delete=<?= urlencode($file) ?>" onclick="return confirm('آیا از حذف این آهنگ مطمئن هستید؟');" class="button-link delete">حذف</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>