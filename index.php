<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>本の検索＆ブックマーク登録</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { padding: 20px; }
    #results ul { list-style-type: none; padding: 0; }
    #results li { padding: 5px 0; cursor: pointer; }
    #results li:hover { background-color: #d1fae5; }
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="./script.js"></script>
</head>
<body>

<header>
  <div>
    <a href="#home"><img src="./gakusyoku.jpg" alt="学食" class="h-16 mb-4 mx-4" /></a>
  </div>
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

    <div id="ranking" class="tab-content hidden px-10 py-8">📊 ランキングの内容が表示されます</div>


<div id="review" class="tab-content hidden px-10 py-8 bg-green-100 rounded mx-10 my-8">
  <h2 class="text-2xl font-bold mb-4">レビュー投稿フォーム</h2>
  <form id="review-form">
    <div class="mb-4">
      <label for="name" class="block mb-1">お名前</label>
      <input type="text" id="name" name="name" class="border rounded px-3 py-2 w-full" required>
    </div>
    <div class="mb-4">
      <label for="comment" class="block mb-1">コメント</label>
      <textarea id="comment" name="comment" class="border rounded px-3 py-2 w-full" rows="4" required></textarea>
    </div>
    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">投稿する</button>
  </form>
  <div id="review-message" class="mt-4 text-green-700"></div>

  <hr class="my-6">

  <h3 class="text-xl font-semibold mb-3">みんなのレビュー</h3>
  <div id="review-list" class="space-y-4">
    <!-- レビュー一覧がここに表示されます -->
  </div>
</div>



    <div id="points" class="tab-content hidden px-10 py-8">💰 ポイント履歴など</div>
  </div>
</main>

</body>
</html>
