<?php
session_start();
echo $_SESSION['user_point']; 
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>本の検索＆ブックマーク登録</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { padding: 20px;  font-family: 'Noto Sans JP', sans-serif;}
    #results ul { list-style-type: none; padding: 0; }
    #results li { padding: 5px 0; cursor: pointer; }
    #results li:hover { background-color: #d1fae5; }
  </style>

</head>
<body>

<header class="flex justify-between items-center">
  <div>
    <a href="#home"><img src="./gakusyoku.jpg" alt="学食" class="h-16 mb-4 mx-4" /></a>
  </div>
  <?php session_start(); ?>
<nav class="flex justify-between items-center p-4 bg-white shadow">
  <div class="text-xl font-bold text-green-700">Gakushoku+</div>
  <div>
    <?php if (isset($_SESSION['user_name'])): ?>
      <span class="text-gray-600 mr-4">こんにちは、<?= htmlspecialchars($_SESSION['user_name']) ?>さん</span>
      <a href="logout.php" class="text-red-600 hover:underline">ログアウト</a>
    <?php else: ?>
      <a href="login.php" class="text-green-600 hover:underline mr-4">ログイン</a>
      <a href="register.php" class="text-green-600 hover:underline">会員登録</a>
    <?php endif; ?>
  </div>
</nav>

</header>

<hr class="border-green-400">

<main class="bg-green-50 min-h-screen">
  <div>
    <!-- タイトルとサブタイトル -->
    <nav class="text-center pt-16">
      <p class="text-4xl font-bold text-green-800">大学生のための学食レビュー＆ポイント還元サービス</p>
      <div class="py-10">
        <p class="text-xl text-green-600">学食の美味しいメニューをシェアして、ポイントを貯めよう！</p>
        <p class="text-xl text-green-600">あなたのレビューが他の学生の食体験を豊かにします。</p>
      </div>
      <div class="pb-16">
        <button class="bg-green-600 rounded text-white px-6 py-2 hover:bg-green-700 mx-2">レビューを投稿する</button>
        <button class="bg-green-50 border border-green-400 rounded text-green-600 px-6 py-2 hover:bg-white hover:text-black mx-2">人気メニューを見る</button>
      </div>
    </nav>

    <!-- タブメニュー -->
    <nav class="flex justify-center items-center mx-10">
      <ul class="flex w-full max-w-4xl justify-between">
        <li class="tab bg-green-200 hover:bg-green-300 rounded w-1/4 py-2 text-center m-2 cursor-pointer" data-tab="home">ホーム</li>
        <li class="tab bg-green-200 hover:bg-green-300 rounded w-1/4 py-2 text-center m-2 cursor-pointer" data-tab="ranking">ランキング</li>
        <li class="tab bg-green-200 hover:bg-green-300 rounded w-1/4 py-2 text-center m-2 cursor-pointer" data-tab="review">レビュー</li>
        <li class="tab bg-green-200 hover:bg-green-300 rounded w-1/4 py-2 text-center m-2 cursor-pointer" data-tab="points">ポイント</li>
      </ul>
    </nav>

    <!-- コンテンツ領域 -->
    <div id="home" class="tab-content px-6 py-4 bg-green-100 rounded mx-10 my-8">
      <div class="flex justify-center items-center">
        <input type="text" id="keyword" placeholder="メニュー名を検索" class="w-3/4 py-2 px-4 rounded" />
        <button id="search-btn" class="bg-green-600 rounded text-white px-6 py-2 hover:bg-green-700 mx-2">検索</button>
      </div>
      <div id="results" class="mt-6"></div>
    </div>

    <!-- 既存 -->
    <div id="ranking" class="tab-content hidden px-10 py-8">
    ランキングの内容が表示されます
    <!-- 例：ランキングタブの下に -->
    <div id="ai-recommend" class="mt-6"></div>

      <ul id="ranking-list" class="mt-4 space-y-2"></ul>
    </div>


<!-- レビュータブ内のフォーム -->
<div id="review" class="tab-content hidden px-10 py-8 bg-green-100 rounded mx-10 my-8">
  <h2 class="text-2xl font-bold mb-4">レビュー投稿フォーム</h2>
  <form id="review-form">
    <!-- メニュー選択 -->
    <div>
      <label for="menu_name" class="block font-semibold">メニュー名</label>
      <select name="menu_name" id="menu_name" class="w-full border rounded px-3 py-2" required>
        <option value="唐揚げ定食">唐揚げ定食</option>
        <option value="カレーライス">カレーライス</option>
        <option value="うどんセット">うどんセット</option>
        <option value="日替わりランチ">日替わりランチ</option>
      </select>
    </div>

    <!-- 評価 -->
    <div>
      <label for="rating" class="block font-semibold">評価（1〜5）</label>
      <select name="rating" id="rating" class="w-full border rounded px-3 py-2" required>
        <option value="5">★★★★★（5）</option>
        <option value="4">★★★★☆（4）</option>
        <option value="3">★★★☆☆（3）</option>
        <option value="2">★★☆☆☆（2）</option>
        <option value="1">★☆☆☆☆（1）</option>
      </select>
    </div>

    <!-- コメント -->
    <div>
      <label for="comment" class="block font-semibold">コメント</label>
      <textarea name="comment" id="comment" rows="4" class="w-full border rounded px-3 py-2" required></textarea>
    </div>

    <!-- 写真アップロード -->
    <div>
      <label for="image" class="block font-semibold">写真をアップロード</label>
      <input type="file" name="image" id="image" accept="image/*" class="w-full">
    </div>

    <!-- 送信 -->
    <div>
      <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">投稿する</button>
    </div>
  </form>

  <div id="review-message" class="mt-4 text-green-700"></div>

  <hr class="my-6">

  <h3 class="text-xl font-semibold mb-3">みんなのレビュー</h3>


  <div id="review-list" class="space-y-4">
    <!-- レビュー一覧がここに表示されます -->
  </div>
<div id="point" class="tab-content hidden">
  <h2 class="text-xl font-bold mb-4">ポイント履歴</h2>
  <ul id="point-list" class="space-y-2"></ul>
</div>
</main>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="./script.js"></script>
</body>
</html>
