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
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  if ($email && $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      header('Location: index.php');
      exit();
    } else {
      $message = 'メールアドレスまたはパスワードが間違っています。';
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
  <title>ログイン</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 text-gray-800 p-8">
  <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-center text-green-700">ログイン</h1>

    <?php if ($message): ?>
      <p class="mb-4 text-center text-red-600 font-semibold"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label for="email" class="block font-semibold mb-1">メールアドレス</label>
        <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label for="password" class="block font-semibold mb-1">パスワード</label>
        <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="text-center">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">ログイン</button>
      </div>
    </form>
  </div>
</body>
</html>
