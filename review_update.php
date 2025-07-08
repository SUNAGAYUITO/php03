<?php
header("Content-Type: application/json; charset=UTF-8");


try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  $id = $_POST['id'] ?? '';
  $comment = $_POST['comment'] ?? '';

  if (!$id || !$comment) throw new Exception("IDとコメントが必要です");

  $stmt = $pdo->prepare("UPDATE gs_gakusyoku_table SET comment = :comment WHERE id = :id");
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
  $stmt->execute();

  echo json_encode(['status' => 'success']);
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}