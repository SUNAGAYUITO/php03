<?php
// エラー表示設定
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB接続情報


// DB接続
try {
  $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
  exit('DB_CONNECT_ERROR: ' . $e->getMessage());
}

// データ取得
$stmt = $pdo->prepare("SELECT * FROM gs_bm_table ORDER BY indate DESC");
$status = $stmt->execute();

if ($status === false) {
  $error = $stmt->errorInfo();
  exit("SQL_ERROR: " . $error[2]);
}

$values = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ブックマーク一覧</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { padding: 20px; }
    td, th { vertical-align: middle !important; }
    .table a { word-break: break-word; }
  </style>
</head>
<body>

<div class="container">
  <a href="index.php" class="btn btn-info" style="margin-bottom: 20px;">ブックマーク登録に戻る</a>
  <h2>登録されたブックマーク一覧</h2>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th><th>書籍名</th><th>URL</th><th>コメント</th><th>登録日時</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($values) === 0): ?>
        <tr><td colspan="5">📭 データが登録されていません</td></tr>
      <?php else: ?>
        <?php foreach($values as $value): ?>
          <tr>
            <td><?= htmlspecialchars($value["id"], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($value["name"], ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <?php if (!empty($value["url"])): ?>
                <a href="<?= htmlspecialchars($value["url"], ENT_QUOTES, 'UTF-8') ?>" target="_blank">
                  <?= htmlspecialchars($value["url"], ENT_QUOTES, 'UTF-8') ?>
                </a>
              <?php else: ?>
                （URLなし）
              <?php endif; ?>
            </td>
            <td><?= nl2br(htmlspecialchars($value["comment"], ENT_QUOTES, 'UTF-8')) ?></td>
            <td><?= htmlspecialchars($value["indate"], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
