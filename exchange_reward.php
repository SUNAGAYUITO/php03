<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'ログインが必要です']);
  exit();
}

$user_id = $_SESSION['user_id'];
$reward_id = $_POST['reward_id'] ?? 0;


try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  // ① リワード情報取得
  $stmt = $pdo->prepare("SELECT * FROM rewards WHERE id = :reward_id");
  $stmt->bindValue(':reward_id', $reward_id, PDO::PARAM_INT);
  $stmt->execute();
  $reward = $stmt->fetch();

  if (!$reward) {
    echo json_encode(['status' => 'error', 'message' => '特典が見つかりません']);
    exit();
  }

  // ② ユーザーの現在ポイント取得
  $stmt = $pdo->prepare("SELECT point FROM users WHERE id = :user_id");
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->execute();
  $user = $stmt->fetch();

  if ($user['point'] < $reward['point_cost']) {
    echo json_encode(['status' => 'error', 'message' => 'ポイントが足りません']);
    exit();
  }

  // ③ ポイント減算・交換履歴登録をトランザクションで
  $pdo->beginTransaction();

  // ポイントを減らす
  $update = $pdo->prepare("UPDATE users SET point = point - :cost WHERE id = :user_id");
  $update->bindValue(':cost', $reward['point_cost'], PDO::PARAM_INT);
  $update->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $update->execute();

  // 交換履歴に記録
  $insert = $pdo->prepare("INSERT INTO user_rewards (user_id, reward_id) VALUES (:user_id, :reward_id)");
  $insert->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $insert->bindValue(':reward_id', $reward_id, PDO::PARAM_INT);
  $insert->execute();

  $pdo->commit();

  echo json_encode(['status' => 'success', 'message' => '交換完了しました！']);
} catch (PDOException $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['status' => 'error', 'message' => '交換処理中エラー: ' . $e->getMessage()]);
}
