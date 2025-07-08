<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// ログインしていなければリダイレクト
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// ここに

try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
  exit();
}

// 入力データ
$name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];
$comment = $_POST['comment'] ?? '';
$menu_name = $_POST['menu_name'] ?? '';
$rating = $_POST['rating'] ?? '';
$image = $_FILES['image'] ?? null;

if (!$comment || !$menu_name || !$rating) {
  echo json_encode(['status' => 'error', 'message' => '全ての項目を入力してください']);
  exit();
}

// 画像処理
$image_path = '';
if ($image && $image['error'] === UPLOAD_ERR_OK) {
  $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
  $unique_name = uniqid('img_', true) . '.' . $ext;
  $upload_dir = __DIR__ . '/upload/';
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
  }
  $image_path = 'upload/' . $unique_name;
  move_uploaded_file($image['tmp_name'], $upload_dir . $unique_name);
}

// レビュー登録
$sql = "INSERT INTO gs_gakusyoku_table (user_id, name, comment, menu_name, rating, image_path, indate)
        VALUES (:user_id, :name, :comment, :menu_name, :rating, :image_path, NOW())";
$stmt = $pdo->prepare($sql);

try {
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->bindValue(':name', $name, PDO::PARAM_STR);
  $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
  $stmt->bindValue(':menu_name', $menu_name, PDO::PARAM_STR);
  $stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
  $stmt->bindValue(':image_path', $image_path, PDO::PARAM_STR);
  $stmt->execute();

  // ポイント加算
  $update = $pdo->prepare("UPDATE users SET point = point + 5 WHERE id = :id");
  $update->bindValue(':id', $user_id, PDO::PARAM_INT);
  $update->execute();

  echo json_encode(['status' => 'success', 'message' => 'レビューを投稿し、5ポイントを付与しました。']);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => '登録エラー: ' . $e->getMessage()]);
}
