<?php
header("Content-Type: application/json; charset=UTF-8");



try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  $id = $_POST['id'] ?? '';
  if (!$id) throw new Exception("IDが必要です");

  $stmt = $pdo->prepare("DELETE FROM gs_gakusyoku_table WHERE id = :id");
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $stmt->execute();

  echo json_encode(['status' => 'success']);
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}