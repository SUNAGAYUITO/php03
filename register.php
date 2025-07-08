<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();


try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
  exit("DB接続エラー: " . $e->getMessage());
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  if ($name && $email && $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
      $stmt = $pdo->prepare("INSERT INTO users (name, email, password, point) 
                             VALUES (:name, :email, :password, 0)");
      $stmt->bindValue(':name', $name, PDO::PARAM_STR);
      $stmt->bindValue(':email', $email, PDO::PARAM_STR);
      $stmt->bindValue(':password', $hash, PDO::PARAM_STR);
      $stmt->execute();
      $message = '登録が完了しました。ログインしてください。';
    } catch (PDOException $e) {
      $message = '登録エラー：' . $e->getMessage();
    }
  } else {
    $message = 'すべての項目を入力してください。';
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>会員登録</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 text-gray-800 p-8">
  <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-center text-green-700">会員登録</h1>

    <?php if ($message): ?>
      <p class="mb-4 text-center text-red-600 font-semibold"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label for="name" class="block font-semibold mb-1">ユーザー名</label>
        <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label for="email" class="block font-semibold mb-1">メールアドレス</label>
        <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label for="password" class="block font-semibold mb-1">パスワード</label>
        <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="text-center">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">登録する</button>
      </div>
    </form>
    <a href="./login.php">ログイン画面</a>
  </div>
</body>
</html>
