<?php
header("Content-Type: application/json; charset=UTF-8");



try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  $stmt = $pdo->query("
    SELECT id, name, comment, menu_name, rating, image_path, indate
    FROM gs_gakusyoku_table
    ORDER BY indate DESC
    LIMIT 20
  ");

  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'status' => 'success',
    'data' => $results
  ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
  echo json_encode([
    'status' => 'error',
    'message' => $e->getMessage()
  ]);
}
exit();
