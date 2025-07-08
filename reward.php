<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);



try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  $stmt = $pdo->query("SELECT * FROM rewards ORDER BY point_cost ASC");
  $rewards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  exit("DB接続エラー：" . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ポイント交換リスト</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-green-700 mb-6">ポイント交換リスト</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <?php foreach ($rewards as $reward): ?>
        <div class="bg-white shadow-md rounded-xl p-4">
          <img src="<?= htmlspecialchars($reward['image_path'], ENT_QUOTES) ?>" alt="<?= $reward['name'] ?>" class="w-full h-40 object-cover rounded mb-3">
          <h2 class="text-xl font-bold text-green-700"><?= htmlspecialchars($reward['name'], ENT_QUOTES) ?></h2>
          <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($reward['description'], ENT_QUOTES) ?></p>
          <p class="text-yellow-600 font-bold mb-3">必要ポイント：<?= $reward['point_cost'] ?>pt</p>
<button class="exchange-btn bg-green-500 hover:bg-green-600 text-white py-1 px-4 rounded" data-id="<?= $reward['id'] ?>">
  交換する
</button>

        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).on("click", ".exchange-btn", function () {
  const rewardId = $(this).data("id");

  if (!confirm("この特典と交換しますか？")) return;

  $.post("exchange_reward.php", { reward_id: rewardId }, function (res) {
    if (res.status === "success") {
      alert(res.message);
      location.reload(); // 交換後にリロード
    } else {
      alert("エラー：" + res.message);
    }
  }, "json");
});
</script>

</body>
</html>
